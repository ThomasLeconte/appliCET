<?php

namespace App\Tool\Interfaces;

/**
 * CONSTANTES DES CHEMINS DE DESTINATIONS DES DIFFERENTS FICHIERS IMPORTÉS / EXPORTÉS
 */
Interface IDestination{

    //CHEMIN DU DOSSIER OU EST STOCKÉ LE FICHIER QUI EST IMPORTÉ PAR L'UTILISATEUR
    const IMPORT_CSV = '../temp/csv';

    //CHEMIN DU DOSSIER OU EST STOCKÉ LE FICHIER BILAN-CET (Export-Recherche.csv)
    const EXPORT_CSV = '../temp/export/';

}