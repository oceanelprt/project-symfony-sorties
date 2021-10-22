import $ from 'jquery';


$('#inscription').ready(function () {

    $('#inscription').click(function(){
        var idSortie = $(this).val();
        $.ajax("sortie/" + idSortie + "/register", {
            data: {

            },
            success: function(data) {
                location.reload();
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

            success: function(data) {
                location.reload();
            },
            error: function() {
                alert("Désinscription impossible")
            }
        });
        return false; // this stops normal button behaviour from executing;

    });
});
