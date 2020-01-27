$(function() {

    // player god carousel config
    $('#godCarousel').carousel({
        interval: false,
        wrap: false
    });

    $('.carousel .carousel-item').each(function(){
        var minPerSlide = 4;
        var next = $(this).next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        for (var i=0;i<minPerSlide;i++) {
            next=next.next();
            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }
    });

    // Update a player via the player API
    $(document).on('click', '#player-refresh', function () {
        $(this).attr("disabled", true);
        $(this).html('<div class="spinner-border spinner-border-sm" role="status"></div>');
        $.get("/api/player/update/" + smitePlayerId, function() {
            location.reload();
        }).fail(function() {
            $("#player-refresh").html('Refresh Player').removeAttr("disabled");
        });
    })

});