<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class Events_Controller extends Controller {

   const DIR_IMAGES = "events";
   const DIR_CAT_IMAGES = "cats";
   const DIR_EVENT_IMAGES = "events";
   
   const PARAM_ADMIN_RECIPIENTS = 'admin_rec';

   protected function init()
   {
      parent::init();
      $this->module()->setDataDir(self::DIR_IMAGES);
   }

   public function mainController()
   {
      $this->checkReadableRights();

      $dateFrom = new DateTime($this->getRequestParam('dateFrom', date("Y-m-d")));
      $dateTo = new DateTime($this->getRequestParam('dateTo', date("Y-m-d")));
      switch ($this->getRequestParam('range', 'day')) {
         case 'week':
            $dateTo->modify('+1 week');
            break;
         case 'month':
            $dateTo->modify('+1 month');
            break;
         case 'day':
         default:
            break;
      }
      
      $this->view()->dateFrom = $dateFrom;
      $this->view()->dateTo = $dateTo;

      // load events
      $this->view()->events = $this->getSortedEvents($dateFrom, $dateTo, $this->getRequestParam('cat', null));
      
      $modelCats = new Events_Model_Categories();
      $this->view()->cats = $modelCats
         ->where(Events_Model_Categories::COL_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
         ->order(array(Events_Model_Categories::COL_NAME => Model_ORM::ORDER_ASC))
         ->records();
   }

   public function addCatController()
   {
      $this->checkControllRights();

      $form = $this->createCatEditForm();

      if ($form->isValid()) {
         $model = new Events_Model_Categories();
         $record = $model->newRecord();

         $record->{Events_Model_Categories::COL_ID_CATEGORY} = $this->category()->getId();
         $record->{Events_Model_Categories::COL_NAME} = $form->name->getValues();
         $record->{Events_Model_Categories::COL_CONTACT} = $form->contact->getValues();
         $record->{Events_Model_Categories::COL_WWW} = $form->www->getValues();
         $record->{Events_Model_Categories::COL_NOTE} = $form->note->getValues();// COL_TEXT
         if($form->image->getValues()){
            $img = $form->image->getValues();
            $record->{Events_Model_Categories::COL_IMAGE} = $img['name'];
         } else if(isset ($form->imageSelect)){
            $record->{Events_Model_Categories::COL_IMAGE} = $form->imageSelect->getValues();
         }
         $model->save($record);
         $this->infoMsg()->addMessage($this->tr('Kategorie byla uložena'));
         $this->link()->route('listCats')->reload();
      }

      $this->view()->form = $form;
   }

   public function editCatController()
   {
      $this->checkControllRights();

      $model = new Events_Model_Categories();
      $record = $model->record($this->getRequest('idcat'));

      if ($record == false) {
         return false;
      }

      $form = $this->createCatEditForm($record);

      if ($form->isValid()) {
         $record->{Events_Model_Categories::COL_ID_CATEGORY} = $this->category()->getId();
         $record->{Events_Model_Categories::COL_NAME} = $form->name->getValues();
         $record->{Events_Model_Categories::COL_CONTACT} = $form->contact->getValues();
         $record->{Events_Model_Categories::COL_WWW} = $form->www->getValues();
         $record->{Events_Model_Categories::COL_NOTE} = $form->note->getValues();// COL_TEXT

         if($form->image->getValues()){
            $img = $form->image->getValues();
            $record->{Events_Model_Categories::COL_IMAGE} = $img['name'];
         } else if(isset ($form->imageSelect)){
            $record->{Events_Model_Categories::COL_IMAGE} = $form->imageSelect->getValues();
         }
         
         $model->save($record);
         $this->infoMsg()->addMessage($this->tr('Kategorie byla uložena'));
         $this->link()->route('listCats')->reload();
      }

      $this->view()->form = $form;
      $this->view()->eventCat = $record;
   }

   protected function createCatEditForm($cat = null)
   {
      $form = new Form('editcat_');

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eName);

      $eContact = new Form_Element_TextArea('contact', $this->tr('Kontakt'));
      $form->addElement($eContact);

      $eWww = new Form_Element_Text('www', $this->tr('WWW'));
      $eWww->addValidation(new Form_Validator_Url());
      $form->addElement($eWww);

      $eImage = new Form_Element_File('image', $this->tr('Obrázek / ikona'));
      $eImage->setUploadDir($this->module()->getDataDir().self::DIR_CAT_IMAGES.DIRECTORY_SEPARATOR);
      $eImage->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::IMG));
      $form->addElement($eImage);

      $images = $this->getCatImages();
      if(!empty($images)){
         $eImgSelect = new Form_Element_Select('imageSelect', $this->tr('Uložené obrázky'));
         $eImgSelect->setOptions(array($this->tr('Žádný') => null), true);
         foreach ($images as $img) {
            $eImgSelect->setOptions(array($img => $img), true);
         }
         $form->addElement($eImgSelect);
      }
      
      $eNote = new Form_Element_TextArea('note', $this->tr('Poznámka'));
      $form->addElement($eNote);

      $elemSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($elemSubmit);

      if ($cat instanceof Model_ORM_Record) {
         $form->name->setValues($cat->{Events_Model_Categories::COL_NAME});
         $form->contact->setValues($cat->{Events_Model_Categories::COL_CONTACT});
         $form->www->setValues($cat->{Events_Model_Categories::COL_WWW});
         $form->note->setValues($cat->{Events_Model_Categories::COL_NOTE});// COL_TEXT
         if(isset($form->imageSelect)){
            $form->imageSelect->setValues($cat->{Events_Model_Categories::COL_IMAGE});
         }
      }

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route('listCats')->reload();
      }

      return $form;
   }
   
   protected function getCatImages()
   {
      $images = array();
      $path = $this->category()->getModule()->getDataDir().self::DIR_CAT_IMAGES.DIRECTORY_SEPARATOR;
      foreach (glob ($path . '*.{jpg,jpeg,gif,png}', GLOB_BRACE) as $fileName) {
         $fileName = basename($fileName);
         $images[] = $fileName;
      }
      return $images;
   }

   public function listCatsController()
   {
      $this->checkControllRights();

      $model = new Events_Model_Categories();

      $evCats = $model
         ->order(array(Events_Model_Categories::COL_NAME => Model_ORM::ORDER_ASC))
         ->where(Events_Model_Categories::COL_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
         ->records();

      // listování zde, pokud bude třeba

      $this->view()->categories = $evCats;

      $formGenerateToken = new Form('cat_token_');
      $elemId = new Form_Element_Hidden('id');
      $formGenerateToken->addElement($elemId);
      $elemGenToken = new Form_Element_Submit('generate', $this->tr('Genoravat přístup'));
      $formGenerateToken->addElement($elemGenToken);

      if ($formGenerateToken->isValid()) {
         $newToken = vve_generate_token();
         $cat = $model->record((int) $formGenerateToken->id->getValues());
         $cat->{Events_Model_Categories::COL_ACCESS_TOKEN} = $newToken;
         $model->save($cat);
         $this->infoMsg()->addMessage($this->tr('Přístupový token byl generován'));
         $this->link()->reload();
      }

      $formDelete = new Form('cat_delete_');
      $elemId = new Form_Element_Hidden('id');
      $formDelete->addElement($elemId);
      $elemDelete = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formDelete->addElement($elemDelete);

      if ($formDelete->isValid()) {
         $model->delete((int) $formDelete->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Kategorie byla smazána'));
         $this->link()->reload();
      }

      $formSetPublic = new Form('cat_is_public_');
      $elemId = new Form_Element_Hidden('id');
      $formSetPublic->addElement($elemId);
      $elemSetPublic = new Form_Element_Submit('change_public', $this->tr('Změnit zveřejnění'));
      $formSetPublic->addElement($elemSetPublic);

      if ($formSetPublic->isValid()) {
         $cat = $model->record((int) $formSetPublic->id->getValues());
         $cat->{Events_Model_Categories::COL_IS_PUBLIC} = !$cat->{Events_Model_Categories::COL_IS_PUBLIC};
         $model->save($cat);

         $name = $cat->{Events_Model_Categories::COL_NAME};
         if ($cat->{Events_Model_Categories::COL_IS_PUBLIC} == true) {
            $this->infoMsg()->addMessage(sprintf($this->tr('Kategorie "%s" je nyní veřejná'), $name));
         } else {
            $this->infoMsg()->addMessage(sprintf($this->tr('Kategorie "%s" je nyní neveřejná'), $name));
         }
         $this->link()->reload();
      }

      $this->view()->formSetPublic = $formSetPublic;
      $this->view()->formDelete = $formDelete;
      $this->view()->formGenToken = $formGenerateToken;
   }

   public function listEventsController()
   {
      $this->checkWritebleRights();
      // kontrola tokenu

      $allowedCat = null;
      $this->view()->isControll = false;
      if ($this->category()->getRights()->isControll()) {
         $this->view()->isControll = true;
         $allowedCat = $this->getRequestParam('cat', null);
      } else if ($this->getRequestParam('token', false)) {
         $allowedCat = $this->getTokenCatAccess($this->getRequestParam('token'));
         if ($allowedCat === false) {
            return false;
         }
         $allowedCat = $allowedCat->{Events_Model_Categories::COL_ID};
      } else {
         $allowedCat = null;
      }

      $this->runEventsActions();

      $dateFrom = new DateTime($this->getRequestParam('dateFrom', date("Y-m-1")));
      $dateTo = new DateTime($this->getRequestParam('dateTo', date("Y-m-t")));
      $this->view()->dateFrom = $dateFrom;
      $this->view()->dateTo = $dateTo;

      $this->view()->events = $this->getSortedEvents(
         $dateFrom, $dateTo, $allowedCat, 
         $this->getRequestParam('contain', null),
         false, $this->getRequestParam('onlyPublicAdd', 'off') == 'on' );

      $modelCats = new Events_Model_Categories();
      $this->view()->cats = $modelCats
         ->where(Events_Model_Categories::COL_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
         ->order(array(Events_Model_Categories::COL_NAME => Model_ORM::ORDER_ASC))
         ->records();
   }

   protected function runEventsActions()
   {
      $model = new Events_Model();
      // hromadné akce
      $allowedActions = array('notnew', 'visible', 'nonvisible', 'delete', 'recommended', 'delrecommended');

      if (isset($_POST['events_action']) && isset($_POST['events_items_ids'])
         && $_POST['events_items_ids'] != null
         && in_array($_POST['events_action'], $allowedActions)) {

         $ids = explode(';', $_POST['events_items_ids']);

         switch ($_POST['events_action']) {
            case 'notnew':
               foreach ($ids as $id) {
                  $model->where(Events_Model::COL_ID . " = :ide", array('ide' => $id))
                     ->update(array(Events_Model::COL_PUBLIC_ADD => false));
               }
               break;
            case 'visible':
               foreach ($ids as $id) {
                  $rec = $model
                     ->where(Events_Model::COL_ID . " = :ide", array('ide' => $id))
                     ->update(array(Events_Model::COL_PUBLIC => true));
               }
               break;
            case 'nonvisible':
               foreach ($ids as $id) {
                  $rec = $model
                     ->where(Events_Model::COL_ID . " = :ide", array('ide' => $id))
                     ->update(array(Events_Model::COL_PUBLIC => false));
               }
               break;
            case 'recommended':
               foreach ($ids as $id) {
                  $rec = $model
                     ->where(Events_Model::COL_ID . " = :ide", array('ide' => $id))
                     ->update(array(Events_Model::COL_IS_RECOMMENDED => true));
               }
               break;
            case 'delrecommended':
               foreach ($ids as $id) {
                  $rec = $model
                     ->where(Events_Model::COL_ID . " = :ide", array('ide' => $id))
                     ->update(array(Events_Model::COL_IS_RECOMMENDED => false));
               }
               break;
            case 'delete':
               foreach ($ids as $id) {
                  $rec = $model->delete($id);
               }
               break;
            default:
               throw new UnexpectedValueException($this->tr('Nepodporovaná akce s položkami'));
               break;
         }
         $this->infoMsg()->addMessage($this->tr('Vybrané položky byly praveny'));
         $this->link()->reload();
      }

      // Akce položek
      // formuláře pro mazání, změna viditelnosti, úprava
      $formVisible = new Form('ev_change_visible');
      $eId = new Form_Element_Hidden('id');
      $formVisible->addElement($eId);
      $eChange = new Form_Element_Submit('change', $this->tr('Změnit viditelnost'));
      $formVisible->addElement($eChange);

      if ($formVisible->isValid()) {
         $rec = $model->record((int) $formVisible->id->getValues());
         $rec->{Events_Model::COL_PUBLIC} = !$rec->{Events_Model::COL_PUBLIC};
         $model->save($rec);
         $this->infoMsg()->addMessage($this->tr('Stav položky byl změněn'));
         $this->link()->reload();
      }
      $this->view()->formVisible = $formVisible;

      $formRecomended = new Form('ev_change_recomended_');
      $eId = new Form_Element_Hidden('id');
      $formRecomended->addElement($eId);
      $eChange = new Form_Element_Submit('change', $this->tr('Změnit doporučení'));
      $formRecomended->addElement($eChange);

      if ($formRecomended->isValid()) {
         $rec = $model->record((int) $formRecomended->id->getValues());
         $rec->{Events_Model::COL_IS_RECOMMENDED} = !$rec->{Events_Model::COL_IS_RECOMMENDED};
         $model->save($rec);
         $this->infoMsg()->addMessage($this->tr('Stav položky byl změněn'));
         $this->link()->reload();
      }
      $this->view()->formRecomended = $formRecomended;

      $formDelete = new Form('ev_delete');
      $eId = new Form_Element_Hidden('id');
      $formDelete->addElement($eId);
      $eDelete = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formDelete->addElement($eDelete);

      if ($formDelete->isValid()) {
         $rec = $model->delete((int) $formDelete->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Položka byla smnazána'));
         $this->link()->reload();
      }
      $this->view()->formDelete = $formDelete;
   }

   protected function getSortedEvents($dateFrom, $dateTo, $cat = null, $contain = null, $onlyPublic = true, $onlyPublicAdd = false)
   {
      $model = new Events_Model();
      $modelWhere = Events_Model_Categories::COL_ID_CATEGORY . " = :idc";
      $modelBindValues = array('idc' => $this->category()->getId());
      
      // model settings
      $modelWhere .= " AND (" . Events_Model::COL_DATE_FROM . " BETWEEN :dateStart1 AND :dateEnd1 "
         . " OR " . Events_Model::COL_DATE_TO . " BETWEEN :dateStart2 AND :dateEnd2 "
         . " OR ( " . Events_Model::COL_DATE_FROM . " < :dateStart3 AND " . Events_Model::COL_DATE_TO . " > :dateEnd3 )"
         .")";
      $modelBindValues['dateStart1'] = $modelBindValues['dateStart2'] = $modelBindValues['dateStart3'] = $dateFrom;
      $modelBindValues['dateEnd1'] = $modelBindValues['dateEnd2'] = $modelBindValues['dateEnd3'] = $dateTo;
      
      if ($cat != null) {
         $modelWhere .= " AND " . Events_Model::COL_ID_EVE_CATEGORY . " = :idevcat";
         $modelBindValues['idevcat'] = (int) $cat;
      }
      if ($contain != null) {
         $modelWhere .= " AND " . Events_Model::COL_NAME . " LIKE :cnt";
         $modelBindValues['cnt'] = "%" . (string) $contain . "%";
      }
      if ($onlyPublic) {
         $modelWhere .= " AND " . Events_Model::COL_PUBLIC . " = 1";
      }
      if ($onlyPublicAdd) {
         $modelWhere .= " AND " . Events_Model::COL_PUBLIC_ADD . " = 1";
      }
      
      $events = $model
         ->joinFK(Events_Model::COL_ID_EVE_CATEGORY)
         ->order(array(
            Events_Model_Categories::COL_NAME => Model_ORM::ORDER_ASC,
            Events_Model::COL_DATE_FROM => Model_ORM::ORDER_ASC,
            Events_Model::COL_TIME_FROM => Model_ORM::ORDER_ASC,
         ))
         ->where($modelWhere, $modelBindValues)
         ->records();
      
      $eventsSorted = array();
      if (!empty($events)) {
         foreach ($events as $event) {
            $cId = $event->{Events_Model_Categories::COL_ID};
            if (!isset($eventsSorted[$cId])) {
               $eventsSorted[$cId] = array('cat' => $event, 'events' => array());
            }
            $eventsSorted[$cId]['events'][] = $event;
         }
      }
      return $eventsSorted;
   }

   /**
    * Metoda zkontroluje přístupový token a vrátí id kategorie. Pokud token neexistuje vrací false
    * @return boolean 
    */
   protected function getTokenCatAccess($token)
   {
      $model = new Events_Model_Categories();
      return $model->where(Events_Model_Categories::COL_ACCESS_TOKEN . " = :acctoken", array('acctoken' => $token))
            ->record();
   }

   public function addEventController()
   {
      $this->checkWritebleRights();

      $allowedCat = false;
      $isPublicAdd = true;
      if ($this->category()->getRights()->isControll()) {
         $allowedCat = true;
         $isPublicAdd = false;
      } else if ($this->getRequestParam('token', false)) {
         $allowedCat = $this->getTokenCatAccess($this->getRequestParam('token'));
         $isPublicAdd = false;
      }

      // načtení kategorií podle práv
      $modelC = new Events_Model_Categories();
      $whereStr = Events_Model_Categories::COL_ID_CATEGORY . " = :idc";
      $whereBind = array("idc" => $this->category()->getId());

      if ($allowedCat === false) {
         // only public cats
         $whereStr .= " AND " . Events_Model_Categories::COL_IS_PUBLIC . " = 1";
      } else if ($allowedCat instanceof Model_ORM_Record) {
         // all cats
         $whereStr .= " AND " . Events_Model_Categories::COL_ID . " = :idec";
         $whereBind['idec'] = $allowedCat->{Events_Model_Categories::COL_ID};
      }

      $cats = $modelC
         ->where($whereStr, $whereBind)
         ->order(array(Events_Model_Categories::COL_NAME))
         ->records();


      $form = $this->createEventEditForm(null, $cats, $isPublicAdd, $isPublicAdd);

      $eAddAnother = new Form_Element_Checkbox('addAnother', $this->tr('Přidat další položku'));
      $eAddAnother->setSubLabel($this->tr('Při zaškrtnutí bude po uložení zobrazen tento formulář znovu'));
      $form->addElement($eAddAnother);

      if ($form->isValid()) {
         $model = new Events_Model();
         $record = $model->newRecord();

         if (isset($form->contact)) {
            $record->{Events_Model::COL_CONTACT} = $form->contact->getValues();
         }
         $record->{Events_Model::COL_DATE_FROM} = $form->datefrom->getValues();
         $record->{Events_Model::COL_DATE_TO} = $form->dateto->getValues();
         $record->{Events_Model::COL_TIME_FROM} = $form->timefrom->getValues();
         $record->{Events_Model::COL_TIME_TO} = $form->timeto->getValues();
         $record->{Events_Model::COL_ID_EVE_CATEGORY} = $form->cat->getValues();
         $record->{Events_Model::COL_NAME} = $form->name->getValues();
         $record->{Events_Model::COL_NOTE} = $form->note->getValues();// COL_TEXT
         $record->{Events_Model::COL_PLACE} = $form->place->getValues();
         $record->{Events_Model::COL_PRICE} = $form->price->getValues();
         $record->{Events_Model::COL_PUBLIC} = true;
         $record->{Events_Model::COL_PUBLIC_ADD} = $isPublicAdd;
         $record->{Events_Model::COL_IP_ADD} = ip2long($_SERVER['REMOTE_ADDR']);
         $model->save($record);

         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));

         if($isPublicAdd){
            $this->sendNewEventNotification($record);
         }
         
         // @TODO pokud bude anonym, redirect přímo na hlavní stránku
         if ($form->addAnother->getValues() == true) {
            $this->link()->reload();
         } else if ($isPublicAdd) {
            $this->link()->route()->reload();
         } else {
            $this->link()->route('listEvents')->reload();
         }
      }

      $this->view()->form = $form;
   }

   public function editEventController()
   {
      // opět kontrola
      $this->checkWritebleRights();

      $allowedCat = false;
      if ($this->category()->getRights()->isControll()) {
         $allowedCat = true;
         $isPublicAdd = false;
      } else if ($this->getRequestParam('token', false)) {
         $allowedCat = $this->getTokenCatAccess($this->getRequestParam('token'));
         $isPublicAdd = false;
      } else {
         return false;
      }

      // načtení kategorií podle práv
      $modelC = new Events_Model_Categories();
      $whereStr = Events_Model_Categories::COL_ID_CATEGORY . " = :idc";
      $whereBind = array("idc" => $this->category()->getId());

      if ($allowedCat === false) {
         // only public cats
         $whereStr .= " AND " . Events_Model_Categories::COL_IS_PUBLIC . " = 1";
      } else if ($allowedCat instanceof Model_ORM_Record) {
         // all cats
         $whereStr .= " AND " . Events_Model_Categories::COL_ID . " = :idec";
         $whereBind['idec'] = $allowedCat->{Events_Model_Categories::COL_ID};
      }

      $cats = $modelC
         ->where($whereStr, $whereBind)
         ->order(array(Events_Model_Categories::COL_NAME))
         ->records();

      $modelE = new Events_Model();

      $event = $modelE->record($this->getRequest('idevent', 0));

      if ($event == false) {
         return false;
      }

      $form = $this->createEventEditForm($event, $cats, $event->{Events_Model::COL_CONTACT} != null, false);

      if ($form->isValid()) {
         $model = new Events_Model();
         if (isset($form->contact)) {
            $event->{Events_Model::COL_CONTACT} = $form->contact->getValues();
         }
         $event->{Events_Model::COL_DATE_FROM} = $form->datefrom->getValues();
         $event->{Events_Model::COL_DATE_TO} = $form->dateto->getValues();
         $event->{Events_Model::COL_TIME_FROM} = $form->timefrom->getValues();
         $event->{Events_Model::COL_TIME_TO} = $form->timeto->getValues();
         $event->{Events_Model::COL_ID_EVE_CATEGORY} = $form->cat->getValues();
         $event->{Events_Model::COL_NAME} = $form->name->getValues();
         $event->{Events_Model::COL_NOTE} = $form->note->getValues();// COL_TEXT
         $event->{Events_Model::COL_PLACE} = $form->place->getValues();
         $event->{Events_Model::COL_PRICE} = $form->price->getValues();
         $model->save($event);

         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));

         // @TODO pokud bude anonym, redirect přímo na hlavní stránku
         if (isset($form->addAnother) && $form->addAnother->getValues() == true) {
            $this->link()->reload();
         } else if ($isPublicAdd) {
            $this->link()->route()->reload();
         } else {
            $this->link()->route('listEvents')->reload();
         }
      }

      $this->view()->event = $event;
      $this->view()->form = $form;
   }

   protected function createEventEditForm($event = null, $catsRecords = array(), $withContact = true, $isAnonym = true)
   {
      $form = new Form('editevent_');
      $fGrpBase = $form->addGroup('base', $this->tr('Základní informace'));

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $eName->addFilter(new Form_Filter_StripTags());
      $form->addElement($eName, $fGrpBase);

      $ePlace = new Form_Element_Text('place', $this->tr('Místo konání'));
      $ePlace->addFilter(new Form_Filter_StripTags());
      $form->addElement($ePlace, $fGrpBase);

      $ePrice = new Form_Element_Text('price', $this->tr('Cena v Kč'));
      $ePrice->addFilter(new Form_Filter_StripTags());
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice, $fGrpBase);

      if ($withContact) {
         $fGrpContact = $form->addGroup('contact', $this->tr("Kontakt"));
         $eContact = new Form_Element_TextArea('contact', $this->tr('Kontakt'));
         $eContact->setSubLabel($this->tr('Kontaktní informace na zadavatele akce. (Např. Váš e-mail, telefon)'));
         $eContact->addFilter(new Form_Filter_StripTags());
         if ($event instanceof Model_ORM_Record == false) {
            $eContact->addValidation(new Form_Validator_NotEmpty());
         }
         $form->addElement($eContact, $fGrpContact);
      }

      $eNote = new Form_Element_TextArea('note', $this->tr('Poznámka'));
      $eNote->addFilter(new Form_Filter_StripTags());
      $form->addElement($eNote, $fGrpBase);

      $elemCat = new Form_Element_Select('cat', $this->tr('Kategorie'));
      if (!empty($catsRecords)) {
         if(count($catsRecords) > 1){
            $elemCat->setOptions(array($this->tr('Vyberte') => null ));
         }
         foreach ($catsRecords as $cat) {
            $elemCat->setOptions(array($cat->{Events_Model_Categories::COL_NAME} => $cat->{Events_Model_Categories::COL_ID}), true);
         }
         $form->addElement($elemCat, $fGrpBase);
      }

      $fGrpDates = $form->addGroup('dates', $this->tr('Časové údaje'));

      $eDateFrom = new Form_Element_Text('datefrom', $this->tr('Datum začátku'));
      $eDateFrom->addValidation(new Form_Validator_NotEmpty());
      $eDateFrom->addValidation(new Form_Validator_Date());
      $eDateFrom->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateFrom, $fGrpDates);

      $eTimeFrom = new Form_Element_Text('timefrom', $this->tr('Čas začátku'));
      $eTimeFrom->addValidation(new Form_Validator_Time());
      $eTimeFrom->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eTimeFrom, $fGrpDates);

      $eDateTo = new Form_Element_Text('dateto', $this->tr('Datum konce'));
      $eDateTo->addValidation(new Form_Validator_Date());
      $eDateTo->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateTo, $fGrpDates);

      $eTimeTo = new Form_Element_Text('timeto', $this->tr('Čas konce'));
      $eTimeTo->addValidation(new Form_Validator_Time());
      $eTimeTo->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eTimeTo, $fGrpDates);

      if ($isAnonym) {
         $eCaptcha = new Form_Element_Captcha('captcha', $this->tr('Ověření'));
         $form->addElement($eCaptcha);
      }

      $elemSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($elemSubmit);
      if ($event instanceof Model_ORM_Record) {
         $form->name->setValues($event->{Events_Model::COL_NAME});
         $form->place->setValues($event->{Events_Model::COL_PLACE});
         if ($event->{Events_Model::COL_PRICE} != 0) {
            $form->price->setValues($event->{Events_Model::COL_PRICE});
         }
         if(isset($form->contact)){
            $form->contact->setValues($event->{Events_Model::COL_CONTACT});
         }
         $form->note->setValues($event->{Events_Model::COL_NOTE});// COL_TEXT
         $form->cat->setValues($event->{Events_Model::COL_ID_EVE_CATEGORY});
         if ($event->{Events_Model::COL_DATE_FROM} != null) {
            $date = new DateTime($event->{Events_Model::COL_DATE_FROM});
            $form->datefrom->setValues(vve_date("%x", $date));
         }
         $form->timefrom->setValues($event->{Events_Model::COL_TIME_FROM});
         if ($event->{Events_Model::COL_DATE_TO} != null) {
            $date = new DateTime($event->{Events_Model::COL_DATE_TO});
            $form->dateto->setValues(vve_date("%x", $date));
         }
         $form->timeto->setValues($event->{Events_Model::COL_TIME_TO});
      }

      if($form->isSend() && $form->cat->getValues() == null && count($elemCat->getOptions()) > 1 ){
         $elemCat->setError($this->tr('Musíte vybrat kategorii'));
      }
      
      if ($form->isSend() && $form->save->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route('listEvents')->reload();
      }

      return $form;
   }
   
   // EXPORTY

   public function exportsController(){
      $this->checkControllRights();
      
      $form = new Form('events_export_');
      
      $eType = new Form_Element_Select('type', $this->tr('Typ'));
      $eType->setOptions(array($this->tr('Základní xls - pro sazbu') => 'basexls'));
      $form->addElement($eType);
      
      $eDateFrom = new Form_Element_Text('datefrom', $this->tr('Od'));
      $eDateFrom->addValidation(new Form_Validator_NotEmpty());
      $eDateFrom->addValidation(new Form_Validator_Date());
      $eDateFrom->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateFrom);
      
      $eDateTo = new Form_Element_Text('dateto', $this->tr('Do'));
      $eDateTo->addValidation(new Form_Validator_NotEmpty());
      $eDateTo->addValidation(new Form_Validator_Date());
      $eDateTo->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateTo);
      
      $eExport = new Form_Element_Submit('export', $this->tr('Exportovat'));
      $form->addElement($eExport);
      
      if ($form->isValid()) {
         if($form->type->getValues() == 'basexls'){
            $this->exportXLSPrint($form->datefrom->getValues(), $form->dateto->getValues());
         }
      }
      
      $this->view()->formExport = $form;
   }
   
   protected function exportXLSPrint($dateFrom, $dateTo){
      include_once AppCore::getAppLibDir().'lib/nonvve/phpexcel/PHPExcel.php';
      
      $doc = new PHPExcel();
      $defRowH = 18;
      $doc->setActiveSheetIndex(0);
      $sheet = $doc->getActiveSheet();
      $sheet->setCellValue('A1', sprintf( $this->tr('Export událostí od %s do %s'), vve_date("%x", $dateFrom), vve_date("%x", $dateTo)) );
      $sheet->getStyle('A1')->applyFromArray(array( 'font' => array( 'bold' => true, 'size' => 14 ) ));
      $sheet->getRowDimension(1)->setRowHeight(30);
      $sheet->mergeCells('A1:B1');
      $sheet->getColumnDimension('A')->setWidth(20);
      $sheet->getColumnDimension('B')->setWidth(60);
      $sheet->setCellValue('A2', $this->tr('Datum a čas') );
      $sheet->setCellValue('B2', $this->tr('Název a text') );
      $sheet->getStyle('A2')->applyFromArray(array( 'font' => array( 'bold' => true ) ));
      $sheet->getStyle('B2')->applyFromArray(array( 'font' => array( 'bold' => true ) ));
      $sheet->getRowDimension(2)->setRowHeight($defRowH);
      
      
      $events = $this->getSortedEvents($dateFrom, $dateTo);
      $row = 3;
      foreach ($events as $cat) {
         $sheet->setCellValueByColumnAndRow(0, $row, $cat['cat']->{Events_Model_Categories::COL_NAME});
         $sheet->setCellValueByColumnAndRow(1, $row, $cat['cat']->{Events_Model_Categories::COL_CONTACT});
         $sheet->getStyleByColumnAndRow(0, $row)->applyFromArray(array( 'font' => array( 'bold' => true ) ));
         $sheet->getStyleByColumnAndRow(1, $row)->applyFromArray(array( 'font' => array( 'italic' => true ) ));
         $sheet->getRowDimension($row)->setRowHeight($defRowH+10);
         $row++;
         
         foreach ($cat['events'] as $e) {
            $dateStr = vve_date("%d.%m.", $dateFrom);
            if($e->{Events_Model::COL_DATE_TO} != null){
               $dateStr .= '‒'.vve_date("%d.%m.", $dateTo);
            }
            $sheet->setCellValueByColumnAndRow(0, $row, $dateStr);
            $sheet->setCellValueByColumnAndRow(1, $row, $e->{Events_Model::COL_NAME});
            $sheet->getStyleByColumnAndRow(1, $row)->applyFromArray(array( 'font' => array( 'bold' => true ) ));
            $sheet->getRowDimension($row)->setRowHeight($defRowH);
            $row++;
            
            if($e->{Events_Model::COL_TIME_FROM} != null && $e->{Events_Model::COL_TIME_TO} == null){
               $time = new DateTime($e->{Events_Model::COL_TIME_FROM});
               $sheet->setCellValueByColumnAndRow(0, $row, $time->format("H.i")." h");
            } else if($e->{Events_Model::COL_TIME_FROM} != null && $e->{Events_Model::COL_TIME_TO} != null){
               $timef = new DateTime($e->{Events_Model::COL_TIME_FROM});
               $timet = new DateTime($e->{Events_Model::COL_TIME_TO});
               $sheet->setCellValueByColumnAndRow(0, $row, $timef->format("H.i")."‒".$timet->format("H.i")." h");
            }
            $sheet->setCellValueByColumnAndRow(1, $row, $e->{Events_Model::COL_NOTE}); // COL_TEXT
            
            $sheet->getRowDimension($row)->setRowHeight($defRowH);
            $row++;
         }
      }
            
      
      Template_Output::factory('xls');
      Template_Output::setDownload('events-'.vve_date("%x", $dateFrom)."-".vve_date("%x", $dateTo).'.xls');
      Template_Output::sendHeaders();
      header('Content-type: application/vnd.ms-excel');
      $objWriter = new PHPExcel_Writer_Excel5($doc);
      $objWriter->save('php://output');
   }
   
   protected function sendNewEventNotification($event)
   {
      // maily adminů - z uživatelů
      $adminMails = array();
      $usersId = $this->category()->getParam(self::PARAM_ADMIN_RECIPIENTS, array());
      $modelusers = new Model_Users();
      foreach ($usersId as $id) {
         $user = $modelusers->record($id);
         if($user->{Model_Users::COLUMN_MAIL} == null){
            continue;
         }
         $mails = explode(';', $user->{Model_Users::COLUMN_MAIL});
         $adminMails[$mails[0]] = $user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME};
      }
      if(!empty($adminMails)){ // pokud je prázdný výtahneme nasatvené maily
         try {
            // odeslání emailu
            $mail = new Email(true);
            $mail->setSubject(sprintf($this->tr('Nové veřejné přidání události na stránkách %s'), VVE_WEB_NAME ));
            
            $text = "Do stránek ".VVE_WEB_NAME." byla přidána nová událost:<br /><br />";
            
            $text .= "Událost byla přidána ".vve_date("%X %x")." z IP adresy: ".  long2ip($event->{Events_Model::COL_IP_ADD})."<br /><br />";
            
            $text .= "<table>";
            $text .= "<tr>";
            $text .= "<th width=\"150\" style=\"text-align: left;\">".$this->tr('Název')."</th>";
            $text .= "<td>".$event->{Events_Model::COL_NAME}."</td>";
            $text .= "</tr>";
            
            $text .= "<tr>";
            $text .= "<th style=\"text-align: left;\">".$this->tr('Místo konání')."</th>";
            $text .= "<td>".$event->{Events_Model::COL_PLACE}."</td>";
            $text .= "</tr>";
            
            $text .= "<tr>";
            $text .= "<th style=\"text-align: left;\">".$this->tr('Text')."</th>";
            $text .= "<td>".$event->{Events_Model::COL_NOTE}."</td>"; // COL_TEXT
            $text .= "</tr>";
            
            $text .= "<tr>";
            $text .= "<th style=\"text-align: left;\">".$this->tr('Datum začátku')."</th>";
            $text .= "<td>".vve_date("%x", $event->{Events_Model::COL_DATE_FROM})."</td>";
            $text .= "</tr>";
            
            if($event->{Events_Model::COL_TIME_FROM} != null){
               $text .= "<tr>";
               $text .= "<th style=\"text-align: left;\">".$this->tr('Čas začátku')."</th>";
               $text .= "<td>".vve_date("%H:%i", $event->{Events_Model::COL_TIME_FROM})."</td>";
               $text .= "</tr>";
            }
            if($event->{Events_Model::COL_DATE_TO} != null){
               $text .= "<tr>";
               $text .= "<th style=\"text-align: left;\">".$this->tr('Datum konce')."</th>";
               $text .= "<td>".vve_date("%x", $event->{Events_Model::COL_DATE_TO})."</td>";
               $text .= "</tr>";
            }
            if($event->{Events_Model::COL_TIME_TO} != null){
               $text .= "<tr>";
               $text .= "<th style=\"text-align: left;\">".$this->tr('Čas konce')."</th>";
               $text .= "<td>".vve_date("%H:%i", $event->{Events_Model::COL_TIME_TO})."</td>";
               $text .= "</tr>";
            }
            
            $text .= "<tr>";
            $text .= "<th style=\"text-align: left;\">".$this->tr('Konatkt')."</th>";
            $text .= "<td>".$event->{Events_Model::COL_CONTACT}."</td>";
            $text .= "</tr>";
            
            $text .= "</table>";
            
            $text .= "<br /><hr />".  $this->tr('Na tento e-mail neodpovídejte. Je generován automaticky systémem Cube CMS.');
            
            $mail->setContent($text);
            $mail->addAddress($adminMails);
            
            $mail->sendMail();
         } catch (Exception $e){
            new CoreErrors($e);
         }
      }         
   }


   public function settings(&$settings,Form &$form) {
      $grpAdmin = $form->addGroup('admins', 'Nastavení příjemců',
              'Nastavení příjemců odeslaných dotazů z kontaktního formuláře');

      $elemAdmins = new Form_Element_Select('admins', 'Adresy uživatelů v systému');
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb(true)->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if($user->{Model_Users::COLUMN_MAIL} != null){
            $usersIds[$user->{Model_Users::COLUMN_NAME} ." ".$user->{Model_Users::COLUMN_SURNAME}
              .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.$user->{Model_Users::COLUMN_GROUP_LABEL}
              .' ('.$user->{Model_Users::COLUMN_GROUP_NAME}.')'] = $user->{Model_Users::COLUMN_ID};
         }
      }
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_ADMIN_RECIPIENTS])) {
         $elemAdmins->setValues($settings[self::PARAM_ADMIN_RECIPIENTS]);
      }

      $form->addElement($elemAdmins, $grpAdmin);

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_ADMIN_RECIPIENTS] = $form->admins->getValues();
      }
   }
}
?>