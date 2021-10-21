import $ from 'jquery';

// const $ = require('jquery');

//global.$ = global.jQuery = $;


$('#inscription').ready(function () {

    $('#inscription').click(function(){
        var idSortie = $(this).val();
        $.ajax("sortie/" + idSortie + "/register", {
            data: {

            },
            success: function(data) {
               $("#inscription").prop("id", "desistement").prop("name", "desistement").html("Se désister");
            },
            error: function() {
                alert("Inscription impossible")
            }
        });
        return false; // this stops normal button behaviour from executing;

    });
});

$('#desistement').ready(function () {

    $('#desistement').click(function(){
        var idSortie = $(this).val();
        $.ajax("sortie/" + idSortie + "/unregister", {
            data: {

            },
            success: function(data) {
                $("#desistement").prop("id", "inscription").prop("name", "inscription").html("S'inscrire");
            },
            error: function() {
                alert("Désinscription impossible")
            }
        });
        return false; // this stops normal button behaviour from executing;

    });
});
