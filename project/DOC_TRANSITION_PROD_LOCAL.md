# Protocole de Développement & Déploiement : Prod ➔ Local ➔ Prod

Ce document détaille la procédure pas-à-pas pour exporter la base de production, travailler sur le projet en local, et réintégrer les modifications en production sans risquer d'écraser les soumissions du formulaire de contact (`terminal42/contao-leads`).

---

## 1. Contexte & Tables sensibles

- **Extension formulaires** : `terminal42/contao-leads`
- **Tables contenant les messages reçus en production** :
  - `tl_lead` : métadonnées et identifiants des soumissions.
  - `tl_lead_data` : contenu réel des messages et coordonnées des utilisateurs.
- **Tables de configuration des formulaires (à conserver)** :
  - `tl_form`, `tl_form_field` (structure et champs des formulaires).

---

## 2. Phase 1 : Préparation & Export depuis la Production

### A. Sauvegarde intégrale de sécurité (Prod)
Création d'un backup complet de la base actuelle (conservé hors accès web sur le serveur) :
```bash
mysqldump -u fromton_prod -p fromton_prod | gzip > ~/backups/backup_full_prod_$(date +%Y%m%d_%H%M%S).sql.gz
```

### B. Export prêt pour le développement local (sans les messages)
1. **Export de l'ensemble de la base SAUF les données des leads** :
   ```bash
   mysqldump -u fromton_prod -p --ignore-table=fromton_prod.tl_lead --ignore-table=fromton_prod.tl_lead_data fromton_prod > dump_for_local_base.sql
   ```
2. **Export de la structure seule (`CREATE TABLE`) pour les tables de leads** :
   ```bash
   mysqldump -u fromton_prod -p --no-data fromton_prod tl_lead tl_lead_data > dump_for_local_leads_schema.sql
   ```
3. **Concaténation et compression pour import direct en local** :
   ```bash
   cat dump_for_local_base.sql dump_for_local_leads_schema.sql | gzip > dump_fromton_for_local.sql.gz
   rm dump_for_local_base.sql dump_for_local_leads_schema.sql
   ```

### C. Commit Git des modifications en cours sur la Prod
- Revue des fichiers modifiés (`config/config.yml`, `files/client/css/fixes.scss`, images).
- Ajout à Git et commit propre.
- Push sur le dépôt distant (uniquement sur demande / validation explicite).

---

## 3. Phase 2 : Développement en Local

### A. Initialisation en Local
1. Récupération des commits via `git pull`.
2. Décompression et import du dump :
   ```bash
   gunzip < dump_fromton_for_local.sql.gz | mysql -u <user_local> -p <db_local>
   ```
3. Compilation SCSS en local (selon règle WSL) :
   ```bash
   compass watch files/client/scss/
   ```

### B. Spécification de la fonctionnalité : "La galerie des fondus"

#### 1. Architecture des contenus (Contao)
- **Module d'actualités (News)** :
  - Utilisation d'une archive d'actualités dédiée (ex. `Galerie des fondus` ou catégories spécifiques : "Nos Prestations", "Nos Coulisses", "Vos Événements").
  - Chaque actualité correspond à un album/événement :
    - **Titre** : `headline` (ex. "NOS PRESTATIONS", "NOS COULISSES", "VOS ÉVÉNEMENTS").
    - **Sous-titre / description courte** : `subheadline` ou `teaser` (ex. "BUFFETS, ATELIERS, PLATEAUX... EN ACTION !").
    - **Lien Voir plus** : renvoie vers la page détaillée (`reader`) de l'actualité si > 12 médias ou pour voir tout l'événement.
    - **Éléments de contenu dans l'actualité** :
      - Des images (fichiers ou galeries).
      - Des vidéos (fichiers mp4 / YouTube / Vimeo avec poster / vignette).

#### 2. Rendu Visuel & Layout (cf. maquette)
- **Alternance de sections** : 
  - Section 1 : Fond blanc / beige clair avec typographie noire.
  - Section 2 : Fond noir / ardoise avec typographie blanche.
  - Section 3 : Fond blanc / beige clair.
- **Grille de médias (Cropped 1:1)** :
  - Disposées en grille carrée (crop ratio 1:1, par ex. 6 colonnes x 2 lignes = 12 médias max sur la liste).
  - Hover dynamique, micro-interactions, responsive (grille fluide passant à 4 colonnes sur tablette, 2 colonnes sur mobile).

#### 3. Gestion des Vidéos
- Affichage de la **vignette (poster)** au même format carré 1:1 que les photos.
- **Badge / Icône Custom Play** centré sur la miniature pour identifier immédiatement la vidéo.
- Au clic : ouverture directe dans la lightbox / visualiseur unifié.

#### 4. Visualiseur / Lightbox Unifié (Photos + Vidéos)
- Déjà en place sur le projet : **PhotoSwipe 5** (`files/js/photoswipe/` et template `rsce_gallery_justified.html5`).
- Support unifié : PhotoSwipe gère la navigation séquentielle fluide (Suivant / Précédent, swipe tactile, zoom).
- Pour les vidéos dans PhotoSwipe :
  - Intégration via HTML personnalisé d'élément (`data-pswp-type="video"` ou markup personnalisé vidéo HTML5 / iframe) pour pouvoir naviguer naturellement entre photos et vidéos sans changer de lecteur.
  - Alternative : **GLightbox** si PhotoSwipe s'avère trop rigide pour les flux vidéo externes (YouTube/Vimeo).

#### 5. Page Liste vs Page Détail (Reader)
- **Sur la liste des galeries** :
  - Limite stricte à **12 médias affichés par actualité**.
  - Si l'actualité contient plus de 12 médias : présence du bouton / lien **"VOIR PLUS →"**.
  - Clic sur une photo de la liste : ouvre quand même la lightbox.
- **Sur la page détaillée (News Reader)** :
  - Affiche l'ensemble de tous les médias de l'événement sans limitation à 12.

#### 6. Organisation des Assets & Règles SCSS (Spécifiques au projet)
- **Règles SCSS strictes** :
  - Uniquement éditer `files/client/scss/client.scss` et `files/client/scss/_variables_client.scss`.
  - Prendre appui sur `files/client/scss/_main.scss` (que l'on surcharge).
  - Utilisation de **Bootstrap v5.0.2** et **Compass**.
  - Respect de la charte couleur existante définie dans `_variables_client.scss` :
    - `$white: #f0ecec` (fond clair / beige caractéristique de la marque).
    - `$brand-primary: #272727` et `$brand-secondary: #231f20` (fonds sombres et textes).
    - Typographies : `Anton` pour les titres percutants, `Open Sans` pour le texte courant.
  - Breakpoints responsive :
    - Desktop : `$max-width: 1500px`.
    - Tablette : `$breakpoint-tablet: 1000px`.
    - Mobile : `$breakpoint-mobile: 700px`.
  - Animations : utiliser la classe `.is-animated` (qui gère `.not-in-view`, `.in-view` et `.has-shown`).
- **Règles JS** :
  - `files/client/js/script.js` doit impérativement **rester intact**.
  - Tout JS sur-mesure (initialisation de lightbox, calculs de grille ou gestion vidéo) s'écrit dans `files/client/js/shared.js`.
  - Librairies additionnelles déposées dans `files/client/js/` et `files/client/css/`.

#### 7. Templates Contao à concevoir en local
- `templates/client/news_gallery_list.html5` (ou surcharge de template news existant) pour la liste des 12 médias avec pagination / bouton "Voir plus".
- `templates/client/news_gallery_reader.html5` pour la vue complète non bridée de l'événement.
- Intégration de l'élément visuel Play SVG / icône pour les vignettes vidéos.

---

## 4. Phase 3 : Retour en Production (Déploiement Sécurisé)

### Rappel des règles d'or
- **Ne jamais écraser `tl_lead` ni `tl_lead_data` sur la prod.**
- **Toujours tester le dump sur une base locale de dev au préalable.**

### Scénarios de déploiement :
1. **Déploiement Code / Templates / SCSS** :
   - `git push` (local) ➔ `git pull` (prod).
   - Recompilation SCSS en prod : `ruby compass_fix.rb watch files/client/scss/`.
   - Vider le cache de production : `vendor/bin/contao-console cache:clear`.
2. **Déploiement Structure & Données BDD (News, Pages, Modules)** :
   - Si les pages/modules ont été créés en local :
     - **Option 1 (Idéale & la plus sûre)** : Re-créer les modules/pages/archives News dans le back-office de production directement, ce qui évite toute manipulation risquée de la base de données de production.
     - **Option 2 (Import SQL partiel)** : Exporter uniquement les tables modifiées (`tl_news`, `tl_news_archive`, `tl_page`, `tl_module`, `tl_content`) ou un dump avec `--ignore-table=fromton_prod.tl_lead --ignore-table=fromton_prod.tl_lead_data` en prenant soin de faire un backup préalable de la prod.

