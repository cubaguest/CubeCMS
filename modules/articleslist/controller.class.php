<?php
class ArticlesList_Controller extends Controller {
   const DEFAULT_ARTICLES_IN_PAGE = 5;
   const PARAM_CATEGORY_IDS = 'catsid';
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // načtení textu
      $text = $this->loadText();
      $this->view()->text = $text->{Text_Model_Detail::COLUMN_TEXT};
      // načtení článků
      $artModel = new Articles_Model_List();

      $articles = $artModel->getListByCats($this->category()->getParam(self::PARAM_CATEGORY_IDS,array(0)),
              $this->category()->getParam('scroll', self::DEFAULT_ARTICLES_IN_PAGE));

      $this->view()->articles = $articles;
   }

      public function editTextController() {
      $this->checkControllRights();
      $form = new Form('modlabel');

      $elemText = new Form_Element_TextArea('text', $this->_('Popis'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemS);

      if($form->isValid()) {
         $textM = new Text_Model_Detail();
         $textM->saveText($form->text->getValues(), null, $this->category()->getId());

         $this->infoMsg()->addMessage($this->_('Úvodní text byl uložen'));
         $this->link()->route()->reload();
      }

      // načtení textu
      $text = $this->loadText();
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $this->view()->form = $form;
   }

   private function loadText() {
      $textM = new Text_Model_Detail();
      $text = $textM->getText($this->category()->getId());
      return $text;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nasatvení');

      $elemScroll = new Form_Element_Text('scroll', 'Počet článků na stránku');
      $elemScroll->setSubLabel('Výchozí: '.self::DEFAULT_ARTICLES_IN_PAGE.' článků');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $catM = new Model_Category();
      $modules = array('articles', 'articleswgal', 'news');
      $results = array();
      foreach ($modules as $module) {
         $cats = $catM->getCategoryListByModule($module);
         while($cat = $cats->fetch()) {
            $results[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
         }
      }

      $elemSelectedCategories = new Form_Element_Select('catsid', 'Kategorie ze kterých se má výbírat');
      $elemSelectedCategories->setOptions($results);
      $elemSelectedCategories->setMultiple();
      $form->addElement($elemSelectedCategories,'basic');

      if(isset($settings[self::PARAM_CATEGORY_IDS])) {
         $form->catsid->setValues($settings[self::PARAM_CATEGORY_IDS]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings[self::PARAM_CATEGORY_IDS] = $form->catsid->getValues();
      }
   }
}
?>