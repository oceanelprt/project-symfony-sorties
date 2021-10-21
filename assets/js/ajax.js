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
                $("#inscription").prop("id", "desistement").html("Se désister");

            },
            error: function() {
                console.log(this.error)
                alert("Inscription impossible")
            }
        });
        return false; // this stops normal button behaviour from executing;

    });

});
