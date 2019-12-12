$(function() {

    // Update a player via the player API
    $(document).on('click', '#player-refresh', function () {

        $.get("/api/player/update/" + smitePlayerId, function () {
            location.reload();
        });
    })

});