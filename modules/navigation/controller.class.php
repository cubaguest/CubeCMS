<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class Navigation_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';
   const DEFAUL_NESTED_LEVEL = 2;

   const PARAM_NESTED_LEVEL = 'allow_private';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_ALLOW_SCRIPT_IN_TEXT = 'allow_script';
   const PARAM_TPL_MAIN = 'tplmain';
   const PARAM_TPL_PANEL = 'tplpanel';

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController()
   {
      //		Kontrola práv
      $this->checkReadableRights();
      $menu = Category_Structure::getStructure();
      if ($menu != false) {
         $catModel = new Model_Category();
         
         $menu->setCategories($catModel->getCategoryList());
         $childs = $menu->getCategory($this->category()->getId())->getChildrens();
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
//         $child = new Category_Structure(0);
         $ar = array(
            'name' => $child->getCatObj()->getName(),
            'link' => $this->link(true)->category($child->getCatObj()->getUrlKey()),
            'childs' => array(),
         );
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
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_NESTED_LEVEL] = $form->level->getValues();
      }
   }

}
?>