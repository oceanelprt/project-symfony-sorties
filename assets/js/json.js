import $ from 'jquery';

$('#ville-select').ready(function () {
    $('#ville-select').change(function() {
        let idVille = document.getElementById("ville-select").value;
        console.log(idVille);
        let select = document.getElementById("lieu-select");
        console.log(select);
        fetch("/listeLieux/" + idVille)
            .then(response => response.json())
            .then((lieux) => {
                select.innerHTML = "";
                for (const chaqueLieu of lieux) {
                    let option  = document.createElement("option");
                    option.innerText  = chaqueLieu.nom;
                    option.value = chaqueLieu.id;
                    select.appendChild(option);
                }
            })
    });
});

$('#btn-plus').ready(function () {
    $('#btn-plus').click(function() {
        var valeur = "";
        if(document.getElementById("btn-plus").value == "-")
        {
            valeur = true;
            document.getElementById("btn-plus").value = "+";
        }
        else
        {
            valeur = false;
            document.getElementById("btn-plus").value = "-";
        }

        document.getElementById("rue").hidden = valeur;
        document.getElementById("lbl-rue").hidden = valeur;
        document.getElementById("latitude").hidden = valeur;
        document.getElementById("lbl-latitude").hidden = valeur;
        document.getElementById("longitude").hidden = valeur;
        document.getElementById("lbl-longitude").hidden = valeur;
    });
});

$('#sortie_ville').ready(function() {
    var $sport = $('#sortie_ville');

    // When sport gets selected ...
    $sport.change(function () {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$sport.attr('name')] = $sport.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                // Replace current position field ...
                $('#meetup_position').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#meetup_position')
                );
                // Position field now displays the appropriate positions.
            }
        });
    });
})
