<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdvEventsAdmOrganizers_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
      $this->checkControllRights();
   }

   public function mainController()
   {
      parent::mainController();

      $this->processDelete();

      // načtení sportů
      $model = new AdvEventsBase_Model_Organizers();
//      $model->createTable();
      $compScroll = new Component_Scroll();
      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 20);
      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $this->view()->scroll = $compScroll;

      if($this->getRequestParam('filter', false)){
         $organizers = AdvEventsBase_Model_Organizers::getOrganizerByString($this->getRequestParam('filter'),
            $compScroll->getRecordsOnPage(), $compScroll->getStartRecord(), AdvEventsBase_Model_Organizers::COLUMN_NAME);
      } else {
         $organizers = AdvEventsBase_Model_Organizers::getOrganizers(
             $compScroll->getRecordsOnPage(),
             $compScroll->getStartRecord(), 
             AdvEventsBase_Model_Organizers::COLUMN_NAME);
      }

      $this->view()->organizers = $organizers;
   }

   public function addOrganizerController()
   {
      $form = $this->createOrganizerForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $this->processOrganizerForm($form);
         $this->infoMsg()->addMessage($this->tr('Organizátor byl uložen'));
         $this->link()->route()->redirect();
      }
      
      $this->view()->formEdit = $form;
   }
   
   public function editOrganizerController($id)
   {
      $record = AdvEventsBase_Model_Organizers::getRecord($id);
      if(!$record){
         throw new InvalidArgumentException($this->tr('Požadovaný organizátor neexistuje'));
      }
      $form = $this->createOrganizerForm($record);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $this->processOrganizerForm($form, $record);
         $this->infoMsg()->addMessage($this->tr('Organizátor byl uložen'));
         $this->link()->route()->redirect();
      }

      $this->view()->organizator = $record;
      $this->view()->formEdit = $form;
   }
   
   public function detailOrganizerController($id)
   {
      $record = AdvEventsBase_Model_Organizers::getRecord($id);
      if(!$record){
         throw new InvalidArgumentException($this->tr('Požadovaný organizátor neexistuje'));
      }
      
      $this->view()->organizator = $record;
   }
   
   /* obslužné metody */
   
   
   protected function createOrganizerForm(Model_ORM_Record $record = null)
   {
      $form = new Form('organizer_');

      $grpBase = $form->addGroup('base', $this->tr('Základní parametry'));
//      $grpContacts = $form->addGroup('contacts', $this->tr('Kontakty'));
      $grpOther = $form->addGroup('other', $this->tr('Odkazy'));
          
      
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elem = new Form_Element_Text('name', $this->tr('Název'));
      $elem->addValidation(new Form_Validator_NotEmpty());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpBase);

      $elem = new Form_Element_TextArea('note', $this->tr('Poznámka'));
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpBase);

      $elem = new Form_Element_TextArea('address', $this->tr('Adresa'));
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpBase);
      
      $elem = new Form_Element_Select('idGroup', $this->tr('Uživatelská skupina'));
      $elem->addOption($this->tr('Žádná'), 0);
      $groups = Model_Groups::getAllRecords();
      
      foreach ($groups as $grp) {
         $elem->addOption($grp->{Model_Groups::COLUMN_LABEL}.' ('.$grp->{Model_Groups::COLUMN_NAME}.')', $grp->getPK());
      }
      $form->addElement($elem, $grpBase);
      
      $elem = new Form_Element_Text('priority', $this->tr('Priorita'));
      $elem->setValues(0);
      $elem->addValidation(new Form_Validator_IsNumber(Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elem, $grpBase);
      
      $elem = new Form_Element_Text('url', $this->tr('Odkaz na stránky'));
      $elem->addValidation(new Form_Validator_Url());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpOther);
      
      $elem = new Form_Element_Text('urlFcb', $this->tr('Odkaz na Facebook'));
      $elem->addValidation(new Form_Validator_Url());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpOther);
      
      $elem = new Form_Element_Text('urlYoutube', $this->tr('Odkaz na YouTube'));
      $elem->addValidation(new Form_Validator_Url());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpOther);
      
      $elem = new Form_Element_Text('urlTwitter', $this->tr('Odkaz na Twitter'));
      $elem->addValidation(new Form_Validator_Url());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpOther);
      
      if($record){
         $form->name->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_NAME});
         $form->note->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_NOTE});
         $form->address->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_ADDRESS});
         $form->idGroup->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_ID_GROUP});
         $form->priority->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_PRIORITY});
         $form->url->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_URL});
         $form->urlFcb->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_URL_FCB});
         $form->urlYoutube->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_URL_YOUTUBE});
         $form->urlTwitter->setValues($record->{AdvEventsBase_Model_Organizers::COLUMN_URL_TWITTER});
      }
      
      $elem = new Form_Element_SaveCancel('save');
      $form->addElement($elem);

      
      return $form;
   }
   
   protected function processOrganizerForm(Form $form, Model_ORM_Record $record = null)
   {
      if($record == null){
         $record = AdvEventsBase_Model_Organizers::getNewRecord();
      }
      // uložení místa
      $record->{AdvEventsBase_Model_Organizers::COLUMN_NAME} = $form->name->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_NOTE} = $form->note->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_ADDRESS} = $form->address->getValues();
      
      $record->{AdvEventsBase_Model_Organizers::COLUMN_URL} = $form->url->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_URL_FCB} = $form->urlFcb->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_URL_TWITTER} = $form->urlYoutube->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_URL_YOUTUBE} = $form->urlTwitter->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_ID_GROUP} = $form->idGroup->getValues();
      $record->{AdvEventsBase_Model_Organizers::COLUMN_PRIORITY} = $form->priority->getValues();
      
      $record->save();
      
   }

   protected function processDelete()
   {
      $form = new Form('organizer_del');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new AdvEventsBase_Model_Organizers();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Organizátor byl smazán'));
         $this->link()->redirect();
      }
      $this->view()->formDelete = $form;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
