<?php


$model = new Projects_Model_Projects();
foreach (Locales::getAppLangs() as $lang) {
   if($lang == 'cs'){
      continue;
   }
   $model->updateLangColumns($lang);
}