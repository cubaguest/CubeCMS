#!/bin/bash
# export knihoven, modulů a šablon do zadaného projektu

if [ "$#" -ne 2 ]; then
   echo "Použití: $0 /cesta/do/repozitáře /cesta/do/projektu";
   exit;
fi

if [ -d "$2" ]; then
   echo "Projekt DIR OK";
else
   echo "Adresář projektu neexistuje";
   exit;
fi

if [ -d "$1" ]; then
   echo "Repo DIR OK";
else
   echo "Adresář repozitáře neexistuje";
   exit;
fi

for dir in /modules /lib /templates /docs /fonts /jscripts /app.php; do
   #ls $1$dir;
   svn export $1$dir $2$dir --force;
   echo "Export $dir kompletní";
done

echo "Exportováno";
