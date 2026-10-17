"""Membangun Dataset_Bersih_ID3.xlsx dari data mentah di folder data/."""
import os, statistics as st
from collections import Counter, OrderedDict
from openpyxl import Workbook
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter
from openpyxl.worksheet.table import Table, TableStyleInfo
from extract import man_legger, mas_legger, ptkin_2026, sim, norm
from raw_labels import TKA, TKA_KELAS, SPAN, AZHAR

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "data", "Dataset_Bersih_ID3.xlsx")
man, iss_man = man_legger(); mas, iss_mas = mas_legger(); ptk = ptkin_2026()
log = []  # (Kategori, Sekolah, Nama, Keterangan, Tindakan)

# ---------- aturan ----------
SAINTEK_MAPEL = {"Matematika Tingkat Lanjut","Fisika","Kimia","Biologi"}
SAINTEK_PRODI = {"PENDIDIKAN MATEMATIKA","TADRIS MATEMATIKA","PENDIDIKAN BIOLOGI","TADRIS BIOLOGI","ASTRONOMI ISLAM",
                 "TEKNIK INFORMATIKA","TEKNOLOGI INFORMASI","AGROEKOTEKNOLOGI","TEKNIK LINGKUNGAN","TEKNIK MESIN"}
def prodi_group(p): return "Saintek" if p.upper().strip() in SAINTEK_PRODI else "Soshum"
def tka_label(ch):
    s = sum(c in SAINTEK_MAPEL for c in ch); h = len(ch) - s
    return "Saintek" if h == 0 else ("Soshum" if s == 0 else "Campuran")
def kat(x):
    if x is None: return "Tidak Ada"
    return "Tinggi" if x >= 95 else ("Sedang" if x >= 92 else "Rendah")
GRP = {"BIO":"MIPA","KIM":"MIPA","FIS":"MIPA","MTL":"MIPA","SOS":"IPS","EKO":"IPS","GEO":"IPS",
       "ITaf":"Keagamaan","IHad":"Keagamaan","UFiq":"Keagamaan","ABAR":"Keagamaan","BIDTL":"Bahasa","BIGTL":"Bahasa","BARTL":"Bahasa","BKOR":"Bahasa"}
def peminatan(r):
    if r["Sekolah"].startswith("MAN"): return "Umum"
    g = {GRP[k] for k in r["nilai"] if k in GRP}
    if g == {"MIPA"}: return "MIPA"
    if g in ({"IPS","Bahasa"},{"IPS"}): return "IPS"
    if g == {"Keagamaan"}: return "Keagamaan"
    return "Lainnya"
def avg(r, keys):
    v = [r["nilai"][k] for k in keys if k in r["nilai"]]
    return round(sum(v)/len(v), 2) if v else None
def features(r):
    f = OrderedDict()
    f["Rata_Agama"] = avg(r, ["QH","AA","FIK","SKI"])
    f["Nilai_BArab"] = avg(r, ["BAR"])
    f["Rata_Bahasa"] = avg(r, ["BINDO","BING"])
    f["Nilai_MTK"] = avg(r, ["MTK"])
    f["Rata_MIPA"] = avg(r, ["BIO","KIM","FIS","MTL"])
    f["Rata_IPS"] = avg(r, ["EKO","SOS","GEO"])
    f["Rata_Umum"] = round(sum(r["nilai"].values())/len(r["nilai"]), 2) if r["nilai"] else None
    m, i = f["Rata_MIPA"], f["Rata_IPS"]
    if m is None or i is None: kc = "Tidak Terukur"
    elif m - i >= 1: kc = "MIPA"
    elif i - m >= 1: kc = "IPS"
    else: kc = "Seimbang"
    f["Kecenderungan"] = kc
    return f

# ---------- MAN: cocokkan formulir TKA ----------
labeled, matched_ids = [], set()
for f, n, nisn, ch, note in TKA:
    cands = [r for r in man if sim(n, r["Nama"]) >= 0.9]
    if nisn in TKA_KELAS: cands = [r for r in cands if r["Kelas"] == TKA_KELAS[nisn]]
    if len(cands) != 1:
        log.append(("Dikeluarkan","MAN 4 Aceh Utara",n,f"Formulir {f} (NISN {nisn}) cocok dengan {len(cands)} baris legger. {note}","Tidak dimasukkan ke data berlabel"))
        continue
    r = cands[0]; matched_ids.add(id(r))
    if note: log.append(("Catatan pembacaan","MAN 4 Aceh Utara",n,f"{f}: {note}","Dipakai dengan catatan"))
    if norm(n) != norm(r["Nama"]): log.append(("Beda ejaan","MAN 4 Aceh Utara",n,f"Formulir '{n}' vs legger '{r['Nama']}'","Dipakai ejaan legger"))
    acc = [p for p in ptk if norm(p["Nama"]) == norm(r["Nama"]) and p["Kelas"].endswith(r["Kelas"][-1])]
    r2 = dict(r, NISN=nisn, Label=tka_label(ch), Sumber_Label="Mapel pilihan TKA 2025",
              Detail_Label=" + ".join(ch), Jurusan_Diterima=(f"{acc[0]['Jurusan']} ({acc[0]['PT']})" if acc else ""), Bukti=f)
    labeled.append(r2)

# ---------- MAS: cocokkan poster SPAN-PTKIN ----------
OVR = {"Alief Al Farisi":"M. ALIEF AL FARISI","Hafizh Azzaki":"M. HAFIZH AZZAKI","Rifa Aqillah":"M. RIFA AQILLAH"}
seen = {}
for n, pt, p, f in SPAN:
    if n in OVR: cands = [r for r in mas if r["Nama"] == OVR[n]]
    else:
        cands = [r for r in mas if norm(r["Nama"]) == norm(n)] or [r for r in mas if sim(n, r["Nama"]) >= 0.9]
    if len(cands) != 1:
        log.append(("Dikeluarkan","MAS Al-Muslimun Lhoksukon",n,f"Poster {f}: {len(cands)} kandidat di legger","Tidak dimasukkan")); continue
    r = cands[0]
    if id(r) in seen:
        log.append(("Duplikat","MAS Al-Muslimun Lhoksukon",r["Nama"],f"Muncul dua kali di poster ({seen[id(r)]} dan {f}) dengan prodi sama","Disimpan satu baris")); continue
    seen[id(r)] = f; matched_ids.add(id(r))
    if norm(n) != norm(r["Nama"]): log.append(("Beda ejaan","MAS Al-Muslimun Lhoksukon",n,f"Poster '{n}' vs legger '{r['Nama']}'","Dipakai ejaan legger"))
    labeled.append(dict(r, Label=prodi_group(p), Sumber_Label="Prodi diterima SPAN-PTKIN 2026",
                        Detail_Label=f"{p} ({pt})", Jurusan_Diterima=f"{p} ({pt})", Bukti=f))
for n, f in AZHAR:
    r = max(mas, key=lambda x: sim(n, x["Nama"]))
    if id(r) in seen:
        log.append(("Catatan","MAS Al-Muslimun Lhoksukon",r["Nama"],f"Juga lulus Al-Azhar Kairo ({f})","Label tetap dari prodi SPAN-PTKIN"))
    else:
        log.append(("Tanpa label","MAS Al-Muslimun Lhoksukon",r["Nama"],f"Lulus Al-Azhar Kairo ({f}), prodi tidak disebut","Masuk sheet Data_Tanpa_Label"))

unlabeled = [r for r in man + mas if id(r) not in matched_ids]
for r in unlabeled:
    if r["Sekolah"].startswith("MAN"):
        log.append(("Tanpa label","MAN 4 Aceh Utara",r["Nama"],f"Ada di legger {r['Kelas']} tetapi formulir TKA tidak ada/tidak bisa dicocokkan","Masuk sheet Data_Tanpa_Label"))
for msg in iss_man + iss_mas: log.append(("Validasi jumlah","",msg,"",""))

# ---------- urutkan dan beri ID ----------
def sortkey(r): return (r["Sekolah"], r["Kelas"], r["No"])
labeled.sort(key=sortkey); unlabeled.sort(key=sortkey)
def sid(r): return ("MAN" if r["Sekolah"].startswith("MAN") else "MAS") + "-" + r["Kelas"].replace("XII.","K") + "-" + f"{r['No']:02d}"

# ---------- workbook ----------
wb = Workbook()
HDR = PatternFill("solid", fgColor="1F4E78"); HDR2 = PatternFill("solid", fgColor="7F7F7F"); LBL = PatternFill("solid", fgColor="C65911")
thin = Side(style="thin", color="BFBFBF"); BOR = Border(left=thin,right=thin,top=thin,bottom=thin)
def write(ws, headers, rows, fills=None, widths=None, table=None):
    ws.append(headers)
    for i, h in enumerate(headers, 1):
        c = ws.cell(row=1, column=i); c.font = Font(bold=True, color="FFFFFF")
        c.fill = (fills or {}).get(h, HDR); c.alignment = Alignment(horizontal="center", vertical="center", wrap_text=True); c.border = BOR
    for row in rows:
        ws.append(row)
    for r in ws.iter_rows(min_row=2, max_row=ws.max_row):
        for c in r: c.border = BOR; c.alignment = Alignment(vertical="top", wrap_text=isinstance(c.value, str) and len(c.value) > 40)
    for i, h in enumerate(headers, 1):
        w = (widths or {}).get(h) or min(max(len(str(h)), *(len(str(x[i-1])) if x[i-1] is not None else 0 for x in rows) if rows else [0]) + 2, 60)
        ws.column_dimensions[get_column_letter(i)].width = w
    ws.freeze_panes = "A2"; ws.row_dimensions[1].height = 32
    if table and rows:
        t = Table(displayName=table, ref=f"A1:{get_column_letter(len(headers))}{len(rows)+1}")
        t.tableStyleInfo = TableStyleInfo(name="TableStyleLight9", showRowStripes=True); ws.add_table(t)

ID_COLS = ["ID_Siswa","Sekolah","Kelas","Nama"]
ATR = ["Sekolah_Asal","Peminatan","Kat_Agama","Kat_BArab","Kat_Bahasa","Kat_MTK","Kat_MIPA","Kat_IPS","Kecenderungan"]
grey = {h: HDR2 for h in ID_COLS}; grey["Minat_Jurusan"] = LBL
def id3_row(r):
    f = features(r)
    return [sid(r), r["Sekolah"], r["Kelas"], r["Nama"],
            "MAN" if r["Sekolah"].startswith("MAN") else "MAS", peminatan(r),
            kat(f["Rata_Agama"]), kat(f["Nilai_BArab"]), kat(f["Rata_Bahasa"]), kat(f["Nilai_MTK"]),
            kat(f["Rata_MIPA"]), kat(f["Rata_IPS"]), f["Kecenderungan"]]

ws = wb.active; ws.title = "Data_ID3"
write(ws, ID_COLS + ATR + ["Minat_Jurusan"], [id3_row(r) + [r["Label"]] for r in labeled], grey, table="DataID3")

SUBJ = ["QH","AA","FIK","SKI","BAR","PP","BINDO","MTK","BING","PJOK","SEJ","SB","ITaf","IHad","UFiq","ABAR","BIO","KIM","FIS","INFOP","MTL","SOS","EKO","GEO","BIDTL","BIGTL","BARTL","BKOR","VKET"]
FEAT = ["Rata_Agama","Nilai_BArab","Rata_Bahasa","Nilai_MTK","Rata_MIPA","Rata_IPS","Rata_Umum","Kecenderungan"]
def num_row(r, lab=True):
    f = features(r)
    base = [sid(r), r["Sekolah"], r["Kelas"], r["Nama"], r["NIS"] or None, r["NISN"] or None, r["JK"] or None, peminatan(r)]
    base += [f[k] for k in FEAT] + [r["nilai"].get(s) for s in SUBJ] + [r["Total_Legger"]]
    if lab: base += [r["Label"], r["Sumber_Label"], r["Detail_Label"], r["Jurusan_Diterima"] or None, r["Bukti"]]
    return base + [r["File"]]
NUMH = ID_COLS + ["NIS","NISN","JK","Peminatan"] + FEAT + SUBJ + ["Total_Legger"]
ws = wb.create_sheet("Data_Numerik")
write(ws, NUMH + ["Minat_Jurusan","Sumber_Label","Detail_Label","Jurusan_Diterima","Bukti_Label","File_Nilai"],
      [num_row(r) for r in labeled], dict(grey), widths={s: 7 for s in SUBJ}, table="DataNumerik")

ws = wb.create_sheet("Data_Tanpa_Label")
write(ws, ID_COLS + ATR + ["Catatan"], [id3_row(r) + ["Belum ada label minat/prodi. Bisa dipakai sebagai data prediksi."] for r in unlabeled], grey, table="TanpaLabel")

# Kamus data
ws = wb.create_sheet("Kamus_Data")
K = [
 ("ID_Siswa","Identitas","Kode unik: sekolah-kelas-nomor absen legger","Bukan atribut ID3"),
 ("Sekolah, Kelas, Nama","Identitas","Dari legger nilai","Bukan atribut ID3"),
 ("Sekolah_Asal","Atribut","MAN = MAN 4 Aceh Utara (Matangkuli); MAS = MAS Al-Muslimun Lhoksukon","MAN, MAS"),
 ("Peminatan","Atribut","MAN: semua siswa mengambil paket mapel sama (Umum). MAS: dari kolom mapel pilihan yang terisi di legger (KMPM=MIPA, KMPS+Bahasa=IPS, KMPA=Keagamaan)","Umum, MIPA, IPS, Keagamaan, Lainnya"),
 ("Kat_Agama","Atribut","Rata-rata QH, AA, FIK, SKI","Tinggi >= 95; Sedang 92 sampai < 95; Rendah < 92"),
 ("Kat_BArab","Atribut","Nilai Bahasa Arab (BAR)","Sama"),
 ("Kat_Bahasa","Atribut","Rata-rata B. Indonesia dan B. Inggris","Sama"),
 ("Kat_MTK","Atribut","Nilai Matematika umum","Sama"),
 ("Kat_MIPA","Atribut","Rata-rata BIO, KIM, FIS, MTL yang tersedia","Sama; Tidak Ada bila siswa tidak mengambil mapel ini"),
 ("Kat_IPS","Atribut","Rata-rata mapel pilihan IPS yang tersedia: EKO, SOS, GEO (Sejarah wajib, tidak dihitung)","Sama"),
 ("Kecenderungan","Atribut","Selisih Rata_MIPA dan Rata_IPS. Selisih >= 1 poin menentukan arah","MIPA, IPS, Seimbang, Tidak Terukur"),
 ("Minat_Jurusan","LABEL (kelas target)","MAN: rumpun 2 mapel pilihan TKA. MAS: rumpun prodi yang diterima SPAN-PTKIN 2026","Saintek, Soshum, Campuran (MAN saja)"),
 ("Batas kategori","Aturan","Batas 92 dan 94/95 diambil dari persentil 33 dan 67 seluruh nilai mapel MAN 4 (92 dan 94)","Ubah fungsi kat() di scripts/build_dataset.py bila perlu"),
 ("Sheet Data_Numerik","Info","Nilai asli per mapel dan rata-rata sebelum dikategorikan. Pakai untuk lampiran atau ID3 dengan diskretisasi lain",""),
 ("Sheet Data_Tanpa_Label","Info","Siswa dengan nilai lengkap tetapi tanpa label. Cocok untuk data uji prediksi sistem",""),
 ("Singkatan mapel","Info","QH Quran Hadis; AA Akidah Akhlak; FIK Fikih; SKI Sejarah Kebudayaan Islam; BAR B. Arab; PP Pendidikan Pancasila; BINDO B. Indonesia; MTK Matematika; BING B. Inggris; PJOK Penjas; SEJ Sejarah; SB Seni Budaya; ITaf Ilmu Tafsir; IHad Ilmu Hadis; UFiq Ushul Fikih; ABAR Bahasa Arab (peminatan); BIO Biologi; KIM Kimia; FIS Fisika; INFOP Informatika; MTL Matematika Tingkat Lanjut; SOS Sosiologi; EKO Ekonomi; GEO Geografi; BIDTL/BIGTL/BARTL B. Indonesia/Inggris/Arab Tingkat Lanjut; BKOR B. Korea; VKET Prakarya/Kewirausahaan",""),
]
write(ws, ["Kolom","Peran","Definisi / Sumber","Nilai yang mungkin"], [list(x) for x in K], widths={"Kolom":24,"Peran":20,"Definisi / Sumber":80,"Nilai yang mungkin":50})

ws = wb.create_sheet("Mapping_Label")
rows = [["Mapel TKA", m, "Saintek"] for m in sorted(SAINTEK_MAPEL)]
rows += [["Mapel TKA", m, "Soshum"] for m in ["Ekonomi","Sosiologi","Geografi","Sejarah","Antropologi","PPKn","Bahasa Indonesia/Inggris Tingkat Lanjut","Bahasa Arab/Asing","Produk Kreatif dan Kewirausahaan"]]
rows += [["Aturan MAN","2 mapel Saintek = Saintek; 2 mapel Soshum = Soshum; 1 dan 1 = Campuran",""]]
prodis = sorted({(p, prodi_group(p)) for _, _, p, _ in SPAN} | {(x["Jurusan"].title(), prodi_group(x["Jurusan"])) for x in ptk})
rows += [["Prodi", p, g] for p, g in prodis]
write(ws, ["Jenis","Mapel / Prodi","Rumpun"], rows, widths={"Jenis":14,"Mapel / Prodi":70,"Rumpun":12})

ws = wb.create_sheet("Log_Pembersihan")
log.insert(0, ("Validasi jumlah","Semua",f"{len(man)} siswa MAN, {len(mas)} siswa MAS","Jumlah nilai per siswa sama dengan kolom TOTAL/Jumlah legger" if not (iss_man or iss_mas) else "Ada selisih, lihat baris di bawah",""))
log.insert(1, ("Kualitas data","MAS Al-Muslimun Lhoksukon","Semua siswa","Nilai legger MAS hampir seragam (hanya 97, 98, 99). Atribut nilai tidak membedakan siswa MAS; pembeda utama adalah Peminatan.","Dipertahankan, perlu disebut di Bab III/IV"))
log.insert(2, ("Kualitas data","MAS Al-Muslimun Lhoksukon","Label","Semua label MAS dari kampus PTKIN (Islam). Mayoritas prodi rumpun Soshum, kelas tidak seimbang.","Perlu disebut sebagai keterbatasan"))
log.insert(3, ("Data tidak dipakai","-","Lembar 2021 sampai 2025 (file PTKIN)","Alumni tahun lalu tanpa nilai rapor di folder data","Tidak dipakai sebagai data latih"))
log.insert(4, ("Data tidak dipakai","MAN 4 Aceh Utara","Legger XII.x PDF vs Excel","Excel 'LEGGER NILAI KELAS XII.1-8' ternyata milik MAS Al-Muslimun (lihat sel B3). Nilai MAN 4 diambil dari 3 file PDF.","Info"))
write(ws, ["Kategori","Sekolah","Nama / Objek","Keterangan","Tindakan"], [list(x) for x in log], widths={"Kategori":18,"Sekolah":26,"Nama / Objek":30,"Keterangan":80,"Tindakan":36})

ws = wb.create_sheet("Ringkasan", 0)
c = Counter((r["Sekolah"], r["Label"]) for r in labeled)
rows = [[s, l, n] for (s, l), n in sorted(c.items())]
rows += [["Total berlabel", "", len(labeled)], ["Tanpa label", "", len(unlabeled)], ["Total siswa di legger", "", len(man)+len(mas)]]
write(ws, ["Sekolah","Minat_Jurusan","Jumlah"], rows, widths={"Sekolah":30,"Minat_Jurusan":18,"Jumlah":10})
ws["E1"] = "Cara pakai"; ws["E1"].font = Font(bold=True)
notes = ["1. Sheet Data_ID3 adalah data latih siap olah. Kolom abu-abu = identitas, kolom biru = atribut, kolom oranye = label.",
         "2. Untuk ID3: gunakan kolom Sekolah_Asal sampai Kecenderungan sebagai atribut, Minat_Jurusan sebagai kelas.",
         "3. Bila dosen meminta 2 kelas saja, buang baris Campuran atau gabungkan sesuai keputusan pembimbing.",
         "4. Sheet Log_Pembersihan mencatat semua keputusan pembersihan data.",
         "5. Dibangun ulang dengan: python scripts/build_dataset.py"]
for i, n in enumerate(notes, 2): ws[f"E{i}"] = n
ws.column_dimensions["E"].width = 110
wb.save(OUT)
print("saved", OUT, len(labeled), len(unlabeled), c)
