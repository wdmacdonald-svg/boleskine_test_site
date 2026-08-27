document.addEventListener('DOMContentLoaded', () => {
    var data = window.fixtureData;
    if (!data) return;

    // --- 1. Update NEXT MATCH Card ---
    var fixCard = document.getElementById('next-match-card');
    if (fixCard && data.fixtures) {
        var fBadge = fixCard.querySelector('.card-type');
        var fTeams = fixCard.querySelector('.fixture-teams');
        var fMeta = fixCard.querySelector('.fixture-meta');

        if (!data.fixtures.has_fixture) {
            fBadge.textContent = 'No Game';
            if (fTeams) fTeams.innerHTML = '<span class="team-name font-cinzel">No game scheduled</span>';
            if (fMeta) fMeta.style.display = 'none';
        } else {
            if (fTeams) {
                fTeams.innerHTML =
                    '<div class="team-wrap home-wrap">' +
                    '<span class="team-name font-cinzel" title="' + data.fixtures.home_team + '">' + data.fixtures.home_team + '</span>' +
                    '</div>' +
                    '<span class="vs">vs</span>' +
                    '<div class="team-wrap away-wrap">' +
                    '<span class="team-name font-cinzel" title="' + data.fixtures.away_team + '">' + data.fixtures.away_team + '</span>' +
                    '</div>';
            }
            if (fMeta) {
                fMeta.innerHTML =
                    '<p><i class="fa-regular fa-calendar-days"></i> ' + data.fixtures.date_formatted + '</p>' +
                    '<p><i class="fa-regular fa-clock"></i> ' + data.fixtures.time_formatted + '</p>' +
                    '<p><i class="fa-solid fa-location-dot"></i> ' + data.fixtures.location_formatted + '</p>';
            }
        }
    }

    // --- 2. Update LATEST RESULTS Card ---
    var resCard = document.getElementById('latest-results-card');
    if (resCard && data.results) {
        var rBadge = resCard.querySelector('.card-type');
        var rTeams = resCard.querySelector('.fixture-teams');
        var rMeta = resCard.querySelector('.fixture-meta');

        if (!data.results.has_result) {
            rBadge.textContent = 'No Results';
            if (rTeams) rTeams.innerHTML = '<span class="team-name font-cinzel">No recent results</span>';
            if (rMeta) rMeta.style.display = 'none';
        } else {
            if (rTeams) {
                rTeams.innerHTML =
                    '<div class="team-wrap home-wrap">' +
                    '<span class="team-name font-cinzel" title="' + data.results.home_team + '">' + data.results.home_team + '</span>' +
                    '<span class="team-score font-cinzel">' + data.results.home_score + '</span>' +
                    '</div>' +
                    '<span class="vs">-</span>' +
                    '<div class="team-wrap away-wrap">' +
                    '<span class="team-score font-cinzel">' + data.results.away_score + '</span>' +
                    '<span class="team-name font-cinzel" title="' + data.results.away_team + '">' + data.results.away_team + '</span>' +
                    '</div>';
            }
            if (rMeta) {
                var scorersText = data.results.boleskine_scorers && data.results.boleskine_scorers.length > 0 
                    ? data.results.boleskine_scorers.join(', ') 
                    : 'None';
                rMeta.innerHTML =
                    '<p><i class="fa-regular fa-calendar-days"></i> ' + data.results.date_formatted + '</p>' +
                    '<p><i class="fa-solid fa-futbol"></i> Boleskine Scorers: ' + scorersText + '</p>';
            }
        }
    }
});
