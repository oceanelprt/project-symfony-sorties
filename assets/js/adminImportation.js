import $ from "jquery";

$('#importation_csv_importer').ready(function () {
    $('#importation_csv_importer').click(function(){
        $(".loader").css("display", "block");
    });
});