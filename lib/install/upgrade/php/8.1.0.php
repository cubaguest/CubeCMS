<?php
// sloučení titulních obrázků kategorie s titulnímí obrázky akcí, článků a podobně

$dirBase = AppCore::getAppDataDir().'categories'.DIRECTORY_SEPARATOR;
$dirTarget = AppCore::getAppDataDir();
// přesun všech ze složky icons do title-images

$dirIterator = new DirectoryIterator($dirBase.'icons');
foreach ($dirIterator as $item) {
   if($item->isDir() OR $item->isDot()) {
      continue;
   }
   // orpavdu tady?
   $file = new File($item->getFilename(), $item->getPath());
   $file->move($dirTarget.CUBE_CMS_ARTICLE_TITLE_IMG_DIR, false);
//   echo 'moving '.$file.' to '.$dirTarget.CUBE_CMS_ARTICLE_TITLE_IMG_DIR.'<br />';
}
rmdir($dirBase.'icons');

$dirIterator2 = new DirectoryIterator($dirBase);
foreach ($dirIterator2 as $item) {
   if($item->isDir() && !$item->isDot()) {
      rename($dirBase.$item->getFilename(), $dirTarget.$item->getFilename());
   }
}
rmdir($dirBase);
