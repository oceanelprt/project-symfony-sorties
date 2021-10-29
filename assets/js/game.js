import $ from "jquery";
import konamiCode from "@sidp/konami-code";




konamiCode(() => {
    $('#btnGame').ready(function () {
        const myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'), {
            keyboard: false
        });
        myModal.show();
    });
});

var userChoice;
var computerChoice;

function compare(comChoice) {
    if (userChoice === comChoice) {
        return "L'ordinateur a également choisi " + computerChoice + ":" + "<br>" + "Egalité! :\|";
    } else if (userChoice === "pierre") {
        if (comChoice === "papier") {
            return "Le papier couvre la pierre <br> Tu as perdu :\(";
        } else if (comChoice === "ciseaux") {
            return "La pierre écrase les ciseaux. <br> Tu as gagné ! :\)";
        } else if (comChoice === "lézard") {
            return "La pierre écrase le lézard. <br> Tu as gagné ! :\) ";
        } else {
            return "Spock détruit la pierre.  <br> Tu as perdu :\(";
        }
    } else if (userChoice === "papier") {
        if (comChoice === "pierre") {
            return "Le papier enveloppe la pierre. <br> Tu as gagné ! :\)";
        } else if (comChoice === "ciseaux") {
            return "Les ciseaux coupent le papier. <br> Tu as perdu :\(";
        } else if (comChoice === "lézard") {
            return "Le lézard mange le papier.  <br> Tu as perdu :\(";
        } else {
            return "Le papier désavoue Spock.  <br> Tu as gagné ! :\)";
        }
    }else if (userChoice === "ciseaux") {
        if (comChoice === "pierre") {
            return "La pierre écrase les ciseaux.  <br> Tu as perdu :\(";
        } else if (comChoice === "papier") {
            return "Ciseaux\tLes ciseaux coupent le papier. <br> Tu as gagné ! :\)";
        } else if (comChoice === "lézard") {
            return "Les ciseaux décapitent le lézard.  <br> Tu as gagné ! :\)";
        } else {
            return "Spock fracasse les ciseaux. <br> Tu as perdu :\(";
        }
    } else if (userChoice === "lézard") {
        if (comChoice === "pierre") {
            return "La pierre écrase le lézard. <br> Tu as perdu :\(";
        } else if (comChoice === "papier") {
            return "Le lézard mange le papier.  <br> Tu as gagné ! :\)";
        } else if (comChoice === "ciseaux") {
            return "Les ciseaux décapitent le lézard.  <br> Tu as perdu :\(";
        } else {
            return "Le lézard empoisonne Spock. <br> Tu as gagné ! :\)";
        }
    } else if (userChoice === "spock") {
        if (comChoice === "pierre") {
            return "Spock détruit la pierre.  <br> Tu as gagné ! :\)";
        } else if (comChoice === "papier") {
            return "Le papier désavoue Spock.  <br> Tu as perdu :\(";
        } else if (comChoice === "ciseaux") {
            return "Spock fracasse les ciseaux. <br> Tu as gagné ! :\) ";
        } else {
            return "Le lézard empoisonne Spock. <br> Tu as perdu :\(";
        }
    }
}

$(document).ready(function(){
    $("button").click(function(){
        userChoice = this.id;
        computerChoice = Math.floor(Math.random() * 5);
        switch (computerChoice) {
            case 0: computerChoice = "pierre";
                break;
            case 1: computerChoice = "papier";
                break;
            case 2: computerChoice = "ciseaux";
                break;
            case 3: computerChoice = "lézard";
                break;
            case 4: computerChoice = "spock";
                break;
        }


        var result = compare(computerChoice).toUpperCase();

        $(".result").html("<h1>Résultat:</h1><p>Utilisateur: " + userChoice.toUpperCase() + "<br>" +
            "L'ordinateur: " + computerChoice.toUpperCase() + "</p>" + "<p>" + result + "</p>");
    });
});