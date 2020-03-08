<?php

namespace App\Tool\Interfaces;

/**
 * CONSTANTES DES DIFFERENTS LIBELLÉS DES TYPES D'ACTIONS.
 */
Interface ICet{

    //CONSTANTES DES ETATS D'UN COMPTE CET
    const CET_OUVERTURE = "Ouverture CET";
    const CET_EPARGNER = "Epargner";
    const CET_PAYER = "Payer";
    const CET_CONGES = "Consommer en congés";
    const CET_RAFP = "Consommer en RAFP";

    //CONSTANTES DES ROLES D'UN COMPTE
    const CET_DSI = "DSI";
    const CET_GESTIONNAIRE = "Gestionnaire";

    //ROLES ATTRIBUES A UN TYPE DE COMPTE
    const CET_ROLE_DSI = [
        //MENU DISPONIBLE POUR CE RÔLE
        'ROLE_MENU_RECHERCHE',
        'ROLE_MENU_PERSONNEL',
        'ROLE_MENU_AFFECTATION',
        'ROLE_MENU_TYPE_CLOTURE',
        'ROLE_MENU_TYPE_ACTION',
        'ROLE_MENU_GRADE',
        'ROLE_MENU_IMPORTATION_DONNEES',
        'ROLE_MENU_DECONNEXION',
        //FONCTIONNALITES DISPONIBLES POUR CE RÔLE
        'ROLE_RECHERCHE',
        'ROLE_PERSONNEL',
        'ROLE_AFFECTATION',
        'ROLE_TYPE_CLOTURE',
        'ROLE_ACTION',
        'ROLE_TYPE_ACTION',
        'ROLE_GRADE',
        'ROLE_IMPORTATION_DONNEES',
        'ROLE_PDF',
        'ROLE_ECRITURE',
        'ROLE_ECRITURE_LDAP',
        'ROLE_LECTURE',
        'ROLE_SUPPRESSION',
        'ROLE_DSI'
    ];

    const CET_ROLE_GESTIONNAIRE = [
        //MENU DISPONIBLES POUR CE RÔLE
        'ROLE_MENU_RECHERCHE',
        'ROLE_MENU_PERSONNEL',
        'ROLE_MENU_DECONNEXION',
        //FONCTIONNALITES DISPONIBLES POUR CE RÔLE
        'ROLE_RECHERCHE',
        'ROLE_PERSONNEL',
        'ROLE_AFFECTATION',
        'ROLE_ACTION',
        'ROLE_GRADE',
        'ROLE_PDF',
        'ROLE_LECTURE',
        'ROLE_ECRITURE_LDAP'
    ];

}