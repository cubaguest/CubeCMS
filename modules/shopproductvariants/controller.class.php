<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopProductVariants_Controller extends Controller {
   
   protected function init()
   {
      $this->checkControllRights();
      $this->category()->getModule()->setDataDir('shop');
   }
   
   public function mainController() 
   {
      //		Kontrola práv
      $modelGroups = new Shop_Model_AttributesGroups();
      $modelAttributes = new Shop_Model_Attributes();


      // form přidání skupiny
      $this->view()->formEditGroup = $this->formEditGroup();

      // form přidání atributu
      $this->view()->formEditVariant = $this->formEditVariant();
   }

   /**
    * Upravuje parametr
    * Parametr: action - (edit;delete;move)
    */
   public function editGroupController()
   {
      // mazání
      if($this->getRequestParam('action', false) == 'delete'
         && $this->getRequestParam('id', false) != null){
         (new Shop_Model_AttributesGroups())->delete($this->getRequestParam('id'));
         $this->infoMsg()->addMessage($this->tr('Skupina byla smazána'));
         $this->link()->rmParam('action')->rmParam('id')->redirect();
      }
      // přesun
      else if($this->getRequestParam('action', false) == 'changepos'
         && $this->getRequestParam('id', false) != null
         && $this->getRequestParam('pos', false) != null
      ){
         Shop_Model_AttributesGroups::changeOrder(
            $this->getRequestParam('id'),
            $this->getRequestParam('pos')
         );

         $this->infoMsg()->addMessage($this->tr('Skupina byla přesunuta na novou pozici'));
         $this->link()
            ->rmParam('action')
            ->rmParam('id')
            ->rmParam('pos')
            ->redirect();
      }

      // úprava a přidání
      $this->formEditGroup();
   }

   /**
    * Upravuje hodnotu parametru
    * Parametr: action - (edit;delete;move)
    */
   public function editVariantController()
   {
      // mazání
      if($this->getRequestParam('action', false) == 'delete'
         && $this->getRequestParam('id', false) != null){
         (new Shop_Model_Attributes())->delete($this->getRequestParam('id'));
         $this->infoMsg()->addMessage($this->tr('Vyrianta byla smazána'));
         $this->link()->rmParam('action')->rmParam('id')->redirect();
      }
      // přesun
      else if($this->getRequestParam('action', false) == 'changepos'
         && $this->getRequestParam('id', false) != null
         && $this->getRequestParam('pos', false) != null
      ){
         Shop_Model_Attributes::changeOrder(
            $this->getRequestParam('id'),
            $this->getRequestParam('pos')
         );

         $this->infoMsg()->addMessage($this->tr('Varianta byla přesunuta na novou pozici'));
         $this->link()
            ->rmParam('action')
            ->rmParam('id')
            ->rmParam('pos')
            ->redirect();
      }
      // úprava a přidání
      $this->formEditVariant();
   }

   /**
    * Varcí seznam skupin
    */
   public function groupsListController()
   {
      $this->view()->groups = (new Shop_Model_AttributesGroups())
         ->order(Shop_Model_AttributesGroups::COLUMN_ORDER)
         ->records();
   }

   /**
    * Vrací seznam hodnot dané skupiny
    */
   public function variantsListController()
   {
      $idg = $this->getRequest('idg');

      $this->view()->variants = (new Shop_Model_Attributes())
         ->where(Shop_Model_Attributes::COLUMN_ID_GROUP." = :id",
         array('id' => $idg))
         ->order(Shop_Model_Attributes::COLUMN_ORDER)
         ->records();
   }

   /**
    * @param null $param
    * @return Form
    */
   protected function formEditGroup($group = null)
   {
      $form = new Form('group_edit_');

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setLangs();
      $elemName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $elemName->addFilter(new Form_Filter_HTMLSpecialChars());
      $form->addElement($elemName);

      $elemIdParam = new Form_Element_Hidden('id');
      $form->addElement($elemIdParam);

      $elemSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new Shop_Model_AttributesGroups();
         if($form->id->getValues() == null || !($group = $model->record($form->id->getValues()))){
            $group = $model->newRecord();
         }

         $group->{Shop_Model_AttributesGroups::COLUMN_NAME} = $form->name->getValues();
         $group->save();

         $this->infoMsg()->addMessage($this->tr('Skupiny byla uložena'));
         $this->link()->redirect();
      }
      return $form;
   }

   /**
    * @param null $value
    * @return Form
    */
   protected function formEditVariant($attr = null)
   {
      $form = new Form('variant_edit_');

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setLangs();
      $elemName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $elemName->addFilter(new Form_Filter_HTMLSpecialChars());
      $form->addElement($elemName);
      
      $elemCode = new Form_Element_Text('code', $this->tr('Kód'));
      $elemCode->addFilter(new Form_Filter_HTMLSpecialChars());
      $form->addElement($elemCode);

      $elemIdGroup = new Form_Element_Hidden('idGroup');
      $form->addElement($elemIdGroup);

      $elemIdValue = new Form_Element_Hidden('id');
      $form->addElement($elemIdValue);

      $elemSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new Shop_Model_Attributes();

         $attr = $model->newRecord();

         if($form->id->getValues() != null){
            $attr = $model->record($form->id->getValues());
         }
         $attr->{Shop_Model_Attributes::COLUMN_ID_GROUP} = $form->idGroup->getValues();
         $attr->{Shop_Model_Attributes::COLUMN_NAME} = $form->name->getValues();
         $attr->{Shop_Model_Attributes::COLUMN_CODE} = $form->code->getValues();

         $attr->save();

         $this->infoMsg()->addMessage($this->tr('Atribut byl uložen'));
         $this->link()->redirect();
      }

      return $form;
   }
}
