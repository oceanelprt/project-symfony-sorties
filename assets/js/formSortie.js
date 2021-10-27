import $ from "jquery";

$('#accordionVille').ready(function() {

    $('#collapseChoiceVille').click(function () {
        $('#sortie_choiceVille').val('choiceVille');
    })

    $('#collapseNewVille').click(function () {
        $('#sortie_choiceVille').val('choiceNewVille');

    })
})

$('#accordionLieu').ready(function() {
    $('#collapseChoiceLieu').click(function () {
        $('#sortie_choiceLieu').val('choiceLieu');
    })

    $('#collapseNewLieu').click(function () {
        $('#sortie_choiceLieu').val('choiceNewLieu');

    })
})