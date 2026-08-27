<?php
/**
 * Shinty Fixtures UI Tester
 * Run this script locally from your terminal to fetch data for any team
 * and update your local fixtures.js file for UI testing.
 * 
 * Usage: php test-team.php
 */

echo "====================================\n";
echo " Shinty Fixtures UI Tester\n";
echo "====================================\n\n";

echo "Enter team name to test (e.g. Kingussie, Lovat, Beauly): ";
$handle = fopen("php://stdin", "r");
$teamSearch = trim(fgets($handle));

if (empty($teamSearch)) {
    echo "No team entered. Exiting.\n";
    exit;
}

// Override the default team search constant before including the main script
define('TEAM_SEARCH', $teamSearch);

echo "\nFetching data for '$teamSearch' from Sportspress API...\n";

// Include the main script to fetch data and write to index_files/fixtures.js
include 'fetch-shinty-fixtures.php';

echo "\nDone! \nRefresh your local web page in the browser to see the UI cards for $teamSearch.\n";
