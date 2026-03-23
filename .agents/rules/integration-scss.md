---
trigger: always_on
---

Tu peux prendre connaissance des fichiers dans files/client/scss si nécessaire. 

Il faut prendre connaissance de files/client/scss/_main.scss car nous surchargeons essentiellement ce fichier.
On édite uniquement les fichiers files/client/scss/client.scss et files/client/scss/_variables_client.scss pour faire le style.
Le css utilise Bootstrap v5.0.2 files/client/scss/bootstrap et Compass files/client/scss/compass.

On utilise la class .is-animated qui permet d'injecter .not-in-view puis .in-view et .has-shown sur l'élément quand il a devient visible à l'écran.

C'est files/client/js/script.js qui contient le js du theme et il doit rester intact, on le surcharge éventuellement dans files/client/js/shared.js

