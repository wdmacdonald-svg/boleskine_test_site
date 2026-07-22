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
            '<span class="team-name font-cinzel">' + data.home_team + '</span>' +
            '<span class="vs">vs</span>' +
            '<span class="team-name font-cinzel">' + data.away_team + '</span>';
    }

    if (meta) {
        meta.innerHTML =
            '<p><i class="fa-regular fa-calendar-days"></i> ' + data.date_formatted + '</p>' +
            '<p><i class="fa-regular fa-clock"></i> ' + data.time_formatted + '</p>' +
            '<p><i class="fa-solid fa-location-dot"></i> ' + data.location_formatted + '</p>';
    }
});
