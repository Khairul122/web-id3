"""Verifikasi independen hasil ID3 SIMINAT terhadap metode di proposal (BAB II 2.5).

Skrip ini TIDAK memakai kode PHP maupun database untuk menghitung. Data dibaca langsung dari
Dataset_Bersih_ID3.xlsx (pustaka standar Python), lalu ID3 dihitung ulang dengan rumus proposal:
  (1) Entropy(S) = -sum p_i log2 p_i
  (2-4) Gain(S,A) = Entropy(S) - sum |Sv|/|S| Entropy(Sv)
  berhenti bila: a) kelas sama, b) atribut habis, c) data habis (daun = mayoritas induk)
  (7) prediksi = argmax P(c|daun)
Hasilnya dibandingkan dengan keluaran sistem (php tests/export_id3.php). Jalankan: python tests/verify_id3_dataset.py
"""
import json
import math
import re
import subprocess
import sys
import zipfile
import xml.etree.ElementTree as ET
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
NS = {'m': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
CLASSES = ['Saintek', 'Soshum', 'Campuran']
TOL = 1e-6


def read_sheet(path, name):
    with zipfile.ZipFile(path) as z:
        shared = []
        if 'xl/sharedStrings.xml' in z.namelist():
            shared = [''.join(t.text or '' for t in si.iter('{%s}t' % NS['m'])) for si in ET.fromstring(z.read('xl/sharedStrings.xml'))]
        rels = {r.get('Id'): r.get('Target') for r in ET.fromstring(z.read('xl/_rels/workbook.xml.rels'))}
        for sheet in ET.fromstring(z.read('xl/workbook.xml')).find('m:sheets', NS):
            if sheet.get('name') != name:
                continue
            target = rels[sheet.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id')]
            data = ET.fromstring(z.read(target.lstrip('/') if target.startswith('/') else 'xl/' + target))
            grid = []
            for row in data.iter('{%s}row' % NS['m']):
                cells = {}
                for c in row.findall('m:c', NS):
                    col = re.sub(r'\d+', '', c.get('r'))
                    if c.get('t') == 's':
                        value = shared[int(c.find('m:v', NS).text)]
                    elif c.get('t') == 'inlineStr':
                        value = ''.join(t.text or '' for t in c.iter('{%s}t' % NS['m']))
                    else:
                        v = c.find('m:v', NS)
                        value = v.text if v is not None else ''
                    cells[col] = (value or '').strip()
                grid.append(cells)
            header = grid[0]
            return [{header[k]: cells.get(k, '') for k in header} for cells in grid[1:]]
    raise KeyError(name)


def entropy(counts):
    total = sum(counts.values())
    return -sum(c / total * math.log2(c / total) for c in counts.values() if c > 0)


def dist(rows):
    d = {c: 0 for c in CLASSES}
    for r in rows:
        d[r['y']] += 1
    return d


def majority(d):
    best = next(iter(d))
    for c, n in d.items():
        if n > d[best]:
            best = c
    return best


def build(rows, attrs, domains, steps, path=()):
    d = dist(rows)
    e = entropy(d)
    node = {'jumlah': len(rows), 'distribusi': d, 'entropy': e}
    if e == 0 or not attrs:                      # kriteria a dan b
        return dict(node, kelas=majority(d))
    gains = []
    for a in attrs:
        remainder = sum(len(sv) / len(rows) * entropy(dist(sv)) for v in domains[a] if (sv := [r for r in rows if r['x'][a] == v]))
        gains.append((a, e - remainder))
    best = max(gains, key=lambda g: (g[1], -attrs.index(g[0])))[0]   # seri: urutan atribut terkecil
    steps.append({'path': ' > '.join(f'{a} = {v}' for a, v in path) or 'Akar', 'entropy': e, 'gains': dict(gains), 'terpilih': best})
    node['atribut'] = best
    node['cabang'] = {}
    rest = [a for a in attrs if a != best]
    for v in domains[best]:
        sv = [r for r in rows if r['x'][best] == v]
        node['cabang'][v] = build(sv, rest, domains, steps, path + ((best, v),)) if sv else {'kelas': majority(d), 'kosong': True}  # kriteria c
    return node


def predict(tree, x):
    node = tree
    while 'atribut' in node:
        child = node['cabang'].get(x.get(node['atribut']))
        if child is None:
            return majority(node['distribusi'])
        node = child
    return node['kelas']


def compare(mine, system, where, problems):
    if 'atribut' in mine or 'atribut' in system:
        if mine.get('atribut') != system.get('atribut'):
            problems.append(f'{where}: atribut {mine.get("atribut")} vs sistem {system.get("atribut")}')
            return
        for v in mine['cabang']:
            compare(mine['cabang'][v], system['cabang'][v], f'{where} > {mine["atribut"]}={v}', problems)
    elif mine['kelas'] != system['kelas']:
        problems.append(f'{where}: daun {mine["kelas"]} vs sistem {system["kelas"]}')
    if not mine.get('kosong') and abs(mine['entropy'] - float(system['entropy'])) > TOL:
        problems.append(f'{where}: entropy {mine["entropy"]:.6f} vs sistem {system["entropy"]}')


def main():
    system = json.loads(subprocess.run(['php', str(ROOT / 'tests/export_id3.php')], capture_output=True, text=True, check=True).stdout)
    attrs, domains, k = system['atribut'], system['domains'], system['k']
    labeled = read_sheet(ROOT / 'data' / 'Dataset_Bersih_ID3.xlsx', 'Data_ID3')
    data = {r['ID_Siswa']: {'x': {a: r[a] for a in attrs}, 'y': r['Minat_Jurusan'], 'sekolah': r['Sekolah_Asal']} for r in labeled}
    scenario_filter = {
        'semua': lambda r: True,
        'man': lambda r: r['sekolah'] == 'MAN',
        'mas': lambda r: r['sekolah'] == 'MAS',
        'tanpa_campuran': lambda r: r['y'] != 'Campuran',
    }
    failed = False
    report = {}
    for scenario, out in system['skenario'].items():
        problems = []
        ids = sorted(i for i, r in data.items() if scenario_filter[scenario](r))
        if ids != sorted(out['kode_siswa']):
            problems.append('himpunan data latih berbeda dengan Excel')
        rows = [data[i] for i in ids]
        steps = []
        tree = build(rows, attrs, domains, steps)
        compare(tree, out['tree'], 'Akar', problems)
        if len(steps) != len(out['steps']):
            problems.append(f'jumlah langkah {len(steps)} vs sistem {len(out["steps"])}')
        for mine, sys_step in zip(steps, out['steps']):
            if mine['terpilih'] != sys_step['atribut_terpilih'] or abs(mine['entropy'] - float(sys_step['entropy_parent'])) > TOL:
                problems.append(f'langkah {mine["path"]}: berbeda')
            for g in sys_step['detail_gain']:
                if abs(mine['gains'][g['atribut']] - float(g['gain'])) > TOL:
                    problems.append(f'langkah {mine["path"]}: gain {g["atribut"]} {mine["gains"][g["atribut"]]:.6f} vs {g["gain"]}')
        acc = sum(predict(tree, r['x']) == r['y'] for r in rows) / len(rows)
        if abs(acc - out['akurasi_latih']) > TOL:
            problems.append(f'akurasi latih {acc:.4f} vs sistem {out["akurasi_latih"]:.4f}')

        # K-fold: pembagian fold diambil dari sistem, pelatihan dan prediksi dihitung ulang di sini.
        fold_ids = out['folds']
        if sorted(i for f in fold_ids for i in f) != ids:
            problems.append('fold tidak mencakup setiap data tepat satu kali')
        for c, n in Counter(data[i]['y'] for i in ids).items():
            per_fold = [sum(data[i]['y'] == c for i in f) for f in fold_ids]
            if max(per_fold) - min(per_fold) > 1:
                problems.append(f'fold tidak terstratifikasi untuk kelas {c}: {per_fold}')
        matrix = {a: {p: 0 for p in CLASSES} for a in CLASSES}
        for f in fold_ids:
            test = set(f)
            fold_tree = build([data[i] for i in ids if i not in test], attrs, domains, [])
            for i in f:
                matrix[data[i]['y']][predict(fold_tree, data[i]['x'])] += 1
        if matrix != out['confusion']:
            problems.append(f'confusion matrix berbeda: {matrix} vs {out["confusion"]}')
        correct = sum(matrix[c][c] for c in CLASSES)
        report[scenario] = {'data': len(rows), 'akar': tree.get('atribut'), 'langkah': len(steps), 'akurasi_latih': round(acc, 4), 'akurasi_kfold': round(correct / len(rows), 4), 'masalah': problems}
        failed = failed or bool(problems)
        print(f'{"LULUS" if not problems else "GAGAL"}  {scenario:<15} data={len(rows):<4} akar={tree.get("atribut"):<14} langkah={len(steps):<3} latih={acc:.4f} kfold={correct / len(rows):.4f}')
        for p in problems[:10]:
            print('      -', p)
    (ROOT / 'output').mkdir(exist_ok=True)
    (ROOT / 'output' / 'verifikasi_id3.json').write_text(json.dumps(report, indent=2, ensure_ascii=False), encoding='utf-8')
    sys.exit(1 if failed else 0)


if __name__ == '__main__':
    main()
