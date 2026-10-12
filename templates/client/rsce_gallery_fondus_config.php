<?php

return array(
    'label' => array(
        'fr' => array('Galerie des Fondus', 'Galerie photo et vidéo au format carré 1:1 avec lightbox PhotoSwipe unifiée'),
    ),
    'types' => array('content'),
    'standardFields' => array('cssID', 'headline', 'space'),
    'fields' => array(
        'subheadline' => array(
            'label' => array(
                'fr' => array('Sous-titre / Accroche', 'Exemple : BUFFETS, ATELIERS, PLATEAUX... EN ACTION !'),
            ),
            'inputType' => 'text',
            'eval' => array('tl_class' => 'w100'),
        ),
        'images' => array(
            'label' => array(
                'fr' => array('Photos de la galerie', 'Sélectionnez les photos à intégrer dans la galerie'),
            ),
            'inputType' => 'fileTree',
            'eval' => array(
                'tl_class' => 'clr',
                'multiple' => true,
                'isSortable' => true,
                'isGallery' => true,
                'fieldType' => 'checkbox',
                'files' => true,
                'extensions' => implode(',', Contao\System::getContainer()->getParameter('contao.image.valid_extensions')),
            ),
        ),
        'videos' => array(
            'label' => array(
                'fr' => array('Vidéos de la galerie', 'Ajoutez les vidéos YouTube, Vimeo ou fichiers MP4'),
            ),
            'elementLabel' => 'Vidéo %s',
            'inputType' => 'list',
            'fields' => array(
                'videoUrl' => array(
                    'label' => array(
                        'fr' => array('Lien de la vidéo', 'Lien YouTube, Vimeo ou URL du fichier vidéo MP4'),
                    ),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'w50'),
                ),
                'poster' => array(
                    'label' => array(
                        'fr' => array('Vignette / Couverture', 'Image affichée dans la grille avec le badge Play'),
                    ),
                    'inputType' => 'fileTree',
                    'eval' => array(
                        'tl_class' => 'w50',
                        'files' => true,
                        'fieldType' => 'radio',
                        'extensions' => implode(',', Contao\System::getContainer()->getParameter('contao.image.valid_extensions')),
                    ),
                ),
                'title' => array(
                    'label' => array(
                        'fr' => array('Titre de la vidéo (optionnel)', ''),
                    ),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'w50'),
                ),
            ),
        ),
    ),
);
