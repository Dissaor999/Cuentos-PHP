
$(document).ready(function() {
    var flipbook = $('#flipbook');

    // Check if the CSS was already loaded



        flipbook.turn({
            display: "double",
            // Width
            width: 820,
            // Height
            height: document.getElementById('flipbook').offsetWidth/2,
            // Hardware acceleration
            acceleration: true,
            // Enables gradients
            gradients: true,
            // Auto center this flipbook
            autoCenter: false,
            angle:0,
        });



    $("#prev").click(function(e){
        e.preventDefault();
        flipbook.turn("previous");
        //$('.lazy').lazy({effectTime: 20 });
    });

    $("#next").click(function(e){
        e.preventDefault();
        flipbook.turn("next");
       /* $('.lazy').lazy({ effect: "fadeIn",
            effectTime: 200,
            visibleOnly: true,
            delay:0
        });*/
    });

   /* $('.lazy').lazy({
        effect: "fadeIn",
        effectTime: 200,
        visibleOnly: true,
        delay:0
    });*/

});