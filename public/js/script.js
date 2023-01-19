/* Enlever l'affichage d'un message flash de succès au bout de 3 secondes */

/* Le compteur est initialisé à 3. Donc l'action de bip() va se répéter chaque seconde durant 3 secondes */
var counter = 3;
var intervalAffichage = null;
var messages = document.getElementsByClassName("alert-success");

/* On bouvle sur chaque élément de la collection html (qui se présente en tableau) pour que chacun ait accès à cette fonction
et puisse disparaître dans le temps donné */
for(let i = 0; i < messages.length; i++){
    
    function removeAffichage(){
        /* On enlève le chargement d'interval, et on change la div de classe pour ne plus qu'elle s'affiche grâce au display none présent en CSS */
        clearInterval(intervalAffichage);
        messages[i].classList.toggle("removealert");  
    }

    function bip(){
        /* A chaque 1000 milisecondes, on répète bip, jusqu'à ce que counter == 0 */
        counter--;
        if(counter ==0){
            removeAffichage();
        }
    }

    /* Cette méthode se met à la fin, comme les autres méthodes auxquelles elle fait appel doivent s'exécuter = doivent exister */
    function setAffichage(){
        /* On met un interval en milisecondes (1000 milisecondes = 1 seconde). Pour pouvoir faire quelque chose à la fin de l'intervalle
        on a besoin de la function bip() qui possède un compteur */
        intervalAffichage = setInterval(bip, 1000);
    }

    /* On execute de suite la fonction, comme le message s'exécute au chargement de la page */
    setAffichage();

}


