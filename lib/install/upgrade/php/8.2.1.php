<?php
// sloučení titulních obrázků kategorie s titulnímí obrázky akcí, článků a podobně
if(CUBE_CMS_SUB_SITE_DOMAIN == null){
   Model_ConfigGlobal::setValue('PRIMARY_DOMAIN', $_SERVER['HTTP_HOST'], Model_ConfigGlobal::TYPE_STRING, 2);
}