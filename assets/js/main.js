$(function() {

    // Update a player via the player API
    $(document).on('click', '***REMOVED***player-refresh', function () {

        $.get("/api/player/update/" + smitePlayerId, function () {
            location.reload();
        });
    })

});