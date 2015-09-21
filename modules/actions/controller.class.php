<?php
class Actions_Controller extends Controller {
   const MAIN_IMAGE_WIDTH = 345;
   const MAIN_IMAGE_HEIGHT = 230;

   const DEFAULT_TIMEWINDOW = 1;
   const DEFAULT_TIMEWINDOW_TYPE = 'month';

   const PARAM_SHOW_EVENT_DIRECTLY = 'sed';
   const PARAM_SHOW_ALL_EVENTS = 'sall';

   protected $action = null;
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController($fromyear = null, $frommonth = null, $fromday = null, $toyear = null, $tomonth = null, $today = null)
   {
      //		Kontrola práv
      $this->checkReadableRights();
      if(!$this->category()->getParam(self::PARAM_SHOW_EVENT_DIRECTLY, false)){
         $this->showEventsList($fromyear, $frommonth, $fromday, $toyear, $tomonth, $today);
      } else {
         // redirect to current event
         $model = new Actions_Model();
         $featured = $model->actualOnly($this->category()->getId())->record();

         if($featured != false){
            $this->link()->route('detail', array('urlkey' => vve_get_lang_string($featured->{Actions_Model::COLUMN_URLKEY})))->reload();
         } else {
            $this->showEventsList($fromyear, $frommonth, $fromday, $toyear, $tomonth, $today);
         }
      }
   }

   protected function showEventsList($fromyear = null, $frommonth = null, $fromday = null, $toyear = null, $tomonth = null, $today = null)
   {
      // uložení datumu do session pokud existuje - kvuli návratu
      // odkaz zpět
      $this->link()->backInit();
      
//      Debug::log($fromyear.'-'.$frommonth.'-'.$fromday, $toyear.'-'.$tomonth.'-'.$today);
      
      
      $windowLen = $this->category()->getParam('time', self::DEFAULT_TIMEWINDOW);
      $windowType = $this->category()->getParam('type', self::DEFAULT_TIMEWINDOW_TYPE);
      
      // výchozí posuny
      $dateStart = new DateTime();
      $dateEnd = new DateTime();
//      $dateStart->setTime(0, 0, 0);
      $dateEnd->setTime(23, 59, 59);
      if(!$fromyear && !$frommonth && !$fromday){
      } else {
         $dateStart->setTime(0, 0, 0);
         if($frommonth && $fromday){
            // rok, měs, den
            $dateStart->setDate($fromyear, $frommonth, $fromday);
            $dateEnd->setDate($fromyear, $frommonth, $fromday);
            $windowType = 'day';
         } else if($frommonth){
            // rok, měs
            $dateStart->setDate($fromyear, $frommonth, 1);
            $dateEnd->setDate($fromyear, $frommonth, $dateStart->format('t'));
            $windowType = 'month';
         } else {
            // rok
            $dateStart->setDate($fromyear, 1, 1);
            $dateEnd->setDate($fromyear, 12, 31);
            $windowType = 'year';
         }
         $windowLen = 1;
      }
     
      
      if(!$toyear && !$tomonth && !$today){
         // není datum konce - nastav podle časového okna
         $c = clone $dateStart;
         $c->modify('+ '.$windowLen.' '.$windowType);
         $c->modify('-1 day');
         $dateEnd->setDate($c->format('Y'), $c->format('m'), $c->format('d'));
      } else {
         if($tomonth && $today){
            // rok, měs, den
            $dateEnd->setDate($toyear, $tomonth, $today);    
         } else if($tomonth){
            // rok, měs
            $dateEnd->setDate($toyear, $tomonth, 31);    
         } else {
            // rok
            $dateEnd->setDate($toyear, 12, 31);    
         }
      }
      
      // počítání nového okna z datuml a vytvoření datumů na předchozí a následující období
      $datePrevStart = new DateTime();
      $datePrevEnd = clone $dateStart;
      $dateNextStart = clone $dateEnd;
      $dateNextEnd = new DateTime();
      
      
      if(!$this->category()->getParam(self::PARAM_SHOW_ALL_EVENTS, false)){
         $actions = $this->getEvents($dateStart, $dateEnd);
      } else {
         $actions = $this->getAllEvents($dateStart, $dateEnd);
      }
      
      $this->view()->actions = $actions;
      $this->view()->windowType = $windowType;
      $this->view()->windowLen = $windowLen;
      $this->view()->dateStart = $dateStart;
      $this->view()->dateEnd = $dateEnd;
      
      $modelAct = new Actions_Model();
      $firstAct = $modelAct
          ->where(Actions_Model::COLUMN_ID_CAT." = :idc", array('idc' => $this->category()->getId()))
          ->order(array(Actions_Model::COLUMN_DATE_START))
          ->record();
      
      $this->view()->firstActionDate = $firstAct ? new DateTime($firstAct->{Actions_Model::COLUMN_DATE_START}) : false;
      return;
   }
   
   protected function getEvents($dateStart, $dateEnd)
   {
      return Actions_Model::getActions(
             $this->category()->getId(), // idc
             $dateStart, $dateEnd, // rozsah
             !Auth::isAdmin() // restrikca uživatele
             );
   }
   
   protected function getAllEvents()
   {
      $model = new Actions_Model();
      return $model
             ->where(Actions_Model::COLUMN_ID_CAT." = :idc", array('idc' => $this->category()->getId()))
             ->order(array(Actions_Model::COLUMN_DATE_START, Actions_Model::COLUMN_TIME))
             ->records();
   }

   protected function showEvent($urlkey = null)
   {
      $model = new Actions_Model();
      $this->view()->action = $model
      ->joinFK(Actions_Model::COLUMN_ID_USER)
      ->where(Actions_Model::COLUMN_ID_CAT." = :idc AND ".Actions_Model::COLUMN_URLKEY." = :urlkey",
            array( 'urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId() ) )
            ->record();
      if($this->view()->action == false) {
         // try default language
         $this->view()->action = $model
         ->joinFK(Actions_Model::COLUMN_ID_USER)
         ->where(Actions_Model::COLUMN_ID_CAT." = :idc AND (".Locales::getDefaultLang().")".Actions_Model::COLUMN_URLKEY." = :urlkey",
               array( 'urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId() ) )
               ->record();

         if($this->view()->action == false){
            throw new UnexpectedPageException();
         }
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);

      // práva k zápisu a kontrole
      if($this->rights()->isWritable() OR $this->rights()->isControll()) {
         // mazání akce
         $delForm = new Form('action_');

         $elemId = new Form_Element_Hidden('id');
         $delForm->addElement($elemId);

         $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
         $delForm->addElement($elemSubmit);
         if($delForm->isValid()) {
            $this->deleteAction($this->view()->action);
         }
         $this->view()->formDelete = $delForm;
      }

      $this->view()->imagesDir = self::getActionImgDir($this->view()->action);

      if($this->view()->action->{Actions_Model::COLUMN_FORM} != null){
         $date = new DateTime($this->view()->action->{Actions_Model::COLUMN_FORM_SHOW_TO});
         $curDate = new DateTime();
         $curDate->setTime(0, 0, 0);
         if($this->view()->action->{Actions_Model::COLUMN_FORM_SHOW_TO} == null
            || $date >= $curDate){
            $this->view()->dForm = Forms_Controller::dynamicForm($this->view()->action->{Actions_Model::COLUMN_FORM},
               array(
                  'pagename'=> $this->view()->action->{Actions_Model::COLUMN_NAME},
                  ));
         }
      }
   }

   public function showController()
   {
      $this->checkReadableRights();
      $this->showEvent($this->getRequest('urlkey'));
   }

   public function previewController()
   {
      //		Kontrola práv
      $this->checkWritebleRights();

      $rec = $this->loadTempEvent($this->getRequest('id'));
      if(!$rec ){
         $this->link()->route()->reload();
      }
      $this->view()->action = $rec;
      $this->view()->imagesDir = self::getActionImgDir($rec);

      $formPreview = new Form('text_preview_');
      $grp = $formPreview->addGroup('preview', $this->tr('Co s obsahem?'));

      $elemSubmit = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zpět k úpravě')) );
      $elemSubmit->setCancelConfirm(false);
      $formPreview->addElement($elemSubmit, $grp);
      
//      $eLang = new Form_Element_Select('lang', $this->tr('Jazyk'));
//      foreach (Locales::getAppLangsNames() as $lang => $name) {
//         $eLang->setOptions(array( $name => $lang), true);
//      }
//      $elemActions->addElement($eLang);

//      $formPreview->addElement($elemActions);

      if($formPreview->isSend()){
         if($formPreview->save->getValues() == false){
            // rediret to edit
            if($this->getRequest('id') == 0){
               $this->link()->route('add')->param('tmp', true)->reload();
            } else {
               $this->link()->route('edit', array('urlkey' => $rec->{Actions_Model::COLUMN_URLKEY}) )->param('tmp', true)->reload();
            }
         } else {
            // store and show new event
            $this->storeEvent($rec);
            $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
            $this->link()->route('detail', array('urlkey' => $rec->{Actions_Model::COLUMN_URLKEY} ))->param('tmp')->reload();
         }
      }

      $this->view()->formPreview = $formPreview;
   }

   protected function deleteAction($action)
   {
      $this->deleteActionData($action);
      $this->infoMsg()->addMessage(sprintf($this->tr('Událost "%s" byla smazána'), $action->{Actions_Model_Detail::COLUMN_NAME}));
      $this->link()->reload($this->view()->linkBack);
   }

   protected function deleteActionData($action)
   {
      // obrázek akce
//       if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
//          $fileObj = new Filesystem_File($action->{Actions_Model_Detail::COLUMN_IMAGE},
//                  $this->category()->getModule()->getDataDir()
//                          .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
//          try {
//             $fileObj->delete();
//          } catch (Exception $e) {
//             $this->log('Soubor '.$fileObj->getName(true)." se nepodařilo smazat.");
//          }
//       }
      // smazání adresáře
      $dir = $this->category()->getModule()->getDataDir().$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()];
      if(file_exists($dir) AND is_dir($dir)) {
         $dir = new Filesystem_Dir($dir);
         $dir->rmDir();
      }

      $model = new Actions_Model_Detail();
      $model->deleteAction($action->{Actions_Model_Detail::COLUMN_ID});
   }

   /**
    * What is this ?
    */
   public function detailExportController()
   {
      $this->checkReadableRights();
      // načtení článku
      $this->view()->action = Actions_Model::getByUrlkey($this->getRequest('urlkey'));
      if($this->view()->action == false) return false;

      $this->view()->output = $this->getRequest('output');
   }

   public function archiveController()
   {
      $this->checkReadableRights();
      $this->link()->backInit();
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController()
   {
      $this->checkWritebleRights();

      $event = null;
      if($this->isTempRecord(0)){
         if($this->getRequestParam('tmpclear', false)){
            $this->clearTempEvent(0);
            $this->infoMsg()->addMessage($this->tr('Rozpracovaná verze byla zrušena'));
            $this->link()->param('tmpclear')->reload();
         }

         // pokud je uložen náhled události
         if(isset($_GET['tmp'])){
            // pokud se má načíst temp
            $event = $this->loadTempEvent(0);
         } else {
            // zobraz upozornění na obnovu
            $this->view()->previewLink = $this->link()->param('tmp', true);
            $this->view()->previewLinkCancel = $this->link()->param('tmpclear', true);
         }
      }

      $form = $this->createForm($event);

      // kontrola integrity data
      if($form->isSend()){
         if($form->send->getValues() == 'cancel'){
            $this->link()->route()->reload();
         }
         if($form->date_stop->isValid() && $form->date_start->isValid()
            && $form->date_start->getValues() != null && $form->date_stop->getValues() != null
            && ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))) {
               $form->date_stop->setError($this->tr('Konečné datum končí dříve než datum začátku'));
         }
      }

      if($form->isValid()) {
         $model = new Actions_Model();
         $event = $model->newRecord();

         if($form->send->getValues() == 'save'){
            $this->storeEvent($event, $form);
            $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
            $this->link()->route('detail', array('urlkey' => $event->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
         } else if($form->send->getValues() == 'preview'){
            $this->storeEvent($event, $form, true);
            $this->link()->route('preview', array('id' => $event->{Actions_Model::COLUMN_ID}) )->reload();
         }
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);

      $this->view()->form = $form;
      $this->view()->edit = false;
   }
   
   /**
    * Kontroler pro přesun akce mezi kategoriemi
    */
   public function moveController($urlkey)
   {
      $this->checkWritebleRights();

      $event = Actions_Model::getByUrlkey($urlkey);
      
      if(!$event){
         throw new UnexpectedPageException();
      }
      
      $form = new Form('action_move_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->setValues($event->{Actions_Model::COLUMN_ID});
      $form->addElement($elemId);
      
//      $cats = Model_Category::getCategoryListByModule(array('actions', 'actionswgal'));
      $eNewCat = new Form_Element_Select('idcat', $this->tr('Nová kateogrie'));
      $struct = Category_Structure::getStructure();
      $ret = $this->getSimilarCats($struct);
      $eNewCat->setOptions($ret);
      
      $eNewCat->setValues($this->category()->getId());
      $form->addElement($eNewCat);
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);
          
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('show')->redirect();
      }

      if($form->isValid()) {
         $this->moveEvent($event, $form->idcat->getValues());
         $catkey = Category_Structure::getStructure()->getCategory($form->idcat->getValues())->getCatObj()->getUrlKey();
         $this->infoMsg()->addMessage($this->tr('Akce byla přesunuta'));
         $this->log(sprintf('Přesun akce %s id: %s mezi kategoriemi', $event->{Actions_Model::COLUMN_NAME}, $event->getPK()));
         $this->link()->category($catkey)->route('detail')->redirect();
      }
      $this->view()->form = $form;
      $this->view()->action = $event;
   }
   
   protected function moveEvent(Model_ORM_Record $event, $targetCatID)
   {
      $event->{Actions_Model::COLUMN_ID_CAT} = $targetCatID;
      $event->save();
   }
   
   protected function getSimilarCats($struct, &$ret = array(), $level = 0) 
   {
      foreach ($struct as $item) {
         /* @var $item Category_Structure */
         if(in_array($item->getCatObj()->getModule()->getName(), array('actions', 'actionswgal')) && $item->getId() != $this->category()->getId()){
            $ret[$item->getId()] = str_repeat('...', $level).$item->getCatObj()->getName()." (ID: ".$item->getId().')';
         }
         if(!$item->isEmpty()){
            $this->getSimilarCats($item, $ret, $level+1);
         }
      }
      return $ret;
   }

   
   /**
    * controller pro úpravu akce
    */
   public function editController()
   {
      $this->checkWritebleRights();

      $model = new Actions_Model();
      $event = $model->where(Actions_Model::COLUMN_ID_CAT." = :idc AND ".Actions_Model::COLUMN_URLKEY." = :urlkey",
         array( 'urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId() ) )
         ->record();

      if(!$event) {
         $event = $model->where(Actions_Model::COLUMN_ID_CAT." = :idc AND (".Locales::getDefaultLang().")".Actions_Model::COLUMN_URLKEY." = :urlkey",
            array( 'urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId() ) )
            ->record();
         if(!$event){
            throw new UnexpectedPageException();
         }
      }

      if($this->isTempRecord($event->getPK())){
         if($this->getRequestParam('tmpclear', false)){
            $this->clearTempEvent($event->getPK());
            $this->infoMsg()->addMessage($this->tr('Rozpracovaná verze byla zrušena'));
            $this->link()->param('tmpclear')->reload();
         }

         // pokud je uložen náhled události
         if(isset($_GET['tmp'])){
            // pokud se má načíst temp
            $event = $this->loadTempEvent($event->getPK());
         } else {
            // zobraz upozornění na obnovu
            $this->view()->previewLink = $this->link()->param('tmp', true);
            $this->view()->previewLinkCancel = $this->link()->param('tmpclear', true);
         }
      }

      $form = $this->createForm($event, true);

      // kontrola integrity data
      if($form->isSend()){
         if($form->send->getValues() == false){
            $this->link()->route('detail')->reload();
         }
         if($form->date_stop->isValid() && $form->date_start->isValid()
            && $form->date_start->getValues() != null && $form->date_stop->getValues() != null
            && ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))) {
               $form->date_stop->setError($this->tr('Konečné datum končí dříve než datum začátku'));
         }
      }
      if($form->isValid()) {
         if($form->send->getValues() == 'save'){
            $this->storeEvent($event, $form);
            $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
            $this->link()->route('detail', array('urlkey' => $event->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
         } else if($form->send->getValues() == 'preview'){
            $this->storeEvent($event, $form, true);
            $this->link()->route('preview', array('id' => $event->{Actions_Model::COLUMN_ID}) )->reload();
         }
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 1);

      $this->view()->form = $form;
      $this->view()->edit = true;
      $this->view()->action = $event;
   }

   /**
    * Vytvoří formulář pro úpravu akce
    * @return From
    */
   protected function createForm(Model_ORM_Record $actRecord = null)
   {
      $form = new Form('action_');

      $fGrpBase = $form->addGroup('base', $this->tr('Základní informace'));

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->setLangs();
      $eName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eName, $fGrpBase);

      $esName = new Form_Element_Text('subname', $this->tr('Pod název'));
      $esName->setLangs();
      $form->addElement($esName, $fGrpBase);

      $eAuthor = new Form_Element_Text('author', $this->tr('Autor/ účinkující'));
      $form->addElement($eAuthor, $fGrpBase);

      $eText = new Form_Element_TextArea('text', $this->tr('Text'));
      $eText->setLangs();
      $eText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eText, $fGrpBase);

      $eNote = new Form_Element_Text('note', $this->tr('Poznámka'));
      $eNote->setLangs();
      $eNote->html()->setAttrib('size', 50);
      $form->addElement($eNote, $fGrpBase);

      $fGrpParams = $form->addGroup('params', $this->tr('Parametry konání'));

      $eDateS = new Form_Element_Text('date_start', $this->tr('Od'));
      $eDateS->addValidation(new Form_Validator_NotEmpty());
      $eDateS->addValidation(new Form_Validator_Date());
      $eDateS->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateS, $fGrpParams);

      $eDateT = new Form_Element_Text('date_stop', $this->tr('Do'));
      $eDateT->addValidation(new Form_Validator_Date());
      $eDateT->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateT, $fGrpParams);

      $eTime = new Form_Element_Text('time', $this->tr('Čas konání'));
      $eTime->setSubLabel($this->tr('Pokud akce nemá čas začátku, nechte prázdné'));
      $eTime->addValidation(new Form_Validator_Time());
      $form->addElement($eTime, $fGrpParams);

      $ePlace = new Form_Element_Text('place', $this->tr('Místo konání'));
      $form->addElement($ePlace, $fGrpParams);

      $ePrice = new Form_Element_Text('price', $this->tr('Cena'));
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice, $fGrpParams);

      $ePrePrice = new Form_Element_Text('preprice', $this->tr('Cena v předprodeji'));
      $ePrePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrePrice, $fGrpParams);

      $fGrpOther = $form->addGroup('other', $this->tr('Ostatní'));

      $ePub = new Form_Element_Checkbox('public', $this->tr('Veřejný'));
      $ePub->setSubLabel($this->tr('Veřejný - viditelný všem návštěvníkům'));
      $ePub->setValues(true);
      $form->addElement($ePub, $fGrpOther);

      $elemImage = new Form_Element_ImageSelector('image', $this->tr('Titulní obrázek'));
      $elemImage->setUploadDir(Utils_CMS::getTitleImagePath(false));
      $form->addElement($elemImage, $fGrpParams);

      $eUrlKey = new Form_Element_Text('urlkey', $this->tr('Url klíč'));
      $eUrlKey->setLangs();
      $eUrlKey->setSubLabel($this->tr('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($eUrlKey, $fGrpOther);

      $fModel = new Forms_Model();
      $forms = $fModel->getForms();
      if(!empty($forms)){
         $eForm = new Form_Element_Select('form', $this->tr('Přiřazený formulář'));
         $eForm->setOptions(array('0' => $this->tr('Žádný')), true);
         foreach ($forms as $f) {
            $eForm->setOptions(array($f->{Forms_Model::COLUMN_ID} => $f->{Forms_Model::COLUMN_NAME}), true);
         }
         $form->addElement($eForm, $fGrpOther);

         $eFormShowTo = new Form_Element_Text('formShowToDate', $this->tr('Formulář zobrazit do'));
         $eFormShowTo->setSubLabel($this->tr('Pro neomezené zobrazení formuláře nechte prázdné'));
         $eFormShowTo->addValidation(new Form_Validator_Date());
         $eFormShowTo->addFilter(new Form_Filter_DateTimeObj());
         $form->addElement($eFormShowTo, $fGrpOther);
      }

//       $eSub = new Form_Element_SaveCancel('save');
//       $form->addElement($eSub);
      $submits = new Form_Element_Multi_Submit('send');
      $submits->addElement(new Form_Element_Submit('cancel', $this->tr('Zrušit')));
      $submits->addElement(new Form_Element_Submit('save', $this->tr('Uložit')));
      $submits->addElement(new Form_Element_Submit('preview', $this->tr('Náhled')));
      $form->addElement($submits);


      if ($actRecord instanceof Model_ORM_Record){
         $form->name->setValues($actRecord->{Actions_Model::COLUMN_NAME});
         $form->text->setValues($actRecord->{Actions_Model::COLUMN_TEXT});
         $form->note->setValues($actRecord->{Actions_Model::COLUMN_NOTE});
         $dateS = new DateTime($actRecord->{Actions_Model::COLUMN_DATE_START});
         $form->date_start->setValues(strftime("%x",$dateS->format("U")));
         if($actRecord->{Actions_Model::COLUMN_DATE_START} != $actRecord->{Actions_Model::COLUMN_DATE_STOP}
            AND $actRecord->{Actions_Model::COLUMN_DATE_STOP} != null) {
            $dateE = new DateTime($actRecord->{Actions_Model::COLUMN_DATE_STOP});
            $form->date_stop->setValues(strftime("%x",$dateE->format("U")));
         }
         $form->urlkey->setValues($actRecord->{Actions_Model::COLUMN_URLKEY});
         $form->public->setValues($actRecord->{Actions_Model::COLUMN_PUBLIC});
         $form->time->setValues($actRecord->{Actions_Model::COLUMN_TIME});
         $form->place->setValues($actRecord->{Actions_Model::COLUMN_PLACE});
         $form->price->setValues($actRecord->{Actions_Model::COLUMN_PRICE});
         $form->preprice->setValues($actRecord->{Actions_Model::COLUMN_PREPRICE});
         $form->author->setValues($actRecord->{Actions_Model::COLUMN_AUTHOR});
         $form->subname->setValues($actRecord->{Actions_Model::COLUMN_SUBANME});
         if($form->image){
            $form->image->setValues($actRecord->{Actions_Model::COLUMN_IMAGE});
         }

         if(isset ($form->form)){
            $form->form->setValues($actRecord->{Actions_Model::COLUMN_FORM});
            if($actRecord->{Actions_Model::COLUMN_FORM_SHOW_TO} != null){
               $form->formShowToDate->setValues(vve_date("%x", new DateTime($actRecord->{Actions_Model::COLUMN_FORM_SHOW_TO})));
            }
         }
      }

      return $form;
   }

   protected function storeEvent(Model_ORM_Record $eventRec, $form = null, $asTmp = false)
   {
      if($form instanceof Form){
         // generování url klíče
         $urlkeys = $form->urlkey->getValues();
         $names = $form->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names, $eventRec->{Actions_Model::COLUMN_ID});

         $eventRec->{Actions_Model::COLUMN_ID_CAT} = $this->category()->getId();
         $eventRec->{Actions_Model::COLUMN_NAME} = $form->name->getValues();
         $eventRec->{Actions_Model::COLUMN_SUBANME} = $form->subname->getValues();
         $eventRec->{Actions_Model::COLUMN_AUTHOR} = $form->author->getValues();
         $eventRec->{Actions_Model::COLUMN_TEXT} = $form->text->getValues();
         $eventRec->{Actions_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues());
         $eventRec->{Actions_Model::COLUMN_NOTE} = $form->note->getValues();
         $eventRec->{Actions_Model::COLUMN_URLKEY} = $urlkeys;
         $eventRec->{Actions_Model::COLUMN_DATE_START} = $form->date_start->getValues();
         $eventRec->{Actions_Model::COLUMN_DATE_STOP} = $form->date_stop->getValues();
         $eventRec->{Actions_Model::COLUMN_TIME} = $form->time->getValues();
         $eventRec->{Actions_Model::COLUMN_PLACE} = $form->place->getValues();
         $eventRec->{Actions_Model::COLUMN_PRICE} = $form->price->getValues();
         $eventRec->{Actions_Model::COLUMN_PREPRICE} = $form->preprice->getValues();
         $eventRec->{Actions_Model::COLUMN_ADDED} = new DateTime();
         $eventRec->{Actions_Model::COLUMN_CHANGED} = new DateTime();

         $eventRec->{Actions_Model::COLUMN_ID_USER} = Auth::getUserId();
         $eventRec->{Actions_Model::COLUMN_PUBLIC} = $form->public->getValues();

         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null) {
            $img = $form->image->getValues();
            $eventRec->{Actions_Model::COLUMN_IMAGE} = $img['name'];
         }
         
         if(isset($form->form)){
            $eventRec->{Actions_Model::COLUMN_FORM} = $form->form->getValues();
            $eventRec->{Actions_Model::COLUMN_FORM_SHOW_TO} = $form->formShowToDate->getValues();
         }
      }

      if($asTmp){
         $f = $this->getTempFileName($eventRec->isNew() ? 0 : $eventRec->getPK());
         file_put_contents($f, serialize($eventRec));
      } else {
         $model = new Actions_Model();
         if( !$eventRec->isNew()){
            // vytvoření nových klíčů, pro jistotu
            $eventRec->{Actions_Model::COLUMN_URLKEY} = $this->createUrlKey(
               $eventRec->{Actions_Model::COLUMN_URLKEY},
               $eventRec->{Actions_Model::COLUMN_NAME},
               $eventRec->{Actions_Model::COLUMN_ID} );

            // přejmenování adresáře s daty (fotky, soubory, atd)
            $oldEvent = $model->record($eventRec->getPK());
            $oldDir = $oldEvent[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()];
            $newDir = $eventRec[Actions_Model::COLUMN_URLKEY][Locales::getDefaultLang()];
            // přesun adresáře pokud existuje
            if($oldDir != $newDir AND is_dir($this->category()->getModule()->getDataDir().$oldDir)){
               rename($this->category()->getModule()->getDataDir().$oldDir, $this->category()->getModule()->getDataDir().$newDir);
            }
         }
         $model->save($eventRec);
         $this->clearTempEvent( $eventRec->isNew() ? 0 : $eventRec->getPK() );
      }
   }

   /**
    * Metoda vygeneruje url klíče
    * @param <type> $urlkeys
    * @param <type> $names
    * @return <type>
    */
   protected function createUrlKey($urlkeys, $names, $id = 0) {
      foreach ($urlkeys as $lang => $key) {
         if($key == null AND $names[$lang] == null) {
            $urlkeys[$lang] = null;
         } else if($key == null) {
            $urlkeys[$lang] = $names[$lang];
         } else {
            $urlkeys[$lang] = $key;
         }
         // kontrola unikátnosti
         $urlkeys[$lang] = $this->createUniqueUrlKey($urlkeys[$lang], $lang, $id);
      }
      return $urlkeys;
   }

   protected function createUniqueUrlKey($key, $lang, $id = 0)
   {
      if($key == null){
         return null;
      }

      $model = new Actions_Model();
      $step = 1;
      $key = vve_cr_url_key($key);
      $origPart = $key;

      $where = '('.$lang.')'.Actions_Model::COLUMN_URLKEY.' = :ukey AND '.Actions_Model::COLUMN_ID.' != :id';
      $keys = array('ukey' => $key, 'id' => (int)$id);// when is nul bad sql query is created

      while ($model->where($where, $keys)->count() != 0 ) {
         $keys['ukey'] = $origPart.'-'.$step;
         $step++;
      }

      return $keys['ukey'];
   }

   public function editLabelController() {
      $this->checkControllRights();
      $form = new Form('modlabel');

      $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_SaveCancel('save');
      $form->addElement($elemS);

      if($form->isSend() AND $form->save->getValues() == false){
          $this->link()->route()->reload();
      }

      if($form->isValid()) {
         $textM = new Text_Model();
         $text = $textM->where(Text_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
            ->setSelectAllLangs(true)->record();

         if(!$text){
            $text = $textM->newRecord();
            $text->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         }

         $text->{Text_Model::COLUMN_ID_USER_EDIT} = Auth::getUserId();
         $text->{Text_Model::COLUMN_TEXT} = $form->text->getValues();
         $text->{Text_Model::COLUMN_TEXT} = vve_strip_tags( $form->text->getValues() );

         $this->infoMsg()->addMessage($this->tr('Úvodní text byl uložen'));
         $this->link()->route()->reload();
      }

      // načtení textu
      $text = $this->loadActionsText();
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $this->view()->form = $form;
   }

   private function loadActionsText() {
      $textM = new Text_Model();
      $text = $textM->getText($this->category()->getId());
      return $text;
   }

   public function featuredListController() {
      $this->checkReadableRights();
      $model = new Actions_Model();
      $to = new DateTime();
      $to->modify('+1 year');
      
      $this->view()->actions = $model->getActions($this->category()->getId(), new DateTime(), $to);
      if($this->view()->action === false) return false;
   }

   public function currentActController() {
      $this->checkReadableRights();
      $this->view()->action = Actions_Model::getCurrentAction($this->category()->getId(), (int)$this->getRequestParam('from', 0));
//      if($this->view()->action === false) return false;
   }

   protected function loadTempEvent($ide = 0)
   {
      $f = $this->getTempFileName($ide);
      if(is_file($f) && filesize($f) > 0){
         $event = unserialize(file_get_contents($f));
         if($event instanceof Model_ORM_Record){
            return $event;
         }
      }
      return false;
   }

   protected function clearTempEvent($ida = 0)
   {
      $f = $this->getTempFileName($ida);
      if(is_file($f)){
         @unlink($f);
      }
   }

   protected function isTempRecord($ida = 0)
   {
      $f = $this->getTempFileName($ida);
      return ( is_file($f) && filesize($f) > 0 );
   }

   protected function getTempFileName($ida = 0)
   {
      return AppCore::getAppCacheDir()."_prew_c".$this->category()->getId().'_'.(int)$ida."_u".Auth::getUserId().".tmp";
   }

   public static function getActionImgDir(Model_ORM_Record $action)
   {
      return $action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()];
   }

   protected function settings(&$settings,Form &$form) {
      $elemTimeWindow = new Form_Element_Text('time', 'Délka časového okna');
      $elemTimeWindow->setSubLabel('Udává délku časového okna, pomocí kterého se vybírají zobrazené události.<br /> Výchozí: '.self::DEFAULT_TIMEWINDOW.'');
      $elemTimeWindow->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemTimeWindow,'view');

      if(isset($settings['time'])) {
         $form->time->setValues($settings['time']);
      }

      $elemTimeWindowType = new Form_Element_Select('type', 'Jednotka časového okna');
      $types = array('Den' => 'day', 'Měsíc' => 'month', 'Rok' => 'year');
      $elemTimeWindowType->setOptions($types);
      $elemTimeWindowType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TIMEWINDOW_TYPE, $types).'');
      $form->addElement($elemTimeWindowType,'view');

      $elemShowEventDirectly = new Form_Element_Checkbox('show_event_directly', 'Zobrazit aktuální akci přímo');
      $elemShowEventDirectly->setValues(false);
      $form->addElement($elemShowEventDirectly, 'view');
      if(isset($settings[self::PARAM_SHOW_EVENT_DIRECTLY])) {
         $form->show_event_directly->setValues($settings[self::PARAM_SHOW_EVENT_DIRECTLY]);
      }

      $elemShowAllEvents = new Form_Element_Checkbox('show_all_events', 'Zobrazit všechny události');
      $elemShowAllEvents->setValues(false);
      $form->addElement($elemShowAllEvents, 'view');
      if(isset($settings[self::PARAM_SHOW_ALL_EVENTS])) {
         $form->show_event_directly->setValues($settings[self::PARAM_SHOW_ALL_EVENTS]);
      }

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      } else {
         $form->type->setValues(self::DEFAULT_TIMEWINDOW_TYPE);
      }

      if($form->isValid()) {
         $settings['time'] = $form->time->getValues();
         $settings[self::PARAM_SHOW_EVENT_DIRECTLY] = $form->show_event_directly->getValues();
         $settings[self::PARAM_SHOW_ALL_EVENTS] = $form->show_all_events->getValues();
         // protože je vždy hodnota
         if($form->type->getValues() != self::DEFAULT_TIMEWINDOW_TYPE){
            $settings['type'] = $form->type->getValues();
         } else {
            $settings['type'] = self::DEFAULT_TIMEWINDOW_TYPE;
         }
      }
   }
}
