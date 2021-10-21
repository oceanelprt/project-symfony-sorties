import $ from 'jquery';

// const $ = require('jquery');

//global.$ = global.jQuery = $;


$('#inscription').ready(function () {

    $('#inscription').click(function(){
        var idSortie = $(this).val();
        $.ajax("/sortie/" + idSortie + "/register", {
            data: {

            },
            success: function(data) {
                console.log("plop")
            },
            error: function() {
                // show alert or something
            }
        });
        return false; // this stops normal button behaviour from executing;

    });

});
