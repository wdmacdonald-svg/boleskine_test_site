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
        CURLOPT_USERAGENT => 'Boleskine-Fixture-Fetcher/1.0',
    ]);
    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

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

// Step 2: Fetch upcoming events and filter for Boleskine
$today = date('Y-m-d\TH:i:s');
$events = json_get(API_BASE . "/events?after={$today}&order=asc&orderby=date&per_page=50");

if ($events === null || empty($events)) {
    $output = [
        'has_fixture' => false,
        'last_updated' => date('c'),
    ];
    file_put_contents(OUTPUT_FILE, 'var fixtureData = ' . json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . "\n");
    fwrite(STDERR, "No upcoming events found on remote API.\n");
    exit(0);
}

// Filter to only events that include Boleskine's team ID and are still upcoming
$boleskineEvents = array_filter($events, function ($e) use ($teamId, $today) {
    $teamIds = array_map('intval', $e['teams'] ?? []);
    $status = $e['status'] ?? '';
    $date = $e['date'] ?? '';
    return in_array($teamId, $teamIds) && $status === 'future' && $date >= $today;
});
$boleskineEvents = array_values($boleskineEvents);

if (empty($boleskineEvents)) {
    $output = [
        'has_fixture' => false,
        'last_updated' => date('c'),
    ];
    file_put_contents(OUTPUT_FILE, 'var fixtureData = ' . json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . "\n");
    fwrite(STDERR, "No upcoming fixture found for Boleskine.\n");
    exit(0);
}

$event = $boleskineEvents[0];

// The API returns team IDs in a flat array, not full objects.
// Fetch team names for the two teams in this event.
$eventTeamIds = array_map('intval', $event['teams'] ?? []);
$teamNames = [];
foreach ($eventTeamIds as $tid) {
    $teamInfo = json_get(API_BASE . "/teams/{$tid}");
    if ($teamInfo !== null) {
        $teamNames[$tid] = $teamInfo['title']['rendered'] ?? "Team {$tid}";
    } else {
        $teamNames[$tid] = "Team {$tid}";
    }
}

// Determine home / away — first team ID is home
$homeTeam = '';
$awayTeam = '';
$isHome = null;

if (count($eventTeamIds) >= 2) {
    $homeTeam = $teamNames[$eventTeamIds[0]] ?? '';
    $awayTeam = $teamNames[$eventTeamIds[1]] ?? '';
    $isHome = ($eventTeamIds[0] === $teamId);
} elseif (count($eventTeamIds) === 1) {
    $homeTeam = $teamNames[$eventTeamIds[0]] ?? '';
    $isHome = ($eventTeamIds[0] === $teamId);
}

// Venue from event — API returns venue IDs, fetch name
$venueName = '';
$eventVenueIds = $event['venues'] ?? [];
if (!empty($eventVenueIds)) {
    $venueInfo = json_get(API_BASE . "/venues/{$eventVenueIds[0]}");
    if ($venueInfo !== null) {
        $venueName = $venueInfo['name'] ?? $venueInfo['title']['rendered'] ?? '';
    }
}

// Date/time from top-level event fields
$rawDate = $event['date'] ?? '';

// Format date and time from the top-level date field
$dateFormatted = '';
$timeFormatted = '';

if ($rawDate) {
    $ts = strtotime($rawDate);
    $dayName = date('l', $ts);
    $monthName = date('F', $ts);
    $dayNum = (int) date('j', $ts);
    $dateFormatted = $dayName . ', ' . $monthName . ' ' . ordinalSuffix($dayNum);
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
