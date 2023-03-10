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


function displayBurgerMenu(){
    const menuNav = document.querySelector('#menu-navigation');
    const burger = document.querySelector('.burger');

    burger.addEventListener('click', (event) =>{
        menuNav.classList.toggle('show-nav');
    });
}

displayBurgerMenu();

                        /* DARK MODE */

/*De base, s'il n'y a rien stocké dans le localStorage, on dit que la clé "mode" correspond à la string "light" = on arrive
directement sur le light mode */ 
const mode = localStorage.getItem('mode');
if(!mode){
    localStorage.setItem("mode","light");
}

// Quand l'évènement onClick a lieu, cette fonction est executée
function changeMode(){
//On regarde ce qui est stocké dans le localStorage
const currentMode = localStorage.getItem("mode");
// Si on est sur le mode "light", on passe à "dark" et inversement
const newMode = currentMode === "dark" ? "light" : "dark";

    /* On sélectionne d'abord le body, comme il contient tous les éléments */
    var element = document.body;
    /* On fait basculer la classe du body à dark mode lorsque onclick est détecté sur le bouton
    En CSS, on a juste à ajouter .dark-mode devant l'élément ciblé à l'intérieur du body pour afficher un certain style quand
    le dark mode est activé */
    element.classList.toggle("dark-mode");       
    
// Mise à jour du localStorage avec la variable newMode qui détecte dans quel mode on est
localStorage.setItem("mode",newMode);    

}   
// console.log(localStorage);

// En sortie de la fonction, si le mode du localStorage est "dark", on bascule sur la classe dark-mode (css) et on la laisse
if(localStorage.getItem("mode")=="dark"){
    document.body.classList.toggle("dark-mode");
    // console.log("dark mode activé");
    // Si on est sur le light mode...
}
// else{
//     console.log("light mode");
// }
