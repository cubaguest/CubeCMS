<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class Navigation_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';
   const DEFAUL_NESTED_LEVEL = 1;

   const PARAM_NESTED_LEVEL = 'allow_private';
   const PARAM_IGNORE_IDS = 'igids';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_ALLOW_SCRIPT_IN_TEXT = 'allow_script';
   const PARAM_TPL_MAIN = 'tplmain';
   const PARAM_TPL_PANEL = 'tplpanel';

   private $ignoreCats = false;

   public function init()
   {
      parent::init();
      $this->actionsLabels = array(
          'main' => $this->tr('Rozcestník')
      );
   }
   
   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController()
   {
      //    Kontrola práv
      $this->checkReadableRights();
      $menu = Category_Structure::getStructure();
      if ($menu != false) {
         $this->ignoreCats = explode(';', $this->category()->getParam(self::PARAM_IGNORE_IDS, null));
         $newMenu = $this->recursive($menu->getCategory($this->category()->getId()));
         $this->view()->structure = $newMenu;
      }

      // načtení textu
      $modelT = new Text_Model();
      $text = $modelT->getText($this->category()->getId(), self::TEXT_MAIN_KEY);
      if($text != false){
         $this->view()->text = (string)$text->{Text_Model::COLUMN_TEXT};
      }

   }

   private function recursive(Category_Structure $obj, $level = 1)
   {
      $retArr = array();
      foreach ($obj->getChildrens() as $child) {
         if($this->ignoreCats && in_array($child->getId(), $this->ignoreCats)){
            continue;
         }
         $ar = array(
            'name' => $child->getCatObj()->getName(),
            'label' => $child->getCatObj()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION},
            'obj' => $child->getCatObj()->getCatDataObj(),
            'category' => $child->getCatObj(),
            'link' => $this->link(true)->category($child->getCatObj()->getUrlKey()),
            'childs' => array(),
            'text' => null,
         );
         if($level == 1){
            $text = Text_Model::getText($child->getCatObj()->getId(), self::TEXT_MAIN_KEY);
            if($text){
               $ar['text'] = $text->{Text_Model::COLUMN_TEXT};
            }
         }   
            
         if (!$child->isEmpty() AND ($level + 1 <= $this->category()->getParam(self::PARAM_NESTED_LEVEL, self::DEFAUL_NESTED_LEVEL))) {
            $ar['childs'] = $this->recursive($child, $level + 1);
         }
         array_push($retArr, $ar);
      }
      return $retArr;
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editTextController()
   {
      $this->checkWritebleRights();

      $form = new Form("text_");

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $textarea->setSubLabel('Řetězec "{LIST}" je nahrazen seznamem podstránek');
      $form->addElement($textarea);

      $model = new Text_Model();
      $text = $model->getText($this->category()->getId(), self::TEXT_MAIN_KEY);
      if ($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if ($form->isSend() AND $form->send->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if ($form->isValid()) {
         // odtranění script, nebezpečných tagů a komentřů
         $text = vve_strip_html_comment($form->text->getValues());
         foreach ($text as $lang => $t) {
            $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
         }

         $model->saveText($text, null, $this->category()->getId(), self::TEXT_MAIN_KEY);
         $this->log('úprava textu');
         $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
         $this->link()->route()->reload();
      }
      // view
      $this->view()->template()->form = $form;
   }

   public function settings(&$settings, Form &$form)
   {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $eLevel = new Form_Element_Text('level', $this->tr('Zanoření'));
      $eLevel->setSubLabel(sprintf($this->tr("Výchozí zanoření: %s"), self::DEFAUL_NESTED_LEVEL));
      $eLevel->addValidation(new Form_Validator_IsNumber());

      $form->addElement($eLevel, $fGrpViewSet);
      if (isset($settings[self::PARAM_NESTED_LEVEL])) {
         $form->level->setValues($settings[self::PARAM_NESTED_LEVEL]);
      }

      $eIgnore = new Form_Element_Select('ignore', $this->tr('Nezobrazovat'));
      $cat = Category_Structure::getStructure()->getCategory($this->category()->getId());
      $arr = $this->catStructToArray($cat);
      $eIgnore->setOptions($arr);
      $eIgnore->setMultiple(true);

      $form->addElement($eIgnore, $fGrpViewSet);
      if (isset($settings[self::PARAM_IGNORE_IDS])) {
         $form->ignore->setValues( is_array($settings[self::PARAM_IGNORE_IDS]) ? $settings[self::PARAM_IGNORE_IDS] 
             : explode(';', $settings[self::PARAM_IGNORE_IDS]));
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $ignoreValue = $form->ignore->getValues();
         $settings[self::PARAM_NESTED_LEVEL] = $form->level->getValues();
         $settings[self::PARAM_IGNORE_IDS] = ($ignoreValue != null && !empty($ignoreValue)) ? implode(';', $ignoreValue) : null;
      }
   }

   protected function catStructToArray(Category_Structure $cat, $returnArray = array(), $level = 1)
   {
      foreach($cat as $child){
         $returnArray[$child->getId()] = str_repeat('.', 3*($level-1)).$child->getCatObj()->getName(true);
         if(!$child->isEmpty()){
            $this->catStructToArray($child, $returnArray, $level+1);
         }
      }
      return $returnArray;
   }

}
