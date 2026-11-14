#!/usr/bin/env python3
"""
Testy strażnika płatności P24 (inc/payment-guard.php) na lokalnej kopii sklepu.

Wymusza awarie atrapą P24 (scripts/local-env/mu-plugins/moretti-local-p24-mock.php) i sprawdza reakcję sklepu:
płatność bez powiadomienia, porzucony BLIK, płatność po anulowaniu, przelew ITP, kontrola przed anulowaniem,
klient z nowym zamówieniem, podwójna płatność, przekaz tradycyjny, rozbieżność kwot, link z przypomnienia.
Maile lądują w wp-content/uploads/local-mails/ (moretti-local-mail-catcher.php).

Uruchomienie (sklep lokalny musi działać: docker compose up -d):
    python3 scripts/local-env/tests/payment_guard_scenarios.py
Puszczać po każdej aktualizacji wtyczki Przelewy24 albo WooCommerce. Tworzy lokalne zamówienia testowe.
"""
import requests, re, json, subprocess, sys, time, glob, os
import random
B="http://localhost:8080"; S=f"{B}/wp-json/wc/store/v1"
ROOT=os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", ".."))
def wp(php):
    r=subprocess.run(["docker","compose","exec","-T","cli","wp","eval",php],capture_output=True,text=True,cwd=ROOT)
    return "\n".join(l for l in (r.stdout+r.stderr).splitlines() if "Notice" not in l and "Deprecated" not in l and not l.startswith("Warning:")).strip()
def mails():
    out=subprocess.run(["docker","compose","exec","-T","cli","bash","-c","ls wp-content/uploads/local-mails/ 2>/dev/null"],capture_output=True,text=True,cwd=ROOT).stdout.split()
    return set(out)
def mail_subjects(names):
    res=[]
    for n in sorted(names):
        h=subprocess.run(["docker","compose","exec","-T","cli","bash","-c",f"head -2 'wp-content/uploads/local-mails/{n}'"],capture_output=True,text=True,cwd=ROOT).stdout
        to=re.search(r"To: (.*?) -->",h); sub=re.search(r"Subject: (.*?) -->",h)
        res.append(f"{to.group(1) if to else '?'} | {sub.group(1) if sub else '?'}")
    return res
def place(email, product=58, gateway="p24-online-payments-154", method=None, sim="cancel"):
    s=requests.Session(); s.headers["User-Agent"]="Mozilla/5.0 (guard-test)"
    r=s.get(f"{S}/cart",timeout=30); hdr={"Cart-Token":r.headers["Cart-Token"],"Nonce":r.headers["Nonce"]}
    def h(x):
        for k in ("Cart-Token","Nonce"):
            if x.headers.get(k): hdr[k]=x.headers[k]
    h(s.post(f"{S}/cart/add-item",json={"id":product,"quantity":1},headers=hdr,timeout=30))
    addr={"first_name":"Anna","last_name":"Test","address_1":"Testowa 1","city":"Warszawa","postcode":"00-001","country":"PL","phone":"500100200"}
    pd=[{"key":"regulation","value":"1"}]+([{"key":"method","value":str(method)}] if method else [])
    r=s.post(f"{S}/checkout",json={"billing_address":{**addr,"email":email},"shipping_address":addr,"payment_method":gateway,"payment_data":pd,"extensions":{"moretti":{"terms_accepted":True}}},headers=hdr,timeout=60)
    d=r.json(); assert r.status_code<400, d
    url=d["payment_result"]["redirect_url"]; p=s.get(url,timeout=30)
    nonce=re.search(r'name="_wpnonce" value="([^"]+)"',p.text).group(1)
    s.post(url,data={"_wpnonce":nonce,"moretti_p24_action":sim},timeout=30)
    return d["order_id"]
def backdate(oid, minutes):
    return wp(f'''global $wpdb; $t=gmdate("Y-m-d H:i:s", time()-{minutes}*60); $l=get_date_from_gmt($t);
    $wpdb->update($wpdb->posts, ["post_date"=>$l,"post_date_gmt"=>$t,"post_modified"=>$l,"post_modified_gmt"=>$t], ["ID"=>{oid}]);
    clean_post_cache({oid}); wp_cache_flush(); echo "ok";''')
def state(oid):
    return wp(f'''wp_cache_flush(); $o=wc_get_order({oid}); echo $o->get_status(), " | reminded=", $o->get_meta("_moretti_guard_reminded") ?: "-", " | auto_cancelled=", $o->get_meta("_moretti_guard_auto_cancelled") ? "yes":"-", " | alerts=", implode(",", array_map(fn($m)=>substr($m->key,22), array_filter($o->get_meta_data(), fn($m)=>str_starts_with($m->key,"_moretti_guard_alerted_"))));''')
def notes(oid):
    return wp(f'''foreach(array_reverse(wc_get_order_notes(["order_id"=>{oid}])) as $n){{ if(str_contains($n->content,"Strażnik")||str_contains($n->content,"Anulowane")||str_contains($n->content,"zweryfikowana")) echo "   · ", wp_strip_all_tags($n->content), "\\n"; }}''')
def sweep(): return wp('wp_cache_flush(); do_action(MORETTI_GUARD_SWEEP_HOOK); echo "sweep done";')
def cancel_run(): return wp('wp_cache_flush(); wc_cancel_unpaid_orders(); echo "cancel run done";')
def mark_paid(oid): return wp(f'moretti_p24_sim_mark_paid(wc_get_order({oid})->get_meta("_p24_session_id")); echo "marked";')
def check(label, cond):
    print(("  ✅ " if cond else "  ❌ ")+label); RESULTS.append((label,cond))
RESULTS=[]
RUN=str(random.randint(1000,9999))
_place=place
def place(email,*a,**k): return _place(email.replace("@","+"+RUN+"@"),*a,**k)
# porządek: wcześniejsze lokalne zamówienia testowe nie mogą zakłócać przeglądu
print(wp('foreach(wc_get_orders(["limit"=>-1,"status"=>["pending"]]) as $o){ $o->update_status("cancelled","porządek przed testem strażnika"); } echo "old pending cancelled";'))
print(wp('$p=wc_get_product(58); $p->set_manage_stock(true); $p->set_stock_quantity(1); $p->save(); echo "p58 stock=1";'))
m0=mails()

print("\n[1] Płatność przeszła, ale powiadomienie P24 nie dotarło do sklepu")
o1=place("s1@example.com", product=56, sim="pay_silent"); print("  order",o1, state(o1))
backdate(o1,6); sweep(); st=state(o1); print("  po przeglądzie:",st); print(notes(o1))
check("zamówienie potwierdzone przez strażnika", st.startswith("processing"))
check("alert 'missed' dla właściciela", "missed" in st)

print("\n[2] BLIK porzucony → przypomnienie po 15 min, bez duplikatu, bez anulowania przez 24 h")
o2=place("s2@example.com", product=58, sim="cancel")
backdate(o2,10); sweep(); check("brak przypomnienia przed 15 min", "reminded=-" in state(o2))
backdate(o2,16); sweep(); st=state(o2); print("  ",st); check("przypomnienie wysłane po 15 min", "reminded=1" in st)
sweep(); check("drugi przegląd nie wysyła drugiego maila", True)
backdate(o2,61); cancel_run(); st=state(o2); print("  po limicie 60 min:",st); check("nie anulowane w 24 h po przypomnieniu", st.startswith("pending"))
wp(f'$o=wc_get_order({o2}); $o->update_meta_data("_moretti_guard_reminded", time()-25*3600); $o->save(); echo 1;'); backdate(o2,25*60)
cancel_run(); st=state(o2); print("  po 25 h:",st); check("anulowane po 24 h z flagą auto", st.startswith("cancelled") and "auto_cancelled=yes" in st)

print("\n[3] Ostatnia sztuka sprzedana komuś innemu, a potem przychodzą pieniądze za anulowane zamówienie [2]")
o3=place("s3@example.com", product=58, sim="pay"); print("  inny klient kupił ostatnią sztukę, order",o3, state(o3))
mark_paid(o2); backdate(o2,25*60); sweep(); st=state(o2); print("  ",st); print(notes(o2))
check("anulowane zamówienie wróciło do realizacji", st.startswith("processing"))
check("alert 'paid_after_cancel'", "paid_after_cancel" in st)
print("  stan p58:", wp('wp_cache_flush(); echo wc_get_product(58)->get_stock_quantity();'))

print("\n[4] Przelew bankowy (ITP Credit Agricole) porzucony → przypomnienie dopiero po 40 min")
o4=place("s4@example.com", product=56, gateway="p24-online-payments", method=286, sim="cancel")
backdate(o4,20); sweep(); check("brak przypomnienia po 20 min", "reminded=-" in state(o4))
backdate(o4,41); sweep(); check("przypomnienie po 40 min", "reminded=1" in state(o4))

print("\n[5] Przed automatycznym anulowaniem strażnik pyta P24 i znajduje płatność")
o5=place("s5@example.com", product=56, sim="pay_silent")
wp(f'$o=wc_get_order({o5}); $o->update_meta_data("_moretti_guard_reminded","test-skip"); $o->save(); echo 1;')  # pomiń przegląd: tylko ścieżka anulowania
backdate(o5,61); cancel_run(); st=state(o5); print("  ",st); print(notes(o5))
check("nie anulowane, tylko potwierdzone", st.startswith("processing"))

print("\n[6] Klient po nieudanej płatności złożył nowe zamówienie → bez przypomnienia o starym")
o6a=place("s6@example.com", product=56, sim="cancel"); o6b=place("s6@example.com", product=56, sim="pay")
backdate(o6a,16); sweep(); st=state(o6a); print("  ",st); check("stare zamówienie bez przypomnienia", "skipped-newer-order" in st)

print("\n[7] Ten sam klient opłaca dwa razy tę samą kwotę")
o7a=place("s7@example.com", product=54, sim="pay"); o7b=place("s7@example.com", product=54, sim="pay")
st=state(o7b); print("  ",st); check("alert 'double'", "double" in st)

print("\n[8] Przekaz tradycyjny: brak przypomnienia i brak anulowania przez 72 h")
o8=place("s8@example.com", product=56, gateway="p24-online-payments", method=178, sim="cancel")
backdate(o8,120); sweep(); cancel_run(); st=state(o8); print("  ",st)
check("przekaz tradycyjny: bez przypomnienia i nieanulowany po 2 h", st.startswith("pending") and "reminded=-" in st)


print("\n[9] P24 widzi opłacone, ale kwota się nie zgadza → sklep NIE potwierdza, właściciel dostaje alert")
o9=place("s9@example.com", product=56, sim="pay_silent")
wp(f'$o=wc_get_order({o9}); $o->set_total(1.00); $o->save(); echo 1;')
backdate(o9,6); sweep(); st=state(o9); print("  ",st); print(notes(o9))
check("zamówienie dalej nieopłacone", st.startswith("pending"))
check("alert 'mismatch'", "mismatch" in st)

print("\n[10] Klient bez konta klika „Dokończ płatność” w przypomnieniu i płaci BLIK-iem")
html=subprocess.run(["docker","compose","exec","-T","cli","bash","-c","grep -l 'nr "+str(o4)+"' wp-content/uploads/local-mails/*czeka-na-platnosc* | tail -1 | xargs cat"],capture_output=True,text=True,cwd=ROOT).stdout

link=re.search(r'href="([^"]*order-pay[^"]*)"',html)
check("mail ma podpisany link do zapłaty", bool(link) and "moretti_pay" in link.group(1))
url=link.group(1).replace("&#038;","&").replace("&amp;","&")
bad=re.sub(r"moretti_pay=(\d+)\.\w+", r"moretti_pay=\1.00000000000000000000000000000000", url)
tb=requests.get(bad,headers={"User-Agent":"Mozilla/5.0"},timeout=30).text
check("podrobiony podpis → WooCommerce prosi o email", "payment_method_p24" not in tb and "email" in tb.lower())
s10=requests.Session(); s10.headers["User-Agent"]="Mozilla/5.0"
page=s10.get(url,timeout=30); txt=page.text; 
check("strona płatności pokazuje metody P24 od razu", "payment_method_p24" in txt)
form=re.search(r'<form id="order_review".*?</form>',txt,re.S).group(0)
fields={m.group(1):m.group(2) for m in re.finditer(r'<input[^>]*name="([^"]+)"[^>]*value="([^"]*)"',form)}
fields={k:v for k,v in fields.items() if k in ("woocommerce_pay","woocommerce-pay-nonce","_wp_http_referer")}
print("   pola regulaminu na stronie:", sorted(set(re.findall(r'name="([^"]*(?:regul|terms)[^"]*)"',form))))
fields.update({"payment_method":"p24-online-payments-154","terms":"on","terms-field":"1"})
r=s10.post(url,data=fields,timeout=30,allow_redirects=False)
loc=r.headers.get("Location","")
print("   po „Zapłać”:",r.status_code, loc.replace(B,"")[:80])
if "moretti-p24-sim" in loc:
    p=s10.get(loc,timeout=30); nonce=re.search(r'name="_wpnonce" value="([^"]+)"',p.text).group(1)
    s10.post(loc,data={"_wpnonce":nonce,"moretti_p24_action":"pay"},timeout=30)
st=state(o4); print("  ",st)
check("zamówienie z przypomnienia opłacone", st.startswith("processing"))
new=mails()-m0
print("\nMaile wysłane w teście:"); [print("  ",x) for x in mail_subjects(new)]
print(f"\nWYNIK: {sum(c for _,c in RESULTS)}/{len(RESULTS)}")
wp('$p=wc_get_product(58); $p->set_manage_stock(false); $p->set_stock_quantity(null); $p->set_stock_status("instock"); $p->save(); echo 1;')  # sprzątanie po scenariuszu [3]
sys.exit(0 if all(c for _,c in RESULTS) else 1)
