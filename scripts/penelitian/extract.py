import subprocess, re, glob, os, openpyxl, unicodedata
from difflib import SequenceMatcher
BASE = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "..", "data", "sumber")
MAN_COLS = ["QH","AA","FIK","SKI","BAR","PP","BINDO","MTK","BING","PJOK","SEJ","SB","BIO","KIM","FIS","MTL","EKO","BIDTL"]

def norm(s):
    s = unicodedata.normalize("NFKD", s).upper()
    s = s.replace("T.M.", "TEUKU MUHAMMAD ").replace("M.", "MUHAMMAD ")
    s = re.sub(r"\bM\b", "MUHAMMAD", s)
    s = re.sub(r"[^A-Z ]", " ", s)
    return re.sub(r"\s+", " ", s).strip()

def sim(a, b):
    a, b = norm(a), norm(b)
    return max(SequenceMatcher(None, a, b).ratio(), SequenceMatcher(None, a.replace(" ",""), b.replace(" ","")).ratio())

def man_legger():
    rows, issues = [], []
    for f in sorted(glob.glob(os.path.join(BASE, "man", "legger", "Legger*.pdf"))):
        txt = subprocess.run(["pdftotext","-layout",f,"-"],capture_output=True,text=True).stdout
        kelas = re.search(r"Kelas\s*:\s*(XII\.\d)", txt).group(1)
        for line in txt.splitlines():
            m = re.match(r"^\s*(\d+)\s+([A-Z][A-Z .']+?)\s{2,}((?:\d+\s+){18}\d+)\s*$", line)
            if not m: continue
            nums = list(map(int, m.group(3).split()))
            vals, total = nums[:18], nums[18]
            if sum(vals) != total:
                issues.append(f"MAN {kelas} {m.group(2)}: jumlah nilai {sum(vals)} != TOTAL {total}")
            rows.append(dict(Sekolah="MAN 4 Aceh Utara", Kelas=kelas, No=int(m.group(1)), Nama=m.group(2).strip(),
                             NIS="", NISN="", JK="", nilai=dict(zip(MAN_COLS, vals)), Total_Legger=total, File=os.path.basename(f)))
    return rows, issues

def mas_legger():
    rows, issues = [], []
    for f in sorted(glob.glob(os.path.join(BASE, "mas", "legger", "LEGGER NILAI KELAS*.xlsx"))):
        ws = openpyxl.load_workbook(f, data_only=True).worksheets[0]
        h1 = [c.value for c in ws[6]]; h2 = [c.value for c in ws[7]]
        cols = []
        for i,(a,b) in enumerate(zip(h1,h2)):
            cols.append(b if b else a)
        kelas = ws["B2"].value
        for r in ws.iter_rows(min_row=8, values_only=True):
            if not r[3]: continue
            d = dict(zip(cols, r))
            nilai = {}
            for c in cols[5:-1]:
                v = d.get(c)
                if v not in (None, ""): nilai[c] = int(v)
            tot = d.get("Jumlah")
            if tot is not None and sum(nilai.values()) != int(tot):
                issues.append(f"MAS {kelas} {r[3]}: jumlah nilai {sum(nilai.values())} != Jumlah {tot}")
            rows.append(dict(Sekolah="MAS Al-Muslimun Lhoksukon", Kelas=kelas, No=int(r[0]), Nama=str(r[3]).strip().upper(),
                             NIS=str(r[1]), NISN=str(r[2]), JK=r[4], nilai=nilai, Total_Legger=tot, File=os.path.basename(f)))
    return rows, issues

def ptkin_2026():
    f = os.path.join(BASE, "man", "SISWA MAN 4 ACEH UTARA YANG LOLOS PTKIN-SPAN PTKIN.xlsx")
    ws = openpyxl.load_workbook(f, data_only=True)["2026"]
    out = []
    for r in ws.iter_rows(min_row=7, values_only=True):
        if r[1] and r[6]:
            out.append(dict(Nama=r[1], Kelas=r[2], Jurusan=r[6], PT=r[7]))
    return out

if __name__ == "__main__":
    a, ia = man_legger(); b, ib = mas_legger()
    print(len(a), len(b), ia, ib)
    import statistics as st
    for c in ["MTK","BIO","KIM","FIS","MTL","EKO","BINDO","BING","QH","SEJ"]:
        v=[r["nilai"][c] for r in a]; print(c, min(v), st.median(v), max(v))
    allv = sorted(v for r in a for v in r["nilai"].values())
    print("q33,q67", allv[len(allv)//3], allv[2*len(allv)//3])
    print(ptkin_2026())
    vals=set(v for r in b for v in r["nilai"].values()); print("MAS distinct", vals)
