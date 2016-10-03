<?php

$model = new People_Model();
foreach (Locales::getAppLangs() as $lang) {
   $model->updateLangColumns($lang);
}
