<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class TrStaticsTexts_Controller extends Controller {
   private static $locales = array('cs_CZ' => 'cs_CZ', 'en_US' => 'en_US', 'sk_SK' => 'sk_SK', 'de_DE' => 'de_DE');


   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkControllRights();

      $formLoad = new Form('loader');

      $elemModule = new Form_Element_Text('module', $this->tr('Modul'));
      $elemModule->setSubLabel($this->tr('Není zadán jsou překládány knihovny'));
      $formLoad->addElement($elemModule);

      $elemFace = new Form_Element_Checkbox('face', $this->tr('Načíst z face'));
//      $elemFace->setValues(true);
      $formLoad->addElement($elemFace);

      $elemMerge = new Form_Element_Checkbox('merge', $this->tr('Sloučit s vytvořenými'));
      $elemMerge->setValues(true);
      $formLoad->addElement($elemMerge);

      $elemLocale = new Form_Element_Select('locale', $this->tr('Locales'));
      $elemLocale->setOptions(self::$locales);
      $formLoad->addElement($elemLocale);

      $elemLoad = new Form_Element_Submit('load', $this->tr('Načíst'));
      $formLoad->addElement($elemLoad);

      if ($formLoad->isValid()) {
         if($formLoad->module->getValues() != null AND $formLoad->module->getValues() != 'engine'){
            $link = $this->link()->route('translateModule', array('module' => $formLoad->module->getValues(), 'locale' => $formLoad->locale->getValues()));
         } else {
            $link = $this->link()->route('translateLibs', array('locale' => $formLoad->locale->getValues()));
         }
         if($formLoad->face->getValues() == true){
            $link->param('face', true);
         }
         if($formLoad->merge->getValues() == true){
            $link->param('merge', true);
         }
         $link->reload();
      }
      $this->view()->formLoad = $formLoad;
   }

   public function translateModuleController() {
      if($this->getRequestParam('face', false) == false){
         $directory = AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getRequest('module');
      } else {
         $directory = Template::faceDir().AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getRequest('module');
      }

      $ret = $this->getTrStringsFromDir($directory);
      $singulars = $ret[0];
      $plurals = $ret[1];
      if($this->getRequestParam('merge', true) == true){
         $moduleTrs = new Translator_Module($this->getRequest('module'));
         if($this->getRequestParam('face', false) == true){
            $moduleTrs->setLocale($this->getRequest('locale'));
            $moduleTrs->setLoadTarget(Translator::LOAD_LIB);
            // odstranění již existujících překladů
            $sLib = $moduleTrs->getSigulars();
            foreach ($singulars as $key => $value) {
               if(isset ($sLib[$key])){
                  unset ($singulars[$key]);
               }
            }

            $pLib = $moduleTrs->getPlurals();
            foreach ($plurals as $key => $value) {
               if(isset ($pLib[$key])){
                  unset ($plurals[$key]);
               }
            }
            $moduleTrs->setLoadTarget(Translator::LOAD_FACE);
            $singulars = array_merge($singulars, $moduleTrs->getSigulars());
         } else {
            $moduleTrs->setLocale($this->getRequest('locale'));
            $moduleTrs->setLoadTarget(Translator::LOAD_LIB);
            
            foreach ($moduleTrs->getSigulars() as $key => $translation) {
               if(isset ($singulars[$key])){
                  $singulars[$key][1] = $translation;
               } else if(isset ($singulars[md5($key)]) ){
                  $singulars[md5($key)][1] = $translation;
               }
            }
            
            foreach ($moduleTrs->getPlurals() as $key => $translation) {
               if(!isset ($plurals[md5($key)]) && !isset ($plurals[$key])){
                  $plurals[md5($key)] = array();
               }
               if(isset ($plurals[$key])){
                  $plurals[$key][1] = $translation;
               } else if(isset ($plurals[md5($key)]) ){
                  $plurals[md5($key)][1] = $translation;
               }
            }
         }
      }
      $this->view()->singulars = $singulars;
      $this->view()->plurals = $plurals;
      $this->view()->lang = $this->getRequest('locale', 'cs_CZ');
      $this->view()->module = $this->getRequest('module');


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

      if($formTranslations->isSend() AND $elemSave->getValues() == false){
         $this->link()->route()->reload();
      }

      if ($formTranslations->isValid()) {
         $str = $this->createTrString($this->view()->singulars, $formTranslations->trsingular->getValues(), $this->view()->plurals,
            $formTranslations->trplural1->getValues(), $formTranslations->trplural2->getValues(), $formTranslations->trplural3->getValues());
         if($this->getRequestParam('face', false) == false){
            file_put_contents(AppCore::getAppCacheDir().$this->getRequest('module').'_'.$this->getRequest('locale').'.php', $str);
         } else {
            file_put_contents(AppCore::getAppCacheDir().Template::face().'_'.$this->getRequest('module').'_'.$this->getRequest('locale').'.php', $str);
         }
         $this->infoMsg()->addMessage($this->tr('Překlad byl uložen do dočasného adresáře'));
         $this->link()->rmParam()->route()->reload();
      }
      $this->view()->formTr = $formTranslations;
   }

   public function translateLibsController() {
      $formTranslations = new Form('translation');

      $elemTrSingular = new Form_Element_TextArea('trsingular', $this->tr('Překlad'));
      $elemTrSingular->setDimensional();
      $formTranslations->addElement($elemTrSingular);

      $elemTrPlural1 = new Form_Element_TextArea('trplural1', $this->tr('Překlad pro 1'));
      $elemTrPlural1->setDimensional();
      $formTranslations->addElement($elemTrPlural1);

      $elemTrPlural2 = new Form_Element_TextArea('trplural2', $this->tr('Překlad pro 2 až 4'));
      $elemTrPlural2->setDimensional();
      $formTranslations->addElement($elemTrPlural2);

      $elemTrPlural3 = new Form_Element_TextArea('trplural3', $this->tr('Překlad pro 5 a více'));
      $elemTrPlural3->setDimensional();
      $formTranslations->addElement($elemTrPlural3);

      $elemSave = new Form_Element_SaveCancel('save');
      $formTranslations->addElement($elemSave);

      if($formTranslations->isSend() AND $elemSave->getValues() == false){
         $this->link()->route()->reload();
      }

      // načtení překladů
      $singulars = $plurals = $pluralsTrs = array();

      // překlad libs
      if ($this->getRequestParam('face', false) == false) {
         // CORE FILE
         $ret = $this->getTrStringsFromFile(AppCore::getAppLibDir() . 'app.php');
         $singulars = $ret[0];
         $plurals = $ret[1];

         // LIB DIR
         $ret = $this->getTrStringsFromDir(AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR);
         $singulars = array_merge($singulars, $ret[0]);
         $plurals = array_merge($plurals, $ret[1]);

         // TPLS DIR
         $ret = $this->getTrStringsFromDir(AppCore::getAppLibDir() . AppCore::ENGINE_TEMPLATE_DIR);
         $singulars = array_merge($singulars, $ret[0]);
         $plurals = array_merge($plurals, $ret[1]);
      } else {
         // překlad face

      }

      if($this->getRequestParam('merge', true) == true){
         $translator = new Translator();
         if($this->getRequestParam('face', false) == true){
            $translator->setLocale($this->getRequest('locale'));
            $translator->setLoadTarget(Translator::LOAD_LIB);
            // odstranění již existujících překladů
            $sLib = $translator->getSigulars();
            foreach ($singulars as $key => $value) {
               if(isset ($sLib[$key])){
                  unset ($singulars[$key]);
               }
            }

            $pLib = $translator->getPlurals();
            foreach ($plurals as $key => $value) {
               if(isset ($pLib[$key])){
                  unset ($plurals[$key]);
               }
            }
            $translator->setLoadTarget(Translator::LOAD_FACE);
            $pluralsTrs = $translator->getPlurals();
            $singulars = array_merge($singulars, $translator->getSigulars());
         } else {

            $translator->setLocale($this->getRequest('locale'));
            $translator->setLoadTarget(Translator::LOAD_LIB);
            
            foreach ($translator->getSigulars() as $key => $translation) {
               if(isset ($singulars[$key])){
                  $singulars[$key][1] = $translation;
               } else if(isset ($singulars[md5($key)]) ){
                  $singulars[md5($key)][1] = $translation;
               }
            }
            
            foreach ($translator->getPlurals() as $key => $translation) {
               if(!isset ($plurals[md5($key)]) && !isset ($plurals[$key])){
                  $plurals[md5($key)] = array();
               }
               if(isset ($plurals[$key])){
                  $plurals[$key][1] = $translation;
               } else if(isset ($plurals[md5($key)]) ){
                  $plurals[md5($key)][1] = $translation;
               }
            }
         }
      }
      $this->view()->singulars = $singulars;
      $this->view()->plurals = $plurals;
      $this->view()->lang = $this->getRequest('locale', 'cs_CZ');
      
      // uložení
      if ($formTranslations->isValid()) {
         $str = $this->createTrString($this->view()->singulars, $formTranslations->trsingular->getValues(), $this->view()->plurals,
            $formTranslations->trplural1->getValues(), $formTranslations->trplural2->getValues(), $formTranslations->trplural3->getValues());
         if($this->getRequestParam('face', false) == false){
            file_put_contents(AppCore::getAppCacheDir().$this->getRequest('locale').'.php', $str);
         } else {
            file_put_contents(AppCore::getAppCacheDir().Template::face().'_'.$this->getRequest('locale').'.php', $str);
         }
         $this->infoMsg()->addMessage($this->tr('Překlad byl uložen do dočasného adresáře'), false);
      }
      $this->view()->formTr = $formTranslations;
   }

   private function createTrStr($singulars, $originalS, $plurals, $originalP)
   {
      $str = "<?php\n";

      $str .= '$singular = '."\n";
//      $str .= var_export($singulars, true); // not show comments for original texts
      if(!empty ($singulars) ){
         $str .= "array(\n";
         foreach ($singulars as $orig => $trans) {
            // 'Smazat' => 'Delete',
            if($trans != $originalS[$orig][0] && $trans != null){
               $str .= "   '".$orig."' => '".  addcslashes($trans, "'")."', /* {$originalS[$orig][0]} */ \n";
            }
         }
         $str .= ")";
      }
      $str .= ";\n";

      $str .= '$plural'." = array(\n";
       /*'%s rok' => 
         array (
            0 => '%s year',
            1 => '%s years',
            2 => '%s years',
         ),*/
      if(!empty ($plurals) ){
         foreach ($plurals as $orig => $trans) {
            if($trans[1] != null){
               $str .= "   '".$orig."' => array( /* {$originalP[$orig][0][0]} */ \n";
               foreach ($trans as $key => $transI) {
                  $str .= "      ".(int)$key." => '". addcslashes($transI, "'")."',  \n";
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

   private function getTrStringsFromFile($file) {
      $tokens = token_get_all(file_get_contents($file));
      array_shift($tokens);

      $singulars = $plurals = array();

      for ($i = 0; $i < count($tokens); $i++) {
         // char token
         if (is_string($tokens[$i]) || $tokens[$i][0] == T_COMMENT) {
            continue;
         }
         // true, false and null are okay, too
         if ($tokens[$i][0] == T_STRING && $tokens[$i][1] == 'tr' AND $tokens[$i - 1][1] == '->') {
            if ($tokens[$i + 2][0] == T_ARRAY) {
               // plural
               if($tokens[$i+4][0] == T_CONSTANT_ENCAPSED_STRING){ // must be string, not variable
                  $pl = array(substr( stripcslashes( $tokens[$i + 4][1] ), 1, -1 ), substr( stripcslashes( $tokens[$i + 6][1] ), 1, -1 ) );
                  if ($tokens[$i + 8][0] == T_CONSTANT_ENCAPSED_STRING) {
                     array_push($pl, substr( stripcslashes($tokens[$i + 8][1]), 1, -1 ));
                  }
                  $plurals[md5($pl[0])] = array($pl, $pl);
               } 
            } else if($tokens[$i+2][0] == T_CONSTANT_ENCAPSED_STRING) {
               // singular
               $str = substr(substr($tokens[$i + 2][1], 1), 0, -1);
               $singulars[md5($str)] = array($str, stripcslashes($str) );
            }
         }
      }
      return array($singulars, $plurals);
   }

   private function getTrStringsFromDir($dir)
   {
      $dir = new RecursiveDirectoryIterator($dir);
      $iterator = new RecursiveIteratorIterator($dir);
      $regex = new RegexIterator($iterator, '/^.+\.(?:php|phtml)$/i', RecursiveRegexIterator::GET_MATCH);
      $singulars = $plurals = array();

      foreach ($regex as $item) {
         if(strpos($item[0], 'nonvve') !== false || strpos($item[0], 'Zend') !== false ){
            continue;
         }
         $ret = $this->getTrStringsFromFile($item[0]);
         $singulars = array_merge($singulars, $ret[0]);
         $plurals = array_merge($plurals, $ret[1]);
      }
      return array($singulars, $plurals);
   }

   private function createTrString($origSin, $sin, $origPls, $pls1, $pls2, $pls3) {
      $translatedSingulars = array();
      if ($sin != null) {
         foreach ($origSin as $strHash => $translation) {
            if(isset ($sin[$strHash])){
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