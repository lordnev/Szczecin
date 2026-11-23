# Tablica odjazdów na żywo – Pomorzany (ZDiTM Szczecin) dla WordPress

Kompletne repozytorium zawierające trzy wersje widżetu odjazdów na żywo (GPS + czas rozkładowy) dla pętli **Pomorzany** oraz pętli **Pomorzany Dobrzyńska** w Szczecinie, zasilane bezpośrednio z oficjalnego API ZDiTM Szczecin.

## Zawartość repozytorium

* **`v1-basic/`** – Wersja podstawowa: zegar na żywo, kolory linii zgodne z ZDiTM, kierunki jazdy, odjazdy GPS oraz rozkładowe (bez przycisków filtrowania).
* **`v2-vehicle-type-filters/`** – Wersja z filtrowaniem po typie pojazdu: przyciski **Wszystkie**, **Tramwaje**, **Autobusy**.
* **`v3-line-filters/`** *(Rekomendowana)* – Wersja z filtrowaniem po konkretnych liniach: przyciski **Wszystkie**, **3**, **6**, **11**, **12**, **53**, **524**, **528** w oficjalnej kolorystyce ZDiTM.
* **`docs/INSTALACJA_I_KONFIGURACJA.md`** – Pełna instrukcja wdrożenia krok po kroku od zera oraz poradnik dodawania nowych linii i słupków w przyszłości.

## Szybki start (WordPress)

1. Wybierz wybraną wersję (np. `v3-line-filters/zditm-pomorzany-shortcode-v3.php`).
2. Wklej zawartość pliku (bez otwierającego tagu `<?php`) na samym końcu pliku `functions.php` w swoim motywie WordPress (lub dodaj jako aktywny fragment we wtyczce **WPCode**).
3. Na dowolnej podstronie lub we wpisie wstaw blok **Shortcode** o treści:
   ```text
   [zditm_pomorzany]
   ```
4. Szczegółową instrukcję od zera znajdziesz w pliku [`docs/INSTALACJA_I_KONFIGURACJA.md`](docs/INSTALACJA_I_KONFIGURACJA.md).
