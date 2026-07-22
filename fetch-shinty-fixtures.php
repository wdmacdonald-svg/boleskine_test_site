<?php
/**
 * Standalone fixture fetcher for Boleskine Camanachd Club.
 *
 * Calls the Sportspress REST API on matches.shinty.com to retrieve
 * Boleskine's next upcoming match, then writes the result to fixtures.js
 * as a JavaScript variable assignment (works with file:// and http://).
 *
 * Requirements: PHP CLI, php-curl extension.
 * Usage: php fetch-shinty-fixtures.php
 */

define('API_BASE', 'https://matches.shinty.com/wp-json/sportspress/v2');
define('TEAM_SEARCH', 'Boleskine');
define('OUTPUT_FILE', __DIR__ . '/fixtures.js');
define('CURL_TIMEOUT', 10);

function json_get(string $url): ?array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => CURL_TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Boleskine-Fixture-Fetcher/1.0',
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
    $location = preg_replace('/\bPark\b/i', 'Pk', $location);
    $location = preg_replace('/\(home\)/i', '(H)', $location);
    $location = preg_replace('/\(away\)/i', '(A)', $location);
    return $location;
}

// Step 1: Find Boleskine's team ID
$teams = json_get(API_BASE . '/teams?search=' . urlencode(TEAM_SEARCH));
if ($teams === null || empty($teams)) {
    $output = [
        'has_fixture' => false,
        'last_updated' => date('c'),
    ];
    file_put_contents(OUTPUT_FILE, 'var fixtureData = ' . json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . "\n");
    fwrite(STDERR, "Team 'Boleskine' not found on remote API.\n");
    exit(0);
}

$teamId = (int) $teams[0]['id'];

// Step 2: Fetch the next upcoming fixture for Boleskine
$today = date('Y-m-d\TH:i:s');
$events = json_get(API_BASE . "/events?team={$teamId}&after={$today}&order=asc&orderby=date&per_page=1&status=future");

if ($events === null || empty($events)) {
    $output = [
        'has_fixture' => false,
        'last_updated' => date('c'),
    ];
    file_put_contents(OUTPUT_FILE, 'var fixtureData = ' . json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . "\n");
    fwrite(STDERR, "No upcoming fixture found for Boleskine.\n");
    exit(0);
}

$event = $events[0];

// Determine home / away teams
$teamsData = $event['teams'] ?? [];
$homeTeam = '';
$awayTeam = '';
$isHome = null;

$metaTeams = $event['meta']['sp_team'] ?? [];
$metaHome  = $event['meta']['sp_home'] ?? null;

foreach ($teamsData as $t) {
    if ($metaHome !== null && (int) $t['id'] === (int) $metaHome) {
        $homeTeam = $t['name'];
        $isHome = ((int) $t['id'] === $teamId);
    } elseif ($metaHome !== null && (int) $t['id'] !== (int) $metaHome) {
        $awayTeam = $t['name'];
    }
}

// Fallback if meta_home not set — infer from team IDs
if ($homeTeam === '' && count($teamsData) >= 2) {
    $homeTeam = $teamsData[0]['name'];
    $awayTeam = $teamsData[1]['name'];
    $isHome = ($teamsData[0]['id'] == $teamId);
}

// Raw date/time from API
$rawDate = $event['meta']['sp_date'] ?? '';
$rawTime = $event['meta']['sp_time'] ?? '';

// Venue from event meta
$venueName = '';
$venues = $event['venues'] ?? [];
if (!empty($venues)) {
    $venueName = $venues[0]['name'] ?? '';
}

// Format date
$dateFormatted = '';
$timeFormatted = '';
$locationFormatted = '';

if ($rawDate) {
    $ts = strtotime($rawDate);
    $dayName = date('l', $ts);
    $monthName = date('F', $ts);
    $dayNum = (int) date('j', $ts);
    $dateFormatted = $dayName . ', ' . $monthName . ' ' . ordinalSuffix($dayNum);
}

if ($rawTime) {
    $ts = strtotime($rawTime);
    $timeFormatted = date('g:i', $ts) . ' PM BST';
}

// Build location string
$wayLabel = $isHome ? '(H)' : '(A)';
if ($venueName) {
    $locationFormatted = abbreviateLocation($venueName . ' ' . $wayLabel);
} else {
    $locationFormatted = $wayLabel;
}

$output = [
    'has_fixture' => true,
    'home_team' => $homeTeam ?: 'Boleskine',
    'away_team' => $awayTeam ?: '',
    'date_formatted' => $dateFormatted,
    'time_formatted' => $timeFormatted,
    'location_formatted' => $locationFormatted,
    'way' => $isHome ? 'home' : 'away',
    'status' => $event['status'] ?? 'future',
    'last_updated' => date('c'),
];

file_put_contents(OUTPUT_FILE, 'var fixtureData = ' . json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . "\n");
echo "Fixtures updated successfully.\n";
