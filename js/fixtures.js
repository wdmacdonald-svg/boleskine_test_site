document.addEventListener('DOMContentLoaded', () => {
    var data = window.fixtureData;
    if (!data) return;

    var card = document.querySelector('#fixtures .news-card:first-child');
    if (!card) return;

    var badge = card.querySelector('.card-type');
    var teams = card.querySelector('.fixture-teams');
    var meta = card.querySelector('.fixture-meta');

    if (!data.has_fixture) {
        badge.textContent = 'No Game';
        if (teams) {
            teams.innerHTML = '<span class="team-name font-cinzel">No game this week</span>';
        }
        if (meta) meta.style.display = 'none';
        return;
    }

    if (teams) {
        teams.innerHTML =
            '<div class="team-row home-row">' +
            '<span class="team-name font-cinzel">' + data.home_team + '</span>' +
            '</div>' +
            '<span class="vs font-cinzel">V</span>' +
            '<div class="team-row away-row">' +
            '<span class="team-name font-cinzel">' + data.away_team + '</span>' +
            '</div>';
    }

    if (meta) {
        meta.innerHTML =
            '<p><i class="fa-regular fa-calendar-days"></i> ' + data.date_formatted + '</p>' +
            '<p><i class="fa-regular fa-clock"></i> ' + data.time_formatted + '</p>' +
            '<p><i class="fa-solid fa-location-dot"></i> ' + data.location_formatted + '</p>';
    }
});
