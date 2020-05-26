/**
 * Created by Alex on 20/03/2017.
 */


var hair_style;
var hair_color;
$(document).ready(function() {
/*
    $(function(){
        /*$('.uploadFile').change(function(e){
            $('.loader').show();
            $('.Preview').show();
            var filePath = URL.createObjectURL(e.target.files[0]);
            var fileName = e.target.files[0].name;
            //var fileName = $(this).val();
            $('.Preview img').attr('src',filePath);
            $('img').load(function(){
                $('.loader').hide();
            })
        });
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#profile-img").change(function(){
        readURL(this);
    });*/



    var MENU_URL = $('html').attr('MENU_URL');


        $(".gender-selector").on("click", function() {
            var $gender = $(this);
            var $input = $("input", $gender);
            $input.prop("checked", true);
            gender = $input.val();
            $(".gender-selector").removeClass("gender-selected");
            $gender.addClass("gender-selected")
        });

        $(".skin-color").on("click", function() {
            var $gender = $(this);
            var $input = $("input", $gender);
            $input.prop("checked", true);
            gender = $input.val();
            $(".skin-color").removeClass("skin-selected");
            $gender.addClass("skin-selected")
        });





    $('.selector-item').click(function() {

          var $gender = $(this);
          var $input = $("input", $gender);
          $input.prop("checked", true);

          $(this).closest('.row').find(".selector-item").removeClass("selector-selected");
          $gender.addClass("selector-selected")




            var updateId=$(this).attr('update-id');
          //alert(updateId);
            $(updateId).css('background-image','url('+$(this).attr('data-img')+')');
        });





    $('.hair-selector-item').click(function(e) {
       e.preventDefault();
        var $item = $(this);
        var $input = $("input", $item);
        $input.prop("checked", true);



        if($(this).hasClass('hair-style'))  {

            $(".hair-selector-item.hair-style").removeClass("hair-selector-selected");
            $item.addClass("hair-selector-selected");

        }

        if($(this).hasClass('hair-color')){
            $(".hair-selector-item.hair-color").removeClass("hair-selector-selected");
            $item.addClass("hair-selector-selected");



        }

        hair_style=$('.hair-selector-item.hair-style.hair-selector-selected').attr('url');
        hair_style_id=$('.hair-selector-item.hair-style.hair-selector-selected').attr('style-folder');
        hair_image=$('.hair-selector-item.hair-style.hair-selector-selected').attr('hair_image');

        hair_color=$('.hair-selector-item.hair-color.hair-selector-selected').attr('value');

        var hair_url =hair_style+'style'+hair_style_id+'/'+hair_color;


        $('#HairStyle-image').css('background-image','url('+hair_url+')');


    });


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