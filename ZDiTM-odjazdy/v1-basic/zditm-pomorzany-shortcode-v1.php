<?php
/**
 * Wersja 1 (v1): Podstawowa tablica odjazdów na żywo z kierunkami jazdy i zegarem.
 * Shortcode: [zditm_pomorzany]
 */

// 1. Endpoint REST API pobierający dane z ZDiTM (z cache na 15 sek.)
add_action('rest_api_init', function () {
    register_rest_route('zditm/v1', '/pomorzany', array(
        'methods' => 'GET',
        'callback' => 'zditm_get_pomorzany_departures',
        'permission_callback' => '__return_true'
    ));
});

function zditm_get_pomorzany_departures() {
    $cache_key = 'zditm_pomorzany_live_data_v1';
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return rest_ensure_response($cached);
    }

    $stops = array(
        '21611' => 'Pomorzany (sł. 11)',
        '21621' => 'Pomorzany (peron 21)',
        '21622' => 'Pomorzany (peron 22)',
        '21623' => 'Pomorzany (peron 23)',
        '21624' => 'Pomorzany (peron 24)',
        '21625' => 'Pomorzany (peron 25)',
        '21631' => 'Pomorzany (sł. 31)',
        '21632' => 'Pomorzany (sł. 32)',
        '21911' => 'Pomorzany Dobrzyńska (Pętla)'
    );

    $target_routes = array('3', '6', '11', '12', '53', '524', '528');
    $results = array();

    foreach ($stops as $stop_code => $stop_label) {
        $url = 'https://www.zditm.szczecin.pl/api/v1/displays/' . $stop_code;
        $response = wp_remote_get($url, array(
            'timeout' => 8,
            'headers' => array(
                'Accept' => 'application/json',
                'User-Agent' => 'WordPress-ZDiTM-Widget/1.0'
            )
        ));

        if (is_wp_error($response)) {
            continue;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['departures']) && is_array($data['departures'])) {
            foreach ($data['departures'] as $dep) {
                $line = trim((string)($dep['line_number'] ?? ''));
                if (in_array($line, $target_routes, true)) {
                    $results[] = array(
                        'line' => $line,
                        'direction' => trim((string)($dep['direction'] ?? '')),
                        'stopCode' => (string)$stop_code,
                        'stopName' => $stop_label,
                        'timeReal' => $dep['time_real'] ?? null,
                        'timeScheduled' => $dep['time_scheduled'] ?? null
                    );
                }
            }
        }
    }

    set_transient($cache_key, $results, 15);
    return rest_ensure_response($results);
}

// 2. Shortcode [zditm_pomorzany] wyświetlający tablicę odjazdów
add_shortcode('zditm_pomorzany', function () {
    $api_endpoint = esc_url_raw(rest_url('zditm/v1/pomorzany'));

    ob_start();
    ?>
    <div id="zditm-live-board" style="font-family: system-ui, -apple-system, sans-serif; max-width: 820px; margin: 20px auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background: #fff;">
      <div style="background: #005E85; color: #fff; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
          <h3 style="margin: 0; font-size: 18px; color: #fff;">Odjazdy na żywo – Pomorzany</h3>
          <small style="opacity: 0.9;">Linie: 3, 6, 11, 12, 53, 524, 528 (Pętla i Przystanki)</small>
        </div>
        <div style="text-align: right;">
          <div id="zditm-clock" style="font-size: 22px; font-weight: 700; font-variant-numeric: tabular-nums; line-height: 1.1;">--:--:--</div>
          <span id="zditm-status" style="font-size: 11px; opacity: 0.85;">Ładowanie danych...</span>
        </div>
      </div>
      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; margin: 0;">
          <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
              <th style="padding: 12px 16px; width: 65px;">Linia</th>
              <th style="padding: 12px 16px;">Kierunek jazdy</th>
              <th style="padding: 12px 16px;">Przystanek (Słupek)</th>
              <th style="padding: 12px 16px; text-align: right;">Odjazd</th>
            </tr>
          </thead>
          <tbody id="zditm-departures-body">
            <tr>
              <td colspan="4" style="padding: 24px; text-align: center; color: #64748b;">Pobieranie rozkładu z ZDiTM Szczecin...</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="background: #f8fafc; padding: 8px 16px; font-size: 11px; color: #64748b; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; border-top: 1px solid #e2e8f0;">
        <span>⚡ Czas rzeczywisty (GPS) • 🕒 Czas rozkładowy</span>
        <span>ZDiTM Szczecin API • Odświeżanie co 20 sek.</span>
      </div>
    </div>
    <script type="text/javascript" data-no-optimize="1" data-cfasync="false">
    (function() {
      var API_URL = <?php echo json_encode($api_endpoint); ?>;
      var DEFAULT_DIRECTIONS = {
        "3": "Osiedle Zawadzkiego",
        "6": "Gocław",
        "11": "Ludowa",
        "12": "Dworzec Niebuszewo",
        "53": "Stocznia Szczecińska",
        "524": "Police Zajezdnia",
        "528": "Maczka"
      };
      var ARRIVAL_ONLY_STOPS = ["21611", "21631", "21632"];
      var ROUTE_COLORS = {
        "3": { bg: "#005E85", text: "#FFFFFF" },
        "6": { bg: "#005E85", text: "#FFFFFF" },
        "11": { bg: "#005E85", text: "#FFFFFF" },
        "12": { bg: "#005E85", text: "#FFFFFF" },
        "53": { bg: "#436000", text: "#FED256" },
        "524": { bg: "#000000", text: "#FED256" },
        "528": { bg: "#000000", text: "#FFFFFF" }
      };
      function updateClock() {
        var clockEl = document.getElementById("zditm-clock");
        if (clockEl) {
          var now = new Date();
          clockEl.textContent = now.toLocaleTimeString("pl-PL", {
            timeZone: "Europe/Warsaw",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
          });
        }
      }
      function resolveDirection(rawDirection, line, stopCode) {
        var cleaned = (rawDirection || "").trim();
        if (cleaned) return cleaned;
        if (ARRIVAL_ONLY_STOPS.indexOf(stopCode) !== -1) {
          return "Pomorzany (Koniec trasy)";
        }
        return DEFAULT_DIRECTIONS[line] || "Kierunek nieznany";
      }
      function getMinutesUntil(dep) {
        if (dep.timeReal !== null && dep.timeReal !== undefined) {
          return Number(dep.timeReal);
        }
        if (dep.timeScheduled) {
          var now = new Date();
          var parts = dep.timeScheduled.split(":");
          var target = new Date(now);
          target.setHours(Number(parts[0]), Number(parts[1]), 0, 0);
          var diff = Math.round((target - now) / 60000);
          if (diff < -120) diff += 1440;
          return diff;
        }
        return 9999;
      }
      function formatDepartureBadge(dep) {
        if (dep.timeReal !== null && dep.timeReal !== undefined) {
          var mins = Number(dep.timeReal);
          var label = mins <= 0 ? "teraz" : mins + " min";
          return '<span style="color: #15803d; font-weight: 700;">⚡ ' + label + '</span>';
        }
        if (dep.timeScheduled) {
          return '<span style="color: #334155; font-weight: 600;">🕒 ' + dep.timeScheduled + '</span>';
        }
        return "-";
      }
      function refreshBoard() {
        var tbody = document.getElementById("zditm-departures-body");
        var statusEl = document.getElementById("zditm-status");
        if (!tbody || !statusEl) return;
        fetch(API_URL, { cache: "no-store" })
          .then(function(res) {
            if (!res.ok) throw new Error("HTTP " + res.status);
            return res.json();
          })
          .then(function(list) {
            var allDepartures = Array.isArray(list) ? list : [];
            allDepartures.forEach(function(d) {
              d.direction = resolveDirection(d.direction, d.line, d.stopCode);
              d.sortMinutes = getMinutesUntil(d);
            });
            allDepartures = allDepartures
              .filter(function(d) { return d.sortMinutes >= -1; })
              .sort(function(a, b) { return a.sortMinutes - b.sortMinutes; });
            var upcoming = allDepartures.slice(0, 20);
            if (upcoming.length === 0) {
              tbody.innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">Brak nadchodzących odjazdów dla wybranych linii w najbliższym czasie.</td></tr>';
            } else {
              tbody.innerHTML = upcoming.map(function(dep) {
                var colors = ROUTE_COLORS[dep.line] || { bg: "#334155", text: "#FFFFFF" };
                var isArrival = ARRIVAL_ONLY_STOPS.indexOf(dep.stopCode) !== -1 || dep.direction.toLowerCase() === "pomorzany";
                var dirPrefix = isArrival ? "🏁" : "➔";
                var dirStyle = isArrival ? "color: #64748b; font-style: italic;" : "color: #0f172a; font-weight: 600;";
                return '<tr style="border-bottom: 1px solid #f1f5f9;">' +
                  '<td style="padding: 10px 16px;">' +
                    '<span style="display: inline-block; min-width: 36px; text-align: center; font-weight: 700; padding: 4px 8px; border-radius: 6px; background: ' + colors.bg + '; color: ' + colors.text + ';">' +
                      dep.line +
                    '</span>' +
                  '</td>' +
                  '<td style="padding: 10px 16px; ' + dirStyle + '">' +
                    '<span style="opacity: 0.7; margin-right: 4px;">' + dirPrefix + '</span>' + dep.direction +
                  '</td>' +
                  '<td style="padding: 10px 16px; color: #475569; font-size: 13px;">' + dep.stopName + '</td>' +
                  '<td style="padding: 10px 16px; text-align: right; white-space: nowrap;">' + formatDepartureBadge(dep) + '</td>' +
                '</tr>';
              }).join("");
            }
            var syncTime = new Date().toLocaleTimeString("pl-PL", { hour: "2-digit", minute: "2-digit", second: "2-digit" });
            statusEl.textContent = "Zaktualizowano: " + syncTime;
          })
          .catch(function(err) {
            console.error("Błąd pobierania danych:", err);
            statusEl.textContent = "Błąd: " + err.message;
            tbody.innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align: center; color: #dc2626;">Błąd połączenia z endpointem (' + err.message + ').</td></tr>';
          });
      }
      updateClock();
      refreshBoard();
      setInterval(updateClock, 1000);
      setInterval(refreshBoard, 20000);
    })();
    </script>
    <?php
    return ob_get_clean();
});
