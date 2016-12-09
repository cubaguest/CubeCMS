<?php

/**
 * Hlavní konfigurační soubor vzhledu
 * Default face
 */
$face['name'] = "Bootstrap default template";
$face['desc'] = "Default Bootstrap CubeCMS tepmlate";
$face['version'] = "1.0";

$face['category_bg_image'] = false;
$face['category_title_image'] = true;

// jquery theme
$face['jquery_theme'] = "bootstrap";

$face['panels'] = array(
   'left' => 'Levý',
//   'left-hp' => 'Levý na HomePage',
   'right' => 'Pravý',
   'bottom' => 'Spodní',
);

/*
 * modules settings
 */
// banners
$modules['banners']['positions'] = array(
   'left' => array('label' => "Box v levo"),
   'right' => array('label' => "Box v pravo"),
   'bottom' => array('label' => "Box dole", 'random' => true),
);

$modules['custommenu']['positions'] = array(
   'bottom' => 'Spodní menu',
);

$modules['articles']['connectPhotogallery'] = true;

$modules['hpslideshow']['enabled'] = true;
$modules['hpslideshow']['wysiwyg'] = true;
$modules['hpslideshow']['dimensions'] = array(
   'width' => 1140,
   'height' => 250,
);

$modules['contact']['footer'] = true;

// základní nasatvení vzhledu
if (!function_exists('getFaceEnviromentItems')) {

   function getFaceEnviromentItems()
   {
      $items = array();

//      $imageHeader = new Form_Element_File('headerImage', 'Oprázek v hlavičce');
//      $imageHeader->addValidation(new Form_Validator_FileExtension('jpg'));
//      $imageHeader->setUploadDir(AppCore::getAppDataDir());
//      $items[] = $imageHeader;


      return $items;
   }

   function processFaceEnviroment(Form $form)
   {
//      if(isset($form->headerImage) && $form->headerImage->getValues() != null){
//         $img = $form->headerImage->createFileObject();
//         $img->rename('header.jpg');
//      }
   }

}

// nastavení kateogrie
if (!function_exists('extendCategorySettings')) {

   function extendCategorySettings(Category $category, Form $form, &$settings, Translator $translator)
   {
      // obrázek
      $elemImgR = new Form_Element_ImageSelector('rightImage', $translator->tr('Obrázek napravo'));
      $elemImgR->setUploadDir(Utils_CMS::getTitleImagePath(false));
      $form->addElement($elemImgR, Categories_Controller::SETTINGS_GROUP_IMAGES);

      if (isset($settings['cimgr'])) {
         $form->rightImage->setValues($settings['cimgr']);
      }
      
      // help box
      $elemColor = new Form_Element_Select('color', $translator->tr('Barva'));
      $elemColor->setOptions(array(
         'Žádná' => '',
         'Červená' => 'red',
         'Modrá' => 'blue',
      ));
      $form->addElement($elemColor, Categories_Controller::SETTINGS_GROUP_VIEW);

      if (isset($settings['bgcolor'])) {
         $form->color->setValues($settings['bgcolor']);
      }
      // uložení
      if ($form->isValid()) {
         if (isset($form->rightImage)) {
            $img = $form->rightImage->getValues();
            $settings['cimgr'] = $img['name'];
         }
         if (isset($form->color)) {
            $settings['bgcolor'] = $form->color->getValues();
         }
      }
   }

}