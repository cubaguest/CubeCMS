<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class NavigationMenu_Controller extends Controller {
   public function mainController() {
      $this->checkReadableRights();

      if($this->rights()->isWritable()){
         // mazání
         $delForm = new Form('link_');
         $elemId = new Form_Element_Hidden('id');
         $delForm->addElement($elemId);

         $submit = new Form_Element_SubmitImage('remove');
         $delForm->addElement($submit);

         if($delForm->isValid()){
            $model = new NavigationMenu_Models_List();
            $item = $model->getItem($delForm->id->getValues());
            $file = new Filesystem_File($item->{NavigationMenu_Models_List::COL_ICON}, $this->getModule()->getDataDir());
            if($file->exist()){
               $file->remove();
            }

            $model->deleteItem($delForm->id->getValues());

            $this->infoMsg()->addMessage($this->_('Odkaz byl smazán'));
            $this->link()->reload();
         }
      }

      // nastavení viewru
      $this->view()->template()->addTplFile('list.phtml');
   }

   public function addController() {
      $this->checkWritebleRights();
      $form = $this->createForm();
      

      if($form->isValid()) {
         $image = null;
         if($form->icon->getValues() != null) {
            $image = $form->icon->createFileObject('Filesystem_File_Image');
            $image->setDimensions(16, 16);
            $image->save();
         }

         $model = new NavigationMenu_Models_List();
         $model->saveLink($form->name->getValues(),$form->url->getValues(),$image,
             $form->type->getValues(),$form->ord->getValues(), $form->params->getValues());

         $this->infoMsg()->addMessage($this->_('Odkaz byl uložen'));
         $this->link()->route()->reload();
      }


      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('edit.phtml');
   }

   public function editController() {
      $this->checkWritebleRights();
      $model = new NavigationMenu_Models_List();
      $item = $model->getItem($this->getRequest('id'));

      $form = $this->createForm();

      $form->name->setValues($item->{NavigationMenu_Models_List::COL_NAME});
      $form->url->setValues($item->{NavigationMenu_Models_List::COL_URL});
      $form->type->setValues($item->{NavigationMenu_Models_List::COL_TYPE});
      $form->ord->setValues($item->{NavigationMenu_Models_List::COL_ORDER});
      $form->params->setValues($item->{NavigationMenu_Models_List::COL_PARAMS});
      if($item->{NavigationMenu_Models_List::COL_ICON} != null){
         $form->icon->setSubLabel($this->_('Aktuálně: ').$item->{NavigationMenu_Models_List::COL_ICON});
      }


      if($form->isValid()) {
         $image = null;
         if($form->icon->getValues() != null) {
            $image = $form->icon->createFileObject('Filesystem_File_Image');
            $image->setDimensions(16, 16);
            $image->save();
         }

         $model = new NavigationMenu_Models_List();
         $model->saveLink($form->name->getValues(),$form->url->getValues(),$image,
             $form->type->getValues(),$form->ord->getValues(), $form->params->getValues(),
             $item->{NavigationMenu_Models_List::COL_ID});

         $this->infoMsg()->addMessage($this->_('Odkaz byl uložen'));
         $this->link()->route()->reload();
      }


      $this->view()->template()->form = $form;
      $this->view()->template()->edit = true;
      $this->view()->template()->name = $item->{NavigationMenu_Models_List::COL_NAME};
      $this->view()->template()->addTplFile('edit.phtml');
   }

   /**
    * Metoda vatvoří formulář
    * @return Form
    */
   private function createForm() {
      $form = new Form('link_');

      $elemLinkName = new Form_Element_Text('name', $this->_('Název'));
      $elemLinkName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemLinkName);

      $elemIcon = new Form_Element_File('icon', $this->_('Souobr s ikonou'));
      $elemIcon->addValidation(new Form_Validator_FileExtension(array('png','jpg','jpeg')));
      $elemIcon->setUploadDir($this->getModule()->getDataDir());
      $form->addElement($elemIcon);

      $elemLink = new Form_Element_Text('url', $this->_('Adresa'));
      $elemLink->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemLink);

      $elemType = new Form_Element_Select('type', $this->_('Typ'));
      $elemType->setOptions(array('Podstránka'=>'subdomain', 'Projekt' => 'project'));
      $form->addElement($elemType);

      $elemParams = new Form_Element_Text('params', $this->_('Parametry odkazu'));
      $form->addElement($elemParams);

      $elemPrior = new Form_Element_Text('ord', $this->_('Pořadí'));
      $elemPrior->addValidation(new Form_Validator_IsNumber());
      $elemPrior->setSubLabel($this->_('Čím menší číslo tím bude odkaz výše'));
      $form->addElement($elemPrior);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $form->addElement($elemSubmit);

      return $form;
   }

   public static function listController() {
      
   }
}

?>