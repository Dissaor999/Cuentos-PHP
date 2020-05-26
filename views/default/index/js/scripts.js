/**
 * Created by Alex on 20/03/2017.
 */
$(document).ready(function() {
    var names = [
        'Carlos',
        'Julia',
        'Sergio',
        'Laura',
        'Lucia',
        'Nathan',
        'Maria',
        'Juan',
        'Jaime',
    ];

    var names_counter=0;
    changeName();
    function changeName() {
        if(names_counter>=names.length) names_counter=0;

        $('.name-changer').attr('placeholder', 'Ejemplo: '+names[names_counter]);

        names_counter++;

        window.setTimeout(function() { changeName() }, 1500)
    }


});
