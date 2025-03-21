# Monde De Char

## Objectif

L'objectif principal de ce programme est de fournir des informations stratégiques en amont d'une bataille de bastion, en récupérant des données public (OSINT) ou presque.


## Installation

Seul PHP 5 ou supérrieur est requis, avec en extention php-json et php-curl.


## Utilisation

Pour l'instant le seul moyen d'utiliser ce script est en ligne de commande :

```bash
php src/cli.php [TAG [TAG [TAG [...] ] ] ]
```


## A venir
 - Sanitize input
 - Sanitize output
 - Custom list of flagged tank
 - Parralelisation requête
 - Optimistation vitesse utilisation API

## Note 

Ce projet utilise volontairement des "vielles technologies" et peu de librairie, afin d'être compatible au plus large, y compris avec des configurations sous Windows XP, et d'autres systèmes 32bit etc..
