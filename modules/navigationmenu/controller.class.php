<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class NavigationMenu_Controller extends Controller {
   const ICONS_DIR = 'navigationmenu';

   public function init(){
      $this->category()->getModule()->setDataDir(self::ICONS_DIR);
   }

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
             $form->type->getValues(),$form->ord->getValues(), $form->params->getValues(),
                 $form->target->getValues());

         $this->infoMsg()->addMessage($this->_('Odkaz byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->template()->form = $form;
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
      $form->target->setValues($item->{NavigationMenu_Models_List::COL_NEW_WIN});
      $form->follow->setValues($item->{NavigationMenu_Models_List::COL_FOLLOW});
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
             $form->target->getValues(), $form->follow->getValues(),
             $item->{NavigationMenu_Models_List::COL_ID});

         $this->infoMsg()->addMessage($this->_('Odkaz byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->template()->form = $form;
      $this->view()->template()->edit = true;
      $this->view()->template()->name = $item->{NavigationMenu_Models_List::COL_NAME};
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
      $elemLink->addFilter(new Form_Filter_Url());
      $form->addElement($elemLink);

      $elemType = new Form_Element_Select('type', $this->_('Typ'));
      $elemType->setOptions(array('Podstránka'=>'subdomain', 'Projekt' => 'project', 
         'Skupina' => 'group', 'Partner' => 'partner'));
      $form->addElement($elemType);

      $elemTarget =  new Form_Element_Checkbox('target', $this->_('Otevřít v novém okně'));
      $form->addElement($elemTarget);

      $elemFollow =  new Form_Element_Checkbox('follow', $this->_('Povolit googlu sledování'));
      $elemFollow->setValues(true);
      $form->addElement($elemFollow);

      $elemParams = new Form_Element_Text('params', $this->_('Parametry odkazu'));
      $form->addElement($elemParams);

      $elemPrior = new Form_Element_Text('ord', $this->_('Pořadí'));
      $elemPrior->addValidation(new Form_Validator_IsNumber());
      $elemPrior->setSubLabel($this->_('Větší číslo = důležitější odkaz'));
      $form->addElement($elemPrior);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      return $form;
   }

   public static function listController() {
      
   }
}

?>