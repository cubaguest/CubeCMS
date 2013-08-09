<?php
// přesun titulních obrázků do složky pro titulní obrázky
$model = new Actions_Model_List();
$actions = $model->getAllActionsWithCats();
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
