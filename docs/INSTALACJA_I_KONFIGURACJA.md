# Dokumentacja wdrożeniowa od zera: Tablica odjazdów ZDiTM Szczecin (Pomorzany)

## 1. Jak działa ten system? (Architektura)

Bezpośrednie pobieranie danych z `zditm.szczecin.pl` bezpośrednio z poziomu przeglądarki użytkownika na stronie WordPress często kończy się błędem blokady **CORS** lub błędem **Mixed Content** (mieszanie protokołów `http://` i `https://`). Dodatkowo edytor blokowy WordPressa (Gutenberg) ze względów bezpieczeństwa usuwa tagi `<script>` wklejane w treść wpisu po jego opublikowaniu.

Rozwiązanie składa się z dwóch współpracujących części zamkniętych w jednym pliku PHP:
1. **Backend (Serwer WordPress – REST API):** Tworzy własny punkt końcowy na Twojej stronie (`/?rest_route=/zditm/v1/pomorzany`). Serwer WordPress cyklicznie (maksymalnie raz na 15 sekund dzięki pamięci podręcznej `transient`) pobiera dane z oficjalnego API ZDiTM Szczecin dla 9 słupków przystankowych na Pomorzanach, filtruje wybrane linie i udostępnia je w bezpiecznym formacie JSON.
2. **Frontend (Shortcode `[zditm_pomorzany]`):** Generuje tabelę HTML oraz skrypt JavaScript, który co 1 sekundę aktualizuje zegar, a co 20 sekund odświeża listę odjazdów z Twojego własnego endpointu.

---

## 2. Instalacja krok po kroku od zera

### Krok 1: Wybór wersji
W repozytorium znajdują się 3 wersje skryptu. Dla każdej przygotowano dwa pliki:
* `*-shortcode-vX.php` – **do użycia w WordPressie** (zawiera backend PHP + frontend JS).
* `*-standalone-vX.html` – czysty plik HTML+JS do testów lokalnych lub użycia na zwykłych stronach statycznych (bez PHP).

Wybierz jedną z wersji PHP:
* **v1 (`v1-basic`)** – sama tabela odjazdów, zegar i kierunki.
* **v2 (`v2-vehicle-type-filters`)** – tabela + filtry: *Wszystkie / Tramwaje / Autobusy*.
* **v3 (`v3-line-filters`)** – tabela + filtry dla każdej linii osobno (*Wszystkie / 3 / 6 / 11 / 12 / 53 / 524 / 528*).

---

### Krok 2: Dodanie kodu do WordPressa

Możesz to zrobić na **dwa sposoby** (wybierz jeden):

#### Metoda A: Przez wtyczkę WPCode (Najbezpieczniejsza – odporna na aktualizacje motywu)
1. Zaloguj się do kokpitu WordPress (`/wp-admin`).
2. Przejdź do **Wtyczki → Dodaj nową**.
3. Wyszukaj wtyczkę **WPCode** (*WPCode – Insert Headers and Footers + Custom Code Snippets*), zainstaluj ją i włącz.
4. W menu po lewej stronie kliknij **Fragmenty kodu (Code Snippets) → + Dodaj fragment**.
5. Najedź na **Dodaj własny kod (Nowy fragment)** i kliknij **Użyj fragmentu**.
6. Z listy **Typ kodu (Code Type)** wybierz **Fragment PHP (PHP Snippet)**.
7. Otwórz wybrany plik `.php` z tego repozytorium (np. `v3-line-filters/zditm-pomorzany-shortcode-v3.php`), skopiuj jego zawartość **bez pierwszej linijki `<?php`** i wklej do edytora.
8. W prawym górnym rogu przestaw przełącznik z *Nieaktywny* na **Aktywny** i kliknij **Zapisz fragment**.

#### Metoda B: Bezpośrednio w pliku `functions.php` motywu
1. W kokpicie WordPress przejdź do **Wygląd → Edytor plików motywu** (lub połącz się przez FTP i wejdź do `/wp-content/themes/nazwa-twojego-motywu/`).
2. Z listy plików po prawej stronie wybierz **Funkcje motywu (`functions.php`)**.
3. Przewiń plik na sam dół.
   * *Uwaga:* Jeśli na samym końcu pliku znajduje się znacznik zamykający `?>`, usuń go lub wklej nowy kod **przed** nim.
4. Wklej zawartość wybranego pliku `.php` (pomijając pierwszą linię `<?php`).
5. Kliknij **Zaktualizuj plik**.

---

### Krok 3: Weryfikacja działania API
Zanim wstawisz tablicę na stronę, sprawdź, czy backend poprawnie pobiera dane. Otwórz w nowej karcie przeglądarki adres Twojej strony z dopiskiem `/?rest_route=/zditm/v1/pomorzany`, np.:
```text
https://twojadomena.pl/?rest_route=/zditm/v1/pomorzany
```
Jeśli zobaczysz tekst zaczynający się od `[{"line":"3",...` (lub `[]` w środku nocy, gdy nic nie kursuje), oznacza to, że połączenie z ZDiTM działa prawidłowo.

---

### Krok 4: Umieszczenie tablicy na stronie lub we wpisie
1. Przejdź do **Strony → Dodaj nową** (lub edytuj istniejącą stronę).
2. Kliknij przycisk **`+`** (Dodaj blok), wyszukaj blok o nazwie **Shortcode** (Krótki kod) i wstaw go.
3. W pole bloku wpisz dokładnie:
   ```text
   [zditm_pomorzany]
   ```
4. Kliknij **Opublikuj** / **Zaktualizuj**.
5. *Ważne:* Jeśli korzystasz z wtyczki przyspieszającej działanie strony (np. **LiteSpeed Cache**, **WP Rocket**, **W3 Total Cache**, **Autoptimize**), kliknij przycisk **Wyczyść pamięć podręczną (Purge All)** na górnym pasku WordPressa.

---

## 3. Konfiguracja przystanków i linii w kodzie

### Obsługiwane słupki przystankowe (z bazy GTFS ZDiTM)
| Numer słupka (`stop_id`) | Nazwa wyświetlana | Przeznaczenie |
| :--- | :--- | :--- |
| `21611` | Pomorzany (sł. 11) | Przystanek przyjazdowy (wysiadanie) |
| `21621` – `21625` | Pomorzany (peron 21–25) | Perony odjazdowe pętli tramwajowej |
| `21631`, `21632` | Pomorzany (sł. 31, 32) | Słupki techniczne / przyjazdowe |
| `21911` | Pomorzany Dobrzyńska (Pętla) | Pętla autobusowa (linie 53, 524, 528) |

---

## 4. Jak dodać nowe linie w przyszłości? (Instrukcja)

Gdy ZDiTM Szczecin uruchomi nową linię przejeżdżającą przez Pomorzany (np. linię tramwajową **`4`** lub autobusową **`61`**), wykonaj 4 proste kroki w swoim kodzie PHP:

### Krok 1: Dopisz numer linii do listy pobieranych tras (w PHP)
Znajdź w funkcji `zditm_get_pomorzany_departures()` tablicę `$target_routes` i dopisz numer nowej linii w apostrofach:
```php
$target_routes = array('3', '6', '11', '12', '53', '524', '528', '4');
```

### Krok 2: Dodaj domyślny kierunek jazdy (w JavaScript)
W sekcji `<script>` znajdź obiekt `DEFAULT_DIRECTIONS` i dopisz przystanek końcowy nowej linii (używany awaryjnie, gdyby API nie przesłało nazwy kierunku):
```javascript
var DEFAULT_DIRECTIONS = {
  "3": "Osiedle Zawadzkiego",
  "6": "Gocław",
  "11": "Ludowa",
  "12": "Dworzec Niebuszewo",
  "53": "Stocznia Szczecińska",
  "524": "Police Zajezdnia",
  "528": "Maczka",
  "4": "Krzekowo"
};
```

### Krok 3: Ustaw kolor kafelka linii (w JavaScript)
W obiekcie `ROUTE_COLORS` zdefiniuj kolor tła (`bg`) oraz kolor czcionki (`text`) zgodnie ze standardem ZDiTM:
* Tramwaj dzienny: `bg: "#005E85", text: "#FFFFFF"` (granatowy)
* Autobus dzienny: `bg: "#436000", text: "#FED256"` (zielony)
* Linia pospieszna (A, B, C): `bg: "#B3040F", text: "#FFFFFF"` (czerwony)
* Linia nocna (5xx): `bg: "#000000", text: "#FFFFFF"` (czarny)

Przykład dopisania linii `4`:
```javascript
var ROUTE_COLORS = {
  "3": { bg: "#005E85", text: "#FFFFFF" },
  "6": { bg: "#005E85", text: "#FFFFFF" },
  "11": { bg: "#005E85", text: "#FFFFFF" },
  "12": { bg: "#005E85", text: "#FFFFFF" },
  "53": { bg: "#436000", text: "#FED256" },
  "524": { bg: "#000000", text: "#FED256" },
  "528": { bg: "#000000", text: "#FFFFFF" },
  "4": { bg: "#005E85", text: "#FFFFFF" }
};
```

### Krok 4: Dodaj przycisk filtra w HTML (dotyczy wersji `v3`)
W kontenerze `<div id="zditm-filters">` skopiuj jeden z istniejących przycisków i zmień jego atrybut `data-filter` oraz tekst na numer nowej linii:
```html
<button type="button" data-filter="4" class="zditm-filter-btn" style="cursor: pointer; border: 1px solid #cbd5e1; background: #fff; color: #334155; padding: 5px 12px; border-radius: 6px; font-size: 13px; font-weight: 700; transition: all 0.15s;">4</button>
```
*(W przypadku wersji `v2`, jeśli dodajesz linię tramwajową o numerze wyższym niż `12`, dopisz ją również do tablicy `var TRAM_ROUTES = [...]` w JS).*

---

## 5. Jak dodać nowy słupek przystankowy?

Jeśli chcesz monitorować kolejny przystanek (np. **Szpitalna** o numerze `21411` lub **Frysztacka** `21511`), dopisz go do tablicy `$stops` w PHP:
```php
$stops = array(
    '21611' => 'Pomorzany (sł. 11)',
    '21621' => 'Pomorzany (peron 21)',
    // ... pozostałe przystanki ...
    '21911' => 'Pomorzany Dobrzyńska (Pętla)',
    '21411' => 'Szpitalna (kier. centrum)'
);
```
Numery wszystkich słupków w Szczecinie można sprawdzić w pliku `stops.txt` paczki GTFS ZDiTM lub w adresie URL konkretnego słupka na stronie `zditm.szczecin.pl`.

---

## 6. Rozwiązywanie problemów (FAQ)

* **Po zmianie kodu w `functions.php` na stronie wciąż widać stare linie:**
  Skrypt buforuje dane w bazie WordPress przez 15 sekund (`$cache_key`). Zmień nazwę klucza np. z `'zditm_pomorzany_live_data_v4'` na `'zditm_pomorzany_live_data_v5'` i wyczyść cache wtyczki optymalizującej stronę.
* **Tablica wyświetla komunikat „Brak nadchodzących odjazdów”:**
  W godzinach nocnych (ok. 23:30 – 4:30) kursują wyłącznie linie nocne `524` i `528` z pętli Pomorzany Dobrzyńska (zazwyczaj co 30 lub 60 minut). Jeśli do najbliższego odjazdu jest więcej czasu niż obejmuje okno API ZDiTM, tabela pozostanie pusta do momentu pojawienia się kursu w systemie.
* **Błąd krytyczny strony po edycji `functions.php`:**
  Upewnij się, że nie wkleiłeś drugiego znacznika `<?php` w środku pliku `functions.php` oraz że nie pominąłeś żadnego nawiasu klamrowego `}` na końcu kodu.
