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

$('#sortie_ville').ready(function() {
    var $ville = $('#sortie_ville');
    $ville.change(function() {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$ville.attr('name')] = $ville.val();
        // Submit data via AJAX to the form's action path.
        if($ville.val()) {
            $.ajax({
                url : "/listeLieux/" + $ville.val(),
                data : data,
                success: function(lieux) {
                    $('#sortie_lieu').empty();

                    for (const lieu of lieux) {
                        var o = new Option(lieu.nom, lieu.id);
                        /// jquerify the DOM object 'o' so we can use the html method
                        $("#sortie_lieu").append(o);
                    }
                }
            });
        }
    });
})