<?php
class Teams_View extends View {
   
   public function init()
   {
      parent::init();
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('person_add', $this->tr('Přidat osobu'), $this->link()->route('add'));
         $toolAdd->setIcon('user_add.png')->setTitle($this->tr("Přidat novou osobu"));
         $toolbox->addTool($toolAdd);
         
         if($this->teams != false){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('person_edit_order', $this->tr('upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon('arrow_up_down.png')->setTitle($this->tr("upravit pořadí osob"));
            $toolbox->addTool($toolOrder);
         }
         
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit text'), $this->link()->route('editText'));
         $toolEditText->setIcon(Template_Toolbox2::ICON_PAGE_EDIT)->setTitle($this->tr("Upravit úvodní text"));
         $toolbox->addTool($toolEditText);

         $this->toolbox = $toolbox;
         if(!empty($this->teams)){
            foreach($this->teams as $team){
               if(!empty($team['persons'])){
                  foreach($team['persons'] as $person){
                     $this->createPersonToolbox($person);
                  }
               }
            }
         }
      }
   }
   
   protected function createPersonToolbox(Model_ORM_Record $person)
   {
      $toolboxEdit = new Template_Toolbox2();
      $toolboxEdit->setIcon('user_edit.png');

      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('person_edit', $this->tr("Upravit osobu"));
      $toolEdit->setIcon('user_edit.png')->setTitle($this->tr('Upravit osobu'));
      $toolEdit->setAction($this->link()->route('edit', array('id' => $person->{Teams_Model_Persons::COLUMN_ID})));
      $toolboxEdit->addTool($toolEdit);

//      $toolEditPhoto = new Template_Toolbox2_Tool_PostRedirect('person_edit_photo', $this->tr("Upravit portrét"));
//      $toolEditPhoto->setIcon(Template_Toolbox2::ICON_USER)->setTitle($this->tr('Upravit portrét osoby'));
//      $toolEditPhoto->setAction($this->link()->route('editPhoto', array('id' => $person->{Teams_Model_Persons::COLUMN_ID})));
//      $toolboxEdit->addTool($toolEditPhoto);

      $this->formDelete->id->setValues($person->{Teams_Model_Persons::COLUMN_ID});
      $toolDelete = new Template_Toolbox2_Tool_Form( $this->formDelete);
      $toolDelete->setIcon('user_delete.png');
      $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat osobu?'));
      $toolboxEdit->addTool($toolDelete);

      $person->toolbox = clone $toolboxEdit;
      
      return $person;
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addFile("tpl://edit.phtml");
      $this->setTinyMCE($this->form->text, 'advanced');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->edit = true;
      $this->addView();
      Template_Module::setEdit(true);
   }
   
   public function editPhotoView() {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://editphoto.phtml');
   }

   public function editOrderView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_order.phtml');
   }

   public function editTextView() {
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://edittext.phtml');
      Template::setFullWidth(true);
   }
   
}
