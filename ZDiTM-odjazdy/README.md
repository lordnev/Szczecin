# ZDiTM Odjazdy na żywo – Pomorzany (Szczecin)

**Repository zawiera trzy wersje widżetu odjazdów na żywo** dla pętli **Pomorzany** oraz **Pomorzany Dobrzyńska** w Szczecinie, zasilane bezpośrednio z oficjalnego API ZDiTM Szczecin.

---

## Spis treści

| Katalog | Opis |
|---|---|
| `v1-basic/` | **Wersja podstawowa** – zegar na żywo, kolory linii zgodne z ZDiTM, kierunki jazdy, odjazdy GPS oraz rozkładowe (bez przycisków filtrowania). |
| `v2-vehicle-type-filters/` | **Wersja z filtrowaniem po typie pojazdu** – przyciski **Wszystkie**, **Tramwaje**, **Autobusy**. |
| `v3-line-filters/` | **Wersja z filtrowaniem po konkretnych liniach** (polecana) – przyciski **Wszystkie**, **3**, **6**, **11**, **12**, **53**, **524**, **528** w oficjalnej kolorystyce ZDiTM. |
| `docs/INSTALACJA_I_KONFIGURACJA.md` | Pełna instrukcja wdrożenia krok po kroku od zera oraz poradnik dodawania nowych linii i słupków w przyszłości. |

---

## Szybki start (WordPress)

1. Wybierz wybraną wersję (np. `v3-line-filters/zditm-pomorzany-shortcode-v3.php`).
2. Wklej zawartość pliku (bez otwierającego tagu `<?php`) na samym końcu pliku `functions.php` w swoim motywie WordPress (lub dodaj jako aktywny fragment we wtyczce **WPCode**).
3. Na dowolnej podstronie lub we wpisie wstaw blok **Shortcode** o treści:

   ```text
   [zditm_pomorzany]
   ```

4. Szczegółową instrukcję od zera znajdziesz w pliku [`docs/INSTALACJA_I_KONFIGURACJA.md`](docs/INSTALACJA_I_KONFIGURACJA.md).

---

## Pliki w repozytorium

### v1-basic/

| Plik | Opis |
|---|---|
| `zditm-pomorzany-shortcode-v1.php` | Kod shortcode WordPress (wersja podstawowa). |
| `zditm-pomorzany-standalone-v1.html` | Samodzielna strona HTML z widgetem. |

### v2-vehicle-type-filters/

| Plik | Opis |
|---|---|
| `zditm-pomorzany-shortcode-v2.php` | Kod shortcode WordPress (wersja z filtrowaniem po typie pojazdu). |
| `zditm-pomorzany-standalone-v2.html` | Samodzielna strona HTML z widgetem. |

### v3-line-filters/ (Rekomendowana)

| Plik | Opis |
|---|---|
| `zditm-pomorzany-shortcode-v3.php` | Kod shortcode WordPress (wersja z filtrowaniem po liniach). |
| `zditm-pomorzany-standalone-v3.html` | Samodzielna strona HTML z widgetem. |

### docs/

| Plik | Opis |
|---|---|
| `INSTALACJA_I_KONFIGURACJA.md` | Pełna instrukcja wdrożenia krok po kroku od zera oraz poradnik dodawania nowych linii i słupków w przyszłości. |

---

## Wsparcie i rozwój

- Projekty te powstały w ramach współpracy z **ZDiTM Szczecin**.
- Wszelkie pytania, sugestie lub zgłoszenia błędów są mile widziane – proszę **otworzyć nowe issue** w repozytorium.
- W przyszłości planowana jest możliwość dodawania nowych linii i aktualizowania kolorystyki na podstawie oficjalnych zmian ZDiTM.