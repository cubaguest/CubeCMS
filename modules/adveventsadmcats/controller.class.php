<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdvEventsAdmCats_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
      $this->checkControllRights();
   }

   public function mainController()
   {

      parent::mainController();

      $this->processDelete();

      // načtení 
      $model = new AdvEventsBase_Model_Categories();
      $compScroll = new Component_Scroll();
      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 20);
      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $this->view()->scroll = $compScroll;
//
      if($this->getRequestParam('filter', false)){
         $cats = AdvEventsBase_Model_Categories::getCategoriesByName($this->getRequestParam('filter'),
            $compScroll->getRecordsOnPage(), $compScroll->getStartRecord(), AdvEventsBase_Model_Categories::COLUMN_NAME);
      } else {
         $cats = AdvEventsBase_Model_Categories::getCategories($compScroll->getRecordsOnPage(), 
             $compScroll->getStartRecord(), AdvEventsBase_Model_Categories::COLUMN_NAME);
      }
//
      $this->view()->evcats = $cats;
   }

   public function addCategoryController()
   {
      $form = $this->createCategoryForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $cat = $this->processCategoryForm($form);
         $this->infoMsg()->addMessage($this->tr('Místo bylo uloženo'));
         Template_Core::closePopupWindow($cat->toArray());
         $this->link()->route()->redirect();
      }
      
      $this->view()->formEdit = $form;
   }
   
   public function editCategoryController($id)
   {
      $cat = AdvEventsBase_Model_Categories::getRecord($id);
      if(!$cat){
         throw new InvalidArgumentException($this->tr('Požadovaná kateogrie neexistuje'));
      }
      $form = $this->createCategoryForm($cat);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $this->processCategoryForm($form, $cat);
         $this->infoMsg()->addMessage($this->tr('Místo bylo uloženo'));
         $this->link()->route()->redirect();
      }

      $this->view()->evcat = $cat;
      $this->view()->formEdit = $form;
   }
   
   public function detailCategoryontroller($id)
   {
      $place = AdvEventsBase_Model_Categories::getRecord($id);
      if(!$place){
         throw new InvalidArgumentException($this->tr('Požadovaná kategorie neexistuje'));
      }
      
      $this->view()->evcat = $place;
   }
   
   /* obslužné metody */
   
   
   protected function createCategoryForm(Model_ORM_Record $cat = null)
   {
      $form = new Form('evcat_');

//      $grpBase = $form->addGroup('base', $this->tr('Základní parametry'));
//      $grpContacts = $form->addGroup('contacts', $this->tr('Kontakty'));
//      $grpOther = $form->addGroup('other', $this->tr('Ostatní'));
          
      
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elem = new Form_Element_Text('name', $this->tr('Název'));
      $elem->addValidation(new Form_Validator_NotEmpty());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem);
      
      if($cat){
         $form->name->setValues($cat->{AdvEventsBase_Model_Categories::COLUMN_NAME});
      }
      
      $elem = new Form_Element_SaveCancel('save');
      $form->addElement($elem);

      
      return $form;
   }
   
   protected function processCategoryForm(Form $form, Model_ORM_Record $cat = null)
   {
      if($cat == null){
         $cat = AdvEventsBase_Model_Categories::getNewRecord();
      }
      $cat->{AdvEventsBase_Model_Categories::COLUMN_NAME} = $form->name->getValues();
      $cat->save();
      return $cat;
   }

   protected function processDelete()
   {
      $form = new Form('cat_del');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new AdvEventsBase_Model_Categories();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Kateogire byla smazána'));
         $this->link()->redirect();
      }
      $this->view()->formDelete = $form;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
