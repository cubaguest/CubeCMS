<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class CodeBook_Controller extends Controller {

   protected $modelName = false;
   protected $modelColumnID = false;
   protected $modelColumnName = false;
   protected $modelColumnIDCategory = false;

   

   public function init()
   {
       parent::init();
       
//       $this->modelName = 'KRVC_Model_CompanyTypes';
//       $this->modelColumnID = KRVC_Model_CompanyTypes::COLUMN_ID;
//       $this->modelColumnName = KRVC_Model_CompanyTypes::COLUMN_NAME;
   }

   public function mainController()
   {
      if(!$this->modelName || !$this->modelColumnID || !$this->modelColumnName){
         throw new InvalidArgumentException($this->tr('Modul není správně nastaven'));
      }
         
      $this->checkControllRights();
      $model = new $this->modelName();
      if($this->modelColumnIDCategory){
         $model->where($this->modelColumnIDCategory." = :idc", array('idc' => $this->category()->getId()));
      }
      $this->view()->items = $model->records();
      
      
      $this->view()->columnName = $this->modelColumnName;
      $this->view()->columnID = $this->modelColumnID;
      $this->view()->sorting = $model instanceof Model_ORM_Ordered;
      $this->processDelete();
   }
   
   protected function processDelete(){
      $f = new Form('item_delete_');
      $eId = new Form_Element_Hidden('id');
      $f->addElement($eId);
      $eSend = new Form_Element_Submit('send', $this->tr('Smazat položku'));
      $f->addElement($eSend);
      
      if($f->isValid()){
         $model = new $this->modelName();
         
         $model->delete($f->id->getValues());
         
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->route()->redirect();
      }
      
      $this->view()->formDelete = $f;
   }
   
   public function sortController()
   {
      $this->checkControllRights();
      $id = $this->getRequestParam('id');
      $position = $this->getRequestParam('position');
      
      if(!$id || !$position){
         throw new InvalidArgumentException($this->tr('Nebyly předány všechny potřebné argumenty'));
      }
      
      $model = new $this->modelName();
      /* @var $model Model_ORM_Ordered */
      /* @var $record Model_ORM_Ordered_Record */
      $record = $model->record($id);
      $record->setRecordPosition($position);
      $this->infoMsg()->addMessage($this->tr('Položka byla přesunuta'));
   }
       

   public function addController()
   {
      $this->checkControllRights();
      
      $form = $this->createItemForm();
      
      if($form->isValid()){
         $model = new $this->modelName();
         $item = $model->newRecord();
         $this->processItemForm($form, $item);

         $this->infoMsg()->addMessage($this->tr('Položka byla uložena'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
   }
   
   public function editController()
   {
      $this->checkControllRights();
      
      $model = new $this->modelName();
      $item = $model->record($this->getRequest('id'));
      
      if(!$item){
         throw new UnexpectedPageException();
      }
      
      $form = $this->createItemForm($item);
      
      if($form->isValid()){
         $this->processItemForm($form, $item);
         $this->infoMsg()->addMessage($this->tr('Položka byla uložena'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
      $this->view()->itemName = $item->{$this->modelColumnName};
      $this->view()->itemID = $item->{$this->modelColumnID};
   }
   
   protected function createItemForm(Model_ORM_Record $item = null)
   {
      $f = new Form('company_type_');
      
      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eName);
      
      $eSend = new Form_Element_SaveCancel('send');
      $f->addElement($eSend);
      
      if($item){
         $f->name->setValues($item->{$this->modelColumnName});
      }
      
      if($f->isSend() && $f->send->getValues() == false){
         $this->link()->route()->redirect();
      }
      return $f;
   }
   
   protected function processItemForm(Form $form, Model_ORM_Record $item)
   {
      if($this->modelColumnIDCategory){
         $item->{$this->modelColumnIDCategory} = $this->category()->getId();
      }
      $item->{$this->modelColumnName} = $form->name->getValues();
      $item->save();
      
      return $item;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
