$(document).ready(function () {

    //Règle de gestion : l'age de la personne ne peut dépasser 149 ans
    $("#personne_date_naissance").on("focusout", function () {
        var date_naissance = $(this).val();
        var annee_naissance = date_naissance.substring(0, 4);
        var annee_naissance_number = parseInt(annee_naissance);

        var date_du_jour = new Date();
        var annee_actuelle = date_du_jour.getFullYear();
        var annee_actuelle_number = parseInt(annee_actuelle);

        if (annee_actuelle_number - annee_naissance_number > 149) {
            this.setCustomValidity("Vous ne pouvez pas vous inscrire si vous avez 150 ans ou plus...");
        }
        else {
            this.setCustomValidity("");
        }
    });

    //Ramener toutes les personnes
    $(".btn-primary").on("click", function () {
        $.ajax({
            url: '/affiche_toutes',
            type: 'GET',
            dataype: "json", //Optionnel
            success: function (data) {
                var data = data.slice(1, -1);
                var tableauDePersonnes = data.split(',');

                var liste_personnes = '';
                tableauDePersonnes.forEach(function (unePersonne) {
                    liste_personnes += '<li>' + unePersonne + '</li>';
                });
                $('#toutes_les-personnes').find('ul').html(liste_personnes);
            },
            error: function (xhr, status, error) {
                alert('L appel en GET a échoué...:' + xhr.responseText);
            }
        });
    });
});