from extract import *
from raw_labels import *
man,_=man_legger(); mas,_=mas_legger()
print("== TKA -> MAN legger")
for f,n,nisn,ch,note in TKA:
    c=sorted(man,key=lambda r:-sim(n,r["Nama"]))[:3]
    print(f"{n:28s} -> {c[0]['Nama']:28s} {c[0]['Kelas']} {sim(n,c[0]['Nama']):.2f} | 2nd {c[1]['Nama']} {sim(n,c[1]['Nama']):.2f}")
print("== SPAN -> MAS legger")
for n,pt,p,f in SPAN:
    c=sorted(mas,key=lambda r:-sim(n,r["Nama"]))[:2]
    flag = "" if sim(n,c[0]['Nama'])>=0.9 else "  <<<"
    print(f"{n:32s} -> {c[0]['Nama']:34s} {c[0]['Kelas']} {sim(n,c[0]['Nama']):.2f}{flag}")
for n,f in AZHAR:
    c=sorted(mas,key=lambda r:-sim(n,r["Nama"]))[:2]; print("AZHAR",n,"->",c[0]["Nama"],sim(n,c[0]["Nama"]))
from collections import Counter
G={"BIO":"MIPA","KIM":"MIPA","FIS":"MIPA","MTL":"MIPA","SOS":"IPS","EKO":"IPS","GEO":"IPS","ITaf":"Keagamaan","IHad":"Keagamaan","UFiq":"Keagamaan","ABAR":"Keagamaan","BIDTL":"Bahasa","BIGTL":"Bahasa","BARTL":"Bahasa","BKOR":"Bahasa"}
print(Counter(tuple(sorted({G[k] for k in r["nilai"] if k in G})) for r in mas))
print(Counter(tuple(sorted(k for k in r["nilai"] if k in G)) for r in mas))
