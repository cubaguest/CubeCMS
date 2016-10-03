<?php

$model = new FAQ_Model();
foreach (Locales::getAppLangs() as $lang) {
   $model->updateLangColumns($lang);
}
