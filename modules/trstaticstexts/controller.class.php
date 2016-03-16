<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class TrStaticsTexts_Controller extends Controller {

   private static $locales = array('cs_CZ' => 'cs_CZ', 'en_US' => 'en_US', 'sk_SK' => 'sk_SK', 'de_DE' => 'de_DE');

   const TR_FILES_REGEX = '/^.+\.(?:php|phtml|tpl)$/i';


   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController()
   {
      //		Kontrola práv
      $this->checkControllRights();

      $formLoad = new Form('loader');


      $elemModule = new Form_Element_Select('module', $this->tr('Modul'));
      $elemModule->addOption($this->tr('jádro a základní vzhled'), '');

      $dir_iterator = new DirectoryIterator(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR);
      $options = array();
      foreach ($dir_iterator as $dir) {
         if (!$dir->isDot() && $dir->isDir()) {
            $name = $dir->getFilename();
            // load module info
            if(is_file($dir->getRealPath().DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'spicifikation.html')){
               $cnt = file_get_contents($dir->getRealPath().DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'spicifikation.html');
               $m = array();
               if(preg_match('/moduleName">([^<]+)/i', $cnt, $m)){
                  $name = $m[1].' (modul: '.$name.')';
               }
            }
            $options[$name] = $dir->getFilename();
         }
      }
      ksort($options, SORT_LOCALE_STRING);
      $elemModule->setOptions($options, true);
      $formLoad->addElement($elemModule);

      $elemLocale = new Form_Element_Select('locale', $this->tr('Locales'));
      foreach (Locales::getAppLangs() as $lang) {
         $loc = Locales::getSupportedLocale($lang);
         $elemLocale->addOption($loc['name'], $lang);
      }
      $formLoad->addElement($elemLocale);

      $elemLoad = new Form_Element_Submit('load', $this->tr('Načíst'));
      $formLoad->addElement($elemLoad);

      if ($formLoad->isValid()) {
         if ($formLoad->module->getValues() != null AND $formLoad->module->getValues() != 'engine') {
            $link = $this->link()->route('translateModule', array('module' => $formLoad->module->getValues(), 'locale' => $formLoad->locale->getValues()));
         } else {
            $link = $this->link()->route('translateLibs', array('locale' => $formLoad->locale->getValues()));
         }
         $link->reload();
      }
      $this->view()->formLoad = $formLoad;
   }

   public function translateModuleController($module, $locale)
   {
      $ret = $this->getTrStringsFromModule($module);
      $singulars = $ret[0];
      $plurals = $ret[1];

      $moduleTrs = new Translator_Module($this->getRequest('module'));
      $moduleTrs->setLocale($locale);
      $moduleTrs->setLoadTarget(Translator::LOAD_BOOTH);
//      var_dump(
//          $singulars, 
//          $moduleTrs->getSingulars()
//          $plurals
//          );

      foreach ($moduleTrs->getSingulars() as $key => $value) {
         if (isset($singulars[$key])) {
            $singulars[$key][1] = $value;
         }
      }

      foreach ($moduleTrs->getPlurals() as $key => $value) {
         if (isset($plurals[$key])) {
            $plurals[$key][1] = $value;
         }
      }

//      var_dump($moduleTrs->getPlurals(), $plurals );
//      die;

      $this->view()->singulars = $singulars;
      $this->view()->plurals = $plurals;
      $this->view()->lang = $locale;
      $this->view()->module = $module;

      $formTranslations = new Form('translation');

      $elemTrSingular = new Form_Element_TextArea('trsingular', $this->tr('Překlad'));
      $elemTrSingular->setDimensional();
      $formTranslations->addElement($elemTrSingular);

      $elemTrPlural1 = new Form_Element_TextArea('trplural1', $this->tr('Překlad do 1 (1)'));
      $elemTrPlural1->setDimensional();
      $formTranslations->addElement($elemTrPlural1);

      $elemTrPlural2 = new Form_Element_TextArea('trplural2', $this->tr('Překlad pro 2 až 4 (2 - 4)'));
      $elemTrPlural2->setDimensional();
      $formTranslations->addElement($elemTrPlural2);

      $elemTrPlural3 = new Form_Element_TextArea('trplural3', $this->tr('Překlad pro 5 a více (>= 5)'));
      $elemTrPlural3->setDimensional();
      $formTranslations->addElement($elemTrPlural3);

      $elemSave = new Form_Element_SaveCancel('save');
      $formTranslations->addElement($elemSave);

      if ($formTranslations->isSend() AND $elemSave->getValues() == false) {
         $this->link()->route()->reload();
      }

      if ($formTranslations->isValid()) {
         $str = $this->createTrString($this->view()->singulars, $formTranslations->trsingular->getValues(), $this->view()->plurals, $formTranslations->trplural1->getValues(), $formTranslations->trplural2->getValues(), $formTranslations->trplural3->getValues());

         // uložit překlad do adresáře vzhledu

         $faceModulePath = Face::getCurrent()->getDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . Locales::LOCALES_DIR . DIRECTORY_SEPARATOR;
         if (is_writable(Face::getCurrent()->getDir())) {
            FS_Dir::checkStatic($faceModulePath);
            $this->infoMsg()->addMessage($this->tr('Překlad byl uložen'));
            file_put_contents($faceModulePath . $locale . '.php', $str);
            $this->link()->reload();
         } else {
            $this->errMsg()->addMessage($this->tr('Není nastaveno oprávnění zápisu do adresáře vzhledu. Překlad je uložen do dočasného adresáře.'));
            file_put_contents(AppCore::getAppCacheDir() . $module . '_' . $locale . '.php', $str);
            $this->link()->reload();
         }
      }
      $this->view()->formTr = $formTranslations;
   }

   public function translateLibsController($locale)
   {
      $formTranslations = new Form('translation');

      $elemTrSingular = new Form_Element_TextArea('trsingular', $this->tr('Překlad'));
      $elemTrSingular->setDimensional();
      $formTranslations->addElement($elemTrSingular);

      $elemTrPlural1 = new Form_Element_TextArea('trplural1', $this->tr('Překlad pro 1 (1)'));
      $elemTrPlural1->setDimensional();
      $formTranslations->addElement($elemTrPlural1);

      $elemTrPlural2 = new Form_Element_TextArea('trplural2', $this->tr('Překlad pro 2 až 4 (2 - 4)'));
      $elemTrPlural2->setDimensional();
      $formTranslations->addElement($elemTrPlural2);

      $elemTrPlural3 = new Form_Element_TextArea('trplural3', $this->tr('Překlad pro 5 a více (>= 5)'));
      $elemTrPlural3->setDimensional();
      $formTranslations->addElement($elemTrPlural3);

      $elemSave = new Form_Element_SaveCancel('save');
      $formTranslations->addElement($elemSave);

      if ($formTranslations->isSend() AND $elemSave->getValues() == false) {
         $this->link()->route()->reload();
      }

      // načtení překladů
      $singulars = array();
      $plurals = array();
      $ret = $this->getTrStringsFromEngine();
      $singulars += $ret[0];
      $plurals += $ret[1];

      $translator = new Translator();
      $translator->setLocale($this->getRequest('locale'));
      $translator->setLoadTarget(Translator::LOAD_BOOTH);
      
      foreach ($translator->getSingulars() as $key => $value) {
         if (isset($singulars[$key])) {
            $singulars[$key][1] = $value;
         }
      }

      foreach ($translator->getPlurals() as $key => $value) {
         if (isset($plurals[$key])) {
            $plurals[$key][1] = $value;
         }
      }
  
      $this->view()->singulars = $singulars;
      $this->view()->plurals = $plurals;
      $this->view()->lang = $locale;

      // uložení
      if ($formTranslations->isValid()) {
         $str = $this->createTrString($this->view()->singulars, $formTranslations->trsingular->getValues(), $this->view()->plurals, $formTranslations->trplural1->getValues(), $formTranslations->trplural2->getValues(), $formTranslations->trplural3->getValues());
         if (is_writable(Face::getCurrent()->getDir())) {
            $faceLocalePath = Face::getCurrent()->getDir().Locales::LOCALES_DIR.DIRECTORY_SEPARATOR;
            FS_Dir::checkStatic($faceLocalePath);
            $this->infoMsg()->addMessage($this->tr('Překlad byl uložen'));
            file_put_contents($faceLocalePath . $locale . '.php', $str);
            $this->link()->reload();
         } else {
            $this->errMsg()->addMessage($this->tr('Není nastaveno oprávnění zápisu do adresáře vzhledu. Překlad je uložen do dočasného adresáře.'));
            file_put_contents(AppCore::getAppCacheDir() . $locale . '.php', $str);
            $this->link()->reload();
         }
//         if ($this->getRequestParam('face', false) == false) {
//            file_put_contents(AppCore::getAppCacheDir() . $this->getRequest('locale') . '.php', $str);
//         } else {
//            file_put_contents(AppCore::getAppCacheDir() . Template::face() . '_' . $this->getRequest('locale') . '.php', $str);
//         }
//         $this->infoMsg()->addMessage($this->tr('Překlad byl uložen do dočasného adresáře'), false);
      }
      $this->view()->formTr = $formTranslations;
   }

   private function createTrStr($singulars, $originalS, $plurals, $originalP)
   {
      $str = "<?php\n";

      $str .= '$singular = ' . "\n";
//      $str .= var_export($singulars, true); // not show comments for original texts
      if (!empty($singulars)) {
         $str .= "array(\n";
         foreach ($singulars as $orig => $trans) {
            // 'Smazat' => 'Delete',
            if ($trans != $originalS[$orig][0] && $trans != null) {
               $str .= "   '" . $orig . "' => '" . addcslashes($trans, "'") . "', /* {$originalS[$orig][0]} */ \n";
            }
         }
         $str .= ")";
      }
      $str .= ";\n";

      $str .= '$plural' . " = array(\n";
      /* '%s rok' => 
        array (
        0 => '%s year',
        1 => '%s years',
        2 => '%s years',
        ), */
      if (!empty($plurals)) {
         foreach ($plurals as $orig => $trans) {
            if ($trans[1] != null) {
               $str .= "   '" . $orig . "' => array( /* {$originalP[$orig][0][0]} */ \n";
               foreach ($trans as $key => $transI) {
                  $str .= "      " . (int) $key . " => '" . addcslashes($transI, "'") . "',  \n";
               }
               $str .= "   ),\n";
            }
         }
      }

      $str .= ")";
      $str .= ";\n";
      $str .= "?>\n";

      return $str;
   }

   private function getTrStringsFromFile($file)
   {
      $tokens = token_get_all(file_get_contents($file));
      array_shift($tokens);

      $singulars = $plurals = array();

      for ($i = 0; $i < count($tokens); $i++) {
         // char token
         if (is_array($tokens[$i]) && $tokens[$i][0] == T_DOC_COMMENT && strpos($tokens[$i][1], '@notranslate') !== false) {
            return false;
         }
         if (is_string($tokens[$i]) || $tokens[$i][0] == T_COMMENT) {
            continue;
         }
         // true, false and null are okay, too
         if ($tokens[$i][0] == T_STRING && $tokens[$i][1] == 'tr' AND $tokens[$i - 1][1] == '->') {
            if ($tokens[$i + 2][0] == T_ARRAY) {
               /**
                * @todo Tady nějak lépe dořešit překladový systém detekce uvnotř pole
                * Tohle funguje pro: (s mezerou v poli i bez)
                * $this->tr(array('Máte %i email','Máte %i emaily','Máte %i emailů'), 3);
                * $this->tr(array('Máte %i email', 'Máte %i emaily', 'Máte %i emailů'), 3);
                */
               $point1 = 4;
               $point2 = 6;
               $point3 = 8;
               // plural
               if ($tokens[$i + $point1][0] == T_CONSTANT_ENCAPSED_STRING) { // must be string, not variable
                  $pl = array(substr(stripcslashes($tokens[$i + $point1][1]), 1, -1));
                  if ($tokens[$i + $point2][0] == T_WHITESPACE) {
                     $point2++;
                     $point3++;
                  }
                  $pl[] = substr(stripcslashes($tokens[$i + $point2][1]), 1, -1);

                  if ($tokens[$i + $point3][0] == T_WHITESPACE) {
                     $point3++;
                  }
                  if ($tokens[$i + $point3][0] == T_CONSTANT_ENCAPSED_STRING) {
                     $pl[] = substr(stripcslashes($tokens[$i + $point3][1]), 1, -1);
                  }
                  $plurals[md5($pl[0])] = array($pl, $pl, basename($file), $file);
               }
            } else if ($tokens[$i + 2][0] == T_CONSTANT_ENCAPSED_STRING) {
               // singular
               $str = substr(substr($tokens[$i + 2][1], 1), 0, -1);
               $singulars[md5($str)] = array($str, stripcslashes($str), basename($file), $file);
            }
         }
      }
      return array($singulars, $plurals);
   }

   private function getTrStringsFromModule($module)
   {
      // připravení seznamu souborů pro překlady
      $dirCore = AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $module;
      $dirFace = Template::faceDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $module;

      $dir = new RecursiveDirectoryIterator($dirCore);
      $iterator = new RecursiveIteratorIterator($dir);
      $regex = new RegexIterator($iterator, '/^.+\.(?:php|phtml)$/i', RecursiveRegexIterator::GET_MATCH);

      $filesForSearch = array();
      // základní soubory
      foreach ($regex as $item) {
         $baseFile = str_replace($dirCore, null, $item[0]);
//         var_dump($baseFile,$item[0]);
         // pokud soubor je ve face, tak z jádra ho přeskoč
         if (is_file($dirFace . $baseFile)) {
            continue;
         }
         $filesForSearch[] = $item[0];
      }
      // soubory vzhledu
      if(is_dir($dirFace)){
         $dir = new RecursiveDirectoryIterator($dirFace);
         $iterator = new RecursiveIteratorIterator($dir);
         $regex = new RegexIterator($iterator, '/^.+\.(?:php|phtml)$/i', RecursiveRegexIterator::GET_MATCH);
         foreach ($regex as $item) {
            $filesForSearch[] = $item[0];
         }
      }

      // načtení překladů
      $singulars = array();
      $plurals = array();
      foreach ($filesForSearch as $filepath) {
         $ret = $this->getTrStringsFromFile($filepath);
         if ($ret && !empty($ret[0])) {
            $singulars += $ret[0];
            $plurals += $ret[1];
         }
      }
      return array($singulars, $plurals);
   }
   
   private function getTrStringsFromEngine()
   {
      // připravení seznamu souborů pro překlady
      $dirCore = AppCore::getAppLibDir();
      $dirWeb = AppCore::getAppWebDir();
      $dirFace = Template::faceDir();
      
      $filesForSearch = array($dirCore.'app.php');

      $dir = new RecursiveDirectoryIterator($dirCore.AppCore::ENGINE_LIB_DIR);
      $iterator = new RecursiveIteratorIterator($dir);
      $regex = new RegexIterator($iterator, self::TR_FILES_REGEX, RecursiveRegexIterator::GET_MATCH);

      // libs
      foreach ($regex as $item) {
         if(strpos($item[0], 'nonvve') !== false || strpos($item[0], 'Zend') !== false){
            continue;
         }
         $baseFile = str_replace($dirCore.AppCore::ENGINE_LIB_DIR, null, $item[0]);
         // pokud soubor je ve face, tak z jádra ho přeskoč
         if (is_file($dirFace . $baseFile)) {
            continue;
         }
         $filesForSearch[] = $item[0];
      }
      // templates
      $dir = new RecursiveDirectoryIterator($dirCore.AppCore::ENGINE_TEMPLATE_DIR);
      $iterator = new RecursiveIteratorIterator($dir);
      $regex = new RegexIterator($iterator, self::TR_FILES_REGEX, RecursiveRegexIterator::GET_MATCH);
      foreach ($regex as $item) {
         $baseFile = str_replace($dirCore.AppCore::ENGINE_TEMPLATE_DIR, null, $item[0]);
         // pokud soubor je ve face, tak z jádra ho přeskoč
         if (is_file($dirFace . AppCore::ENGINE_TEMPLATE_DIR . $baseFile) || 
             ( $dirWeb != $dirCore && is_file($dirWeb.AppCore::ENGINE_TEMPLATE_DIR . $baseFile) )) {
            continue;
         }
         $filesForSearch[] = $item[0];
      }
      
      // vzhled
      $dir = new RecursiveDirectoryIterator($dirFace.AppCore::ENGINE_TEMPLATE_DIR);
      $iterator = new RecursiveIteratorIterator($dir);
      $regex = new RegexIterator($iterator, self::TR_FILES_REGEX, RecursiveRegexIterator::GET_MATCH);
      foreach ($regex as $item) {
         $filesForSearch[] = $item[0];
      }
      
      // načtení překladů
      $singulars = array();
      $plurals = array();
      foreach ($filesForSearch as $filepath) {
         $ret = $this->getTrStringsFromFile($filepath);
         if ($ret && !empty($ret[0])) {
            $singulars += $ret[0];
            $plurals += $ret[1];
         }
      }
      return array($singulars, $plurals);
   }

   private function createTrString($origSin, $sin, $origPls, $pls1, $pls2, $pls3)
   {
      $translatedSingulars = array();
      if ($sin != null) {
         foreach ($origSin as $strHash => $translation) {
            if (isset($sin[$strHash])) {
               $translatedSingulars[$strHash] = $sin[$strHash];
            }
         }
      }

      $translatedPlurals = array();
      if ($pls1 != null) {
         foreach ($origPls as $strHash => $translation) {
            $translatedPlurals[$strHash] = array($pls1[$strHash], $pls2[$strHash]);
            if (isset($pls3[$strHash])) {
               array_push($translatedPlurals[$strHash], $pls3[$strHash]);
            }
         }
      }

      return $this->createTrStr($translatedSingulars, $origSin, $translatedPlurals, $origPls);
   }

}

?>