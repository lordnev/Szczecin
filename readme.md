# Szczecin – Repozytorium projektów

Repozytorium zawierające projekty związane z danymi i usługami miejskimi Szczecina.

---

## Zawartość

### 🚌 [ZDiTM-odjazdy](./ZDiTM-odjazdy/)
**Tablica odjazdów na żywo – Pomorzany (ZDiTM Szczecin) dla WordPress**

Kompletne repozytorium zawierające **trzy wersje widżetu odjazdów na żywo** (GPS + czas rozkładowy) dla pętli **Pomorzany** oraz **Pomorzany Dobrzyńska** w Szczecinie, zasilane bezpośrednio z oficjalnego API ZDiTM Szczecin.

| Wersja | Katalog | Funkcjonalność |
|---|---|---|
| **v1 – Podstawowa** | `v1-basic/` | Zegar na żywo, kolory linii ZDiTM, kierunki jazdy, odjazdy GPS i rozkładowe (bez filtrów) |
| **v2 – Filtry pojazdów** | `v2-vehicle-type-filters/` | Przyciski: **Wszystkie**, **Tramwaje**, **Autobusy** |
| **v3 – Filtry linii** ⭐ | `v3-line-filters/` | Przyciski: **Wszystkie**, **3**, **6**, **11**, **12**, **53**, **524**, **528** w oficjalnej kolorystyce ZDiTM |

Każda wersja dostępna w dwóch formatach:
- **Shortcode WordPress** (`.php`) – do wklejenia do `functions.php` lub wtyczki WPCode
- **Standalone HTML** (`.html`) – samodzielna strona do hostowania gdziekolwiek

📖 **Pełna instrukcja wdrożenia:** [`ZDiTM-odjazdy/docs/INSTALACJA_I_KONFIGURACJA.md`](./ZDiTM-odjazdy/docs/INSTALACJA_I_KONFIGURACJA.md)

---

## Licencja

Projekty w tym repozytorium są udostępniane na licencji MIT (chyba że inaczej zaznaczone w konkretnym projekcie).

---

## Kontakt

Wszelkie pytania, sugestie lub zgłoszenia błędów – proszę otworzyć **Issue** w tym repozytorium.