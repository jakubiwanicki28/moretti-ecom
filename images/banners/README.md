# Banery hero (strona główna)

Karuzela na stronie głównej ładuje zdjęcia z tego folderu. Kolejność slajdów wynika z **numeru** w nazwie pliku.

## Sposób 1: jeden obraz na desktop i mobile (stary sposób)

- Nazwa pliku: **`N.rozszerzenie`**, np. `1.jpg`, `2.jpg`, `3.png`.
- Numer `N` określa kolejność slajdu (1 = pierwszy, 2 = drugi itd.).
- Ten sam obraz jest pokazywany na desktopie i na mobile.

**Przykład:** `1.jpg`, `2.jpg`, `3.jpg` → 3 slajdy, ten sam obraz wszędzie.

---

## Sposób 2: osobne banery na desktop i mobile (nowy sposób)

- **Desktop:** plik **`desktop_N.rozszerzenie`**, np. `desktop_1.jpg`, `desktop_2.jpg`.
- **Mobile:** plik **`mobile_N.rozszerzenie`**, np. `mobile_1.jpg`, `mobile_2.jpg`.
- Numer `N` łączy parę: ten sam `N` = jeden slajd, dwa obrazy (desktop + mobile).
- Można mieć tylko desktop (wtedy na mobile używany jest ten sam plik) albo tylko mobile (wtedy na desktopie ten sam plik). Zalecane: oba.

**Przykład:**

- `desktop_1.jpg` + `mobile_1.jpg` → slajd 1: inny obraz na dużym ekranie, inny na telefonie.
- `desktop_2.jpg` + `mobile_2.jpg` → slajd 2: to samo.

Kolejność slajdów: 1, 2, … (według numeru `N`).

---

## Mieszanie

Można łączyć oba sposoby w jednym folderze:

- Slajd 1: `desktop_1.jpg` + `mobile_1.jpg`
- Slajd 2: tylko `2.jpg` (ten sam na desktop i mobile)
- Slajd 3: `desktop_3.jpg` + `mobile_3.jpg`

---

## Jak wrzucać pliki (testowanie)

1. **JPG** (albo PNG itd.) wrzuć do folderu **`images/banners/`** w motywie (ten katalog, w którym jest ten README).

2. **Jedna wersja (desktop = mobile):**  
   Nazwij plik np. `1.jpg`, `2.jpg` – odśwież stronę główną i sprawdź karuzelę.

3. **Dwie wersje (desktop + mobile):**  
   Dla slajdu nr 1 dodaj:
   - `desktop_1.jpg` – widoczny od szerokości ~768 px w górę,
   - `mobile_1.jpg` – widoczny na wąskich ekranach (telefon).

   Dla slajdu nr 2: `desktop_2.jpg`, `mobile_2.jpg` itd.

4. **Breakpoint:** przełączenie „mobile ↔ desktop” następuje przy **768 px** szerokości viewportu (Tailwind `md`).

---

## Uwagi

- Pliki spoza powyższych wzorców (np. `banner-1.jpg` bez prefiksu `desktop_` / `mobile_` lub bez samego numeru) są **ignorowane**.
- Konfiguracja CTA i offsetu kadru (przycisk „Kup teraz”, przesunięcie zdjęcia) jest w **`hero-banners-config.php`** w katalogu motywu – indeks slajdu to 0, 1, 2, … (kolejność wyświetlania).
