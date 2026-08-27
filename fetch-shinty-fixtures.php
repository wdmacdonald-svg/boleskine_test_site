<?php
/**
 * Standalone fixtures and results fetcher for Boleskine Camanachd Club.
 *
 * Calls the Sportspress REST API on matches.shinty.com to retrieve
 * Boleskine's next upcoming match and latest past match, then writes 
 * the combined result to fixtures.js.
 *
 * Requirements: PHP CLI, php-curl extension.
 * Usage: php fetch-shinty-fixtures.php
 */

define('API_BASE', 'https://matches.shinty.com/wp-json/sportspress/v2');
// Easily change this back to 'Boleskine' next season!
define('TEAM_SEARCH', 'Kingussie');
define('OUTPUT_FILE', __DIR__ . '/index_files/fixtures.js');
define('CURL_TIMEOUT', 10);

function json_get(string $url): ?array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => CURL_TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Boleskine-Fixture-Fetcher/2.0',
    ]);
    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error !== '') {
        fwrite(STDERR, "cURL error: $error\n");
        return null;
    }
    if ($httpCode !== 200) {
        fwrite(STDERR, "HTTP $httpCode from $url\n");
        return null;
    }

    $data = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        fwrite(STDERR, 'JSON decode error: ' . json_last_error_msg() . "\n");
        return null;
    }

    return $data;
}

function ordinalSuffix(int $day): string {
    if (!in_array($day % 100, [11, 12, 13])) {
        switch ($day % 10) {
            case 1: return $day . 'st';
            case 2: return $day . 'nd';
            case 3: return $day . 'rd';
        }
    }
    return $day . 'th';
}

function abbreviateLocation(string $location): string {
    return preg_replace('/\bPark\b/i', 'Pk', $location);
}

function formatEventDate(string $rawDate): array {
    if (!$rawDate) return ['', ''];
    $ts = strtotime($rawDate);
    $dateFmt = date('l', $ts) . ', ' . date('F', $ts) . ' ' . ordinalSuffix((int) date('j', $ts));
    $timeFmt = date('g:i', $ts) . ' PM BST';
    return [$dateFmt, $timeFmt];
}

// 1. Resolve Team ID
$teams = json_get(API_BASE . '/teams?search=' . urlencode(TEAM_SEARCH));
if (empty($teams)) {
    die("Team not found.\n");
}
$teamId = 0;
foreach ($teams as $t) {
    if (strtolower(trim($t['title']['rendered'] ?? '')) === strtolower(TEAM_SEARCH)) {
        $teamId = (int) $t['id'];
        break;
    }
}
if (!$teamId) $teamId = (int) $teams[0]['id'];

// Helper to fetch Team Name
$teamNamesCache = [];
function getTeamName(int $tid) {
    global $teamNamesCache;
    if (isset($teamNamesCache[$tid])) return $teamNamesCache[$tid];
    $info = json_get(API_BASE . "/teams/{$tid}");
    $name = $info['title']['rendered'] ?? "Team {$tid}";
    
    // Strip trailing ' 2' or '2' to hide the fact it's a second team!
    $name = preg_replace('/(?:\s+)?2$/', '', $name);
    
    $teamNamesCache[$tid] = $name;
    return $name;
}

$today = date('Y-m-d\TH:i:s');
$output = [
    'last_updated' => date('c'),
    'fixtures' => ['has_fixture' => false],
    'results'  => ['has_result' => false],
];

$searchEnc = urlencode(TEAM_SEARCH);

// 2. Fetch Next Fixture
$futureEvents = json_get(API_BASE . "/events?search={$searchEnc}&after={$today}&order=asc&orderby=date&per_page=50") ?? [];
$fixtures = array_values(array_filter($futureEvents, function($e) use ($teamId, $today) {
    $tids = array_map('intval', $e['teams'] ?? []);
    return in_array($teamId, $tids) && ($e['status'] ?? '') === 'future' && ($e['date'] ?? '') >= $today;
}));

if (!empty($fixtures)) {
    $ev = $fixtures[0];
    $tids = array_map('intval', $ev['teams'] ?? []);
    $homeTeam = count($tids) > 0 ? getTeamName($tids[0]) : '';
    $awayTeam = count($tids) > 1 ? getTeamName($tids[1]) : '';
    $isHome = count($tids) > 0 && $tids[0] === $teamId;
    
    $venueName = '';
    if (!empty($ev['venues'])) {
        $vInfo = json_get(API_BASE . "/venues/{$ev['venues'][0]}");
        $venueName = $vInfo['name'] ?? $vInfo['title']['rendered'] ?? '';
    }
    
    list($dFmt, $tFmt) = formatEventDate($ev['date'] ?? '');
    $wayLabel = $isHome ? '(H)' : '(A)';
    $locFmt = $venueName ? abbreviateLocation("$venueName $wayLabel") : $wayLabel;
    
    $output['fixtures'] = [
        'has_fixture' => true,
        'home_team' => $homeTeam,
        'away_team' => $awayTeam,
        'date_formatted' => $dFmt,
        'time_formatted' => $tFmt,
        'location_formatted' => $locFmt,
    ];
}

// 3. Fetch Latest Result
$pastEvents = json_get(API_BASE . "/events?search={$searchEnc}&before={$today}&order=desc&orderby=date&per_page=50") ?? [];
$results = array_values(array_filter($pastEvents, function($e) use ($teamId, $today) {
    $tids = array_map('intval', $e['teams'] ?? []);
    return in_array($teamId, $tids) && in_array($e['status'] ?? '', ['publish', 'closed']) && ($e['date'] ?? '') < $today;
}));

if (!empty($results)) {
    $ev = $results[0];
    $tids = array_map('intval', $ev['teams'] ?? []);
    $homeId = $tids[0] ?? 0;
    $awayId = $tids[1] ?? 0;
    $homeTeam = getTeamName($homeId);
    $awayTeam = getTeamName($awayId);
    
    $res = $ev['results'] ?? [];
    $homeGoals = $res[$homeId]['goals'] ?? '0';
    $awayGoals = $res[$awayId]['goals'] ?? '0';
    
    // Fetch Goalscorers for Boleskine
    $boleskineScorers = [];
    $perf = $ev['performance'] ?? [];
    if (isset($perf[$teamId]) && (is_array($perf[$teamId]) || is_object($perf[$teamId]))) {
        foreach ((array)$perf[$teamId] as $pid => $stats) {
            if ($pid == 0) continue; // Skip totals row
            if (!empty($stats['goals']) && $stats['goals'] !== '0') {
                $pInfo = json_get(API_BASE . "/players/{$pid}");
                $pName = $pInfo['title']['rendered'] ?? "Player {$pid}";
                $boleskineScorers[] = "$pName {$stats['goals']}";
            }
        }
    }
    
    list($dFmt, $tFmt) = formatEventDate($ev['date'] ?? '');
    
    $output['results'] = [
        'has_result' => true,
        'home_team' => $homeTeam,
        'away_team' => $awayTeam,
        'home_score' => $homeGoals,
        'away_score' => $awayGoals,
        'boleskine_scorers' => $boleskineScorers,
        'date_formatted' => $dFmt,
    ];
}

file_put_contents(OUTPUT_FILE, 'var fixtureData = ' . json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . "\n");
echo "Fixtures and results updated successfully.\n";

