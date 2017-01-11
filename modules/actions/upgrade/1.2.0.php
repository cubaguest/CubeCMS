<?php
// přesun titulních obrázků do složky pro titulní obrázky

$dbc = Db_PDO::getInstance();
$dbst = $dbc->prepare("SELECT "
   ." act.".Actions_Model_Detail::COLUMN_URLKEY.'_'.Locales::getDefaultLang().' AS actkey,'
   ." act.".Actions_Model_Detail::COLUMN_IMAGE.' AS img,'
   ." cat.".Model_Category::COLUMN_URLKEY.'_'.Locales::getDefaultLang().' AS catkey'

   ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE).' AS act'
   ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON act.".Actions_Model_Detail::COLUMN_ID_CAT." = cat.".Model_Category::COLUMN_CAT_ID);
$dbst->setFetchMode(PDO::FETCH_OBJ);
$dbst->execute();
$actions = $dbst->fetchAll();

$tDir = new Filesystem_Dir(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR.DIRECTORY_SEPARATOR);
$tDir->checkDir();
foreach ($actions as $action) {
   $path = AppCore::getAppDataDir().$action->catkey.DIRECTORY_SEPARATOR.$action->actkey.DIRECTORY_SEPARATOR;
   if($action->img != null && is_file($path.$action->img)){
      if(!@rename($path.$action->img, $tDir.$action->img)){
         @copy($path.$action->img, $tDir.$action->img);
      }
   }
}
