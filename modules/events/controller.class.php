<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class Events_Controller extends Controller {

   const DIR_IMAGES = "events";
   const DIR_CAT_IMAGES = "cats";
   const DIR_EVENT_IMAGES = "events";

   protected function init()
   {
      parent::init();
      $this->module()->setDataDir(self::DIR_IMAGES);
   }

   public function mainController()
   {
      $this->checkReadableRights();

      $model = new Events_Model();
      $modelWhere = Events_Model_Categories::COL_ID_CATEGORY . " = :idc AND " . Events_Model::COL_PUBLIC . " = 1";
      $modelBindValues = array('idc' => $this->category()->getId());

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

      // model settings
      $modelWhere .= " AND (" . Events_Model::COL_DATE_FROM . " BETWEEN :dateStart1 AND :dateEnd1 "
         . " OR " . Events_Model::COL_DATE_TO . " BETWEEN :dateStart2 AND :dateEnd2 )";
      $modelBindValues['dateStart1'] = $modelBindValues['dateStart2'] = $dateFrom;
      $modelBindValues['dateEnd1'] = $modelBindValues['dateEnd2'] = $dateTo;
      
      if ($this->getRequestParam('cat', null) != null) {
         $modelWhere .= " AND " . Events_Model::COL_ID_EVE_CATEGORY . " = :idevcat";
         $modelBindValues['idevcat'] = (int) $this->getRequestParam('cat');
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

      $this->view()->events = $this->getSortedEvents($events);
      
      $modelCats = new Events_Model_Categories();
      $this->view()->cats = $modelCats->where(Events_Model_Categories::COL_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))->records();
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
         $record->{Events_Model_Categories::COL_NOTE} = $form->note->getValues();
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
         $record->{Events_Model_Categories::COL_NOTE} = $form->note->getValues();

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
         $form->note->setValues($cat->{Events_Model_Categories::COL_NOTE});
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
      } else if ($this->getRequestParam('token', false)) {
         $allowedCat = $this->getTokenCatAccess($this->getRequestParam('token'));
      } else {
         $allowedCat = false;
      }

      if ($allowedCat === false) {
         return false;
      }

      $this->runEventsActions();

      $model = new Events_Model();
      $modelWhere = Events_Model_Categories::COL_ID_CATEGORY . " = :idc";
      $modelBindValues = array('idc' => $this->category()->getId());

      if ($allowedCat !== null) {
         $modelWhere .= ' AND ' . Events_Model_Categories::COL_ID . " = :idec";
         $modelBindValues['idec'] = $allowedCat->{Events_Model_Categories::COL_ID};
      }

      $dateFrom = new DateTime($this->getRequestParam('dateFrom', date("Y-m-1")));
      $dateTo = new DateTime($this->getRequestParam('dateTo', date("Y-m-t")));
      $this->view()->dateFrom = $dateFrom;
      $this->view()->dateTo = $dateTo;

      // model settings
      $modelWhere .= " AND ( ( " . Events_Model::COL_DATE_TO . " IS NOT NULL AND :dateStart BETWEEN " . Events_Model::COL_DATE_FROM . " AND " . Events_Model::COL_DATE_TO . " )" 
                        ." OR ( " . Events_Model::COL_DATE_TO . " IS NULL AND " . Events_Model::COL_DATE_FROM . " BETWEEN :dateStart2 AND :dateEnd2 ) )";
      
      $modelBindValues['dateStart'] = $modelBindValues['dateStart2'] = $dateFrom;
      $modelBindValues['dateEnd2'] = $dateTo;

      if ($this->getRequestParam('cat', null) != null) {
         $modelWhere .= " AND " . Events_Model::COL_ID_EVE_CATEGORY . " = :idevcat";
         $modelBindValues['idevcat'] = (int) $this->getRequestParam('cat');
      }
      if ($this->getRequestParam('contain', null) != null) {
         $modelWhere .= " AND " . Events_Model::COL_NAME . " LIKE :cnt";
         $modelBindValues['cnt'] = "%" . (string) $this->getRequestParam('contain') . "%";
      }
      if ($this->getRequestParam('onlyPublicAdd', 'off') == 'on') {
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
//      Debug::log($model->getSQLQuery());
      $this->view()->events = $this->getSortedEvents($events);

      $modelCats = new Events_Model_Categories();
      $this->view()->cats = $modelCats->where(Events_Model_Categories::COL_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))->records();
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

   protected function getSortedEvents($records)
   {
      $eventsSorted = array();
      if (!empty($records)) {
         foreach ($records as $event) {
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
         $record->{Events_Model::COL_NOTE} = $form->note->getValues();
         $record->{Events_Model::COL_PLACE} = $form->place->getValues();
         $record->{Events_Model::COL_PRICE} = $form->price->getValues();
         $record->{Events_Model::COL_PUBLIC} = true;
         $record->{Events_Model::COL_PUBLIC_ADD} = $isPublicAdd;
         $record->{Events_Model::COL_IP_ADD} = ip2long($_SERVER['REMOTE_ADDR']);
         $model->save($record);

         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));

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
         $event->{Events_Model::COL_NOTE} = $form->note->getValues();
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
         $form->note->setValues($event->{Events_Model::COL_NOTE});
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

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route('listEvents')->reload();
      }

      return $form;
   }

}
?>