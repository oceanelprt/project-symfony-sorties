import $ from "jquery";

$('#importation_csv_importer').ready(function () {
    $('#importation_csv_importer').click(function(){
        $("#myMusic")[0].play();
        $(".loader").css("display", "block");
        $("#imgAttente").css("display", "block");
    });
});