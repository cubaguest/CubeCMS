#!/bin/bash
if [ -z "$1" ]; then
   echo "Nebyl předán název modulu!";
   echo "Použití: \"create_module.sh nazevmodulu\" nebo \"create_module.sh nazevmodulu rozsirenymodul\" ";
   exit 1;
fi

if [ -d "./$1" ]; then
   echo "Tento modul již existuje!";
   # debug
   rm -Rif ./$1;
   echo "Smazán!";
   #exit 1;
fi

TPL_MODULE='module_template';
TPL_MODULE_EXTEND='module_template_extend';

module=$1;

echo "Vytvářím modul ${module^}";

module_ext="";

if [ -n "$2" ]; then
   module_ext=$2;
   tpl_path=$TPL_MODULE_EXTEND;
   echo "Modul rozšířen z ${module_ext^}";
else
   tpl_path=$TPL_MODULE;
fi

# kopie z šablony
cp -r ./$tpl_path ./${module};

# pokud se rozšiřuje
if [ -n "$module_ext" ]; then
   # nahrazení názvu rozšiřujícího modulu
   find ./${module}/ -type f -exec sed -i "s/MODULEEXTEND_L/${module_ext}/g" '{}' \;
   find ./${module}/ -type f -exec sed -i "s/MODULEEXTEND/${module_ext^}/g" '{}' \;
fi

# nahrazení názvu modulu
find ./${module}/ -type f -exec sed -i "s/MODULE/${module^}/g" '{}' \;

# nastavení na uživatelský modul
rm ./${module}/docs/admin;

echo "Modul $1 vytvořen;";
exit 0;