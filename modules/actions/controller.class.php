<?php
class Actions_Controller extends Controller {
   const MAIN_IMAGE_WIDTH = 345;
   const MAIN_IMAGE_HEIGHT = 230;

   const DEFAULT_TIMEWINDOW = 1;
   const DEFAULT_TIMEWINDOW_TYPE = 'month';

   const PARAM_SHOW_EVENT_DIRECTLY = 'sed';


   protected $action = null;
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      if(!$this->category()->getParam(self::PARAM_SHOW_EVENT_DIRECTLY, false)){
         $this->showEventsList();
      } else {
         // redirect to current event
         $model = new Actions_Model();
         $featured = $model->actualOnly($this->category()->getId())->record();
         
         if($featured != false){
            $this->link()->route('detail', array('urlkey' => $featured->{Actions_Model::COLUMN_URLKEY}))->reload();
         } else {
            $this->showEventsList();
         }
      }
   }
   
   protected function showEventsList() 
   {
      // uložení datumu do session pokud existuje - kvuli návratu
      // odkaz zpět
      $this->link()->backInit();

      if($this->getRequest('month') != null) {
         $currentDateO = new DateTime($this->getRequest('year', date("Y")).'-'
                         .$this->getRequest('month', date("m")).'-'
                         .$this->getRequest('day', date("d")));
      } else {
         $currentDateO = new DateTime();
      }

      $dateNext = clone $currentDateO;
      $datePrev = clone $currentDateO;
      $time = $this->category()->getParam('time', self::DEFAULT_TIMEWINDOW);
      switch (strtolower($this->category()->getParam('type', self::DEFAULT_TIMEWINDOW_TYPE))) {
         case 'year':
            $dateNext->modify("+".$time." year");
            $datePrev->modify("-".$time." year");
            $linkNextLabel = "+ ".$this->tr(array('%s rok','%s roky','%s let') ,(int)$time);
            $linkBackLabel = "- ".$this->tr(array('%s rok','%s roky','%s let') ,(int)$time);
            break;
         case 'month':
            $dateNext->modify("+".$time." month");
            $datePrev->modify("-".$time." month");
            $linkNextLabel = "+ ".$this->tr(array('%s měsíc','%s měsíce','%s měsíců') ,(int)$time);
            $linkBackLabel = "- ".$this->tr(array('%s měsíc','%s měsíce','%s měsíců') ,(int)$time);
            break;
         case 'day':
         default:
            $dateNext->modify("+".$time." day");
            $datePrev->modify("-".$time." day");
            $linkNextLabel = "- ".$this->tr(array('%s den','%s dny','%s dnů') ,(int)$time);
            $linkBackLabel = "- ".$this->tr(array('%s den','%s dny','%s dnů') ,(int)$time);
            break;
      }

      $acM = new Actions_Model_List();
      $actions = $acM->getActions($this->category()->getId(), $currentDateO, $dateNext,
              !$this->getRights()->isWritable());

      $this->view()->actions = $actions->fetchAll();
      $this->view()->dateFrom = $currentDateO;
      $this->view()->dateTo = $dateNext;

      // link další
      $this->view()->linkNext = $this->link()->route('normaldate',
              array('day' => $dateNext->format('j') , 'month' => $dateNext->format('n'),
              'year' => $dateNext->format('Y')));
      $this->view()->linkNextLabel = $linkNextLabel;
      // link předchozí
      $this->view()->linkBack = $this->link()->route('normaldate',
              array('day' => $datePrev->format('j') , 'month' => $datePrev->format('n'),
              'year' => $datePrev->format('Y')));
      $this->view()->linkBackLabel = $linkBackLabel;

      // načtení textu
      $this->view()->text = $this->loadActionsText();
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
         return false;
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

      // komponenta pro vypsání odkazů na sdílení
      $shares = new Component_Share();
      $shares->setConfig('url', (string)$this->link());
      $shares->setConfig('title', $this->view()->action->{Actions_Model_Detail::COLUMN_NAME});
      $this->view()->shares=$shares;
      $this->view()->imagesDir=$this->view()->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()];
      
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

   public function showController() {
      $this->checkReadableRights();
      $this->showEvent($this->getRequest('urlkey'));
   }

   protected function deleteAction($action) {
      $this->deleteActionData($action);
      $this->infoMsg()->addMessage(sprintf($this->tr('Událost "%s" byla smazána'), $action->{Actions_Model_Detail::COLUMN_NAME}));
      $this->link()->reload($this->view()->linkBack);
   }

   protected function deleteActionData($action) {
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

   public function showDataController() {
      $this->checkReadableRights();
      // načtení článku
      $model = new Actions_Model_Detail();
      $this->view()->action = $model->getAction($this->getRequest('urlkey'), $this->category()->getId());
      if($this->view()->action == false) return false;

      $this->view()->output = $this->getRequest('output');
   }

   public function archiveController() {
      $this->checkReadableRights();
      $this->link()->backInit();
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();

      $form = $this->createForm();

      // kontrola integrity data
      if($form->isSend()){
         if($form->save->getValues() == false){
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
         $action = $model->newRecord();
         
         // vybrání exist. obrázku
         if(isset ($form->titleImage)){
            $action->{Actions_Model::COLUMN_IMAGE} = $form->titleImage->getValues();
         }
         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null) {
            $imageObj = new File_Image($form->image);
            $crop = $this->category()->getParam('img_crop', VVE_ARTICLE_TITLE_IMG_C) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO;
            $imageObj->getData()->resize(
               $this->category()->getParam('img_width', VVE_ARTICLE_TITLE_IMG_W), 
               $this->category()->getParam('img_height', VVE_ARTICLE_TITLE_IMG_H), 
               $crop
               )->save();
            $imageObj->move(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR);
            $action->{Actions_Model::COLUMN_IMAGE} = $imageObj->getName();
         }
         
         // generování url klíče
         $urlkeys = $form->urlkey->getValues();
         $names = $form->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names, $action->{Actions_Model::COLUMN_ID});
         
         $action->{Actions_Model::COLUMN_ID_CAT} = $this->category()->getId();
         $action->{Actions_Model::COLUMN_NAME} = $form->name->getValues();
         $action->{Actions_Model::COLUMN_SUBANME} = $form->subname->getValues();
         $action->{Actions_Model::COLUMN_AUTHOR} = $form->author->getValues();
         $action->{Actions_Model::COLUMN_TEXT} = $form->text->getValues();
         $action->{Actions_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues());
         $action->{Actions_Model::COLUMN_NOTE} = $form->note->getValues();
         $action->{Actions_Model::COLUMN_URLKEY} = $urlkeys;
         $action->{Actions_Model::COLUMN_DATE_START} = $form->date_start->getValues();
         $action->{Actions_Model::COLUMN_DATE_STOP} = $form->date_stop->getValues();
         $action->{Actions_Model::COLUMN_TIME} = $form->time->getValues();
         $action->{Actions_Model::COLUMN_PLACE} = $form->place->getValues();
         $action->{Actions_Model::COLUMN_PRICE} = $form->price->getValues();
         $action->{Actions_Model::COLUMN_PREPRICE} = $form->preprice->getValues();
         $action->{Actions_Model::COLUMN_ADDED} = new DateTime();
         
         $action->{Actions_Model::COLUMN_ID_USER} = Auth::getUserId();
         $action->{Actions_Model::COLUMN_PUBLIC} = $form->public->getValues();
         if(isset($form->form)){
            $action->{Actions_Model::COLUMN_FORM} = $form->form->getValues();
            $action->{Actions_Model::COLUMN_FORM_SHOW_TO} = $form->formShowToDate->getValues();
         }
         
         $model->save($action);

         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
         $this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);

      $this->view()->form = $form;
      $this->view()->edit = false;
   }

   /**
    * controller pro úpravu akce
    */
   public function editController() {
      $this->checkWritebleRights();


      $model = new Actions_Model();
      $action = $model->where(Actions_Model::COLUMN_ID_CAT." = :idc AND ".Actions_Model::COLUMN_URLKEY." = :urlkey", 
         array( 'urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId() ) )
         ->record();
      
      if($action == false) return false;
      
      $form = $this->createForm(true);

      $form->name->setValues($action->{Actions_Model::COLUMN_NAME});
      $form->text->setValues($action->{Actions_Model::COLUMN_TEXT});
      $form->note->setValues($action->{Actions_Model::COLUMN_NOTE});
      $dateS = new DateTime($action->{Actions_Model::COLUMN_DATE_START});
      $form->date_start->setValues(strftime("%x",$dateS->format("U")));
      if($action->{Actions_Model::COLUMN_DATE_START} != $action->{Actions_Model::COLUMN_DATE_STOP}
              AND $action->{Actions_Model::COLUMN_DATE_STOP} != null) {
         $dateE = new DateTime($action->{Actions_Model::COLUMN_DATE_STOP});
         $form->date_stop->setValues(strftime("%x",$dateE->format("U")));
      }
      $form->urlkey->setValues($action->{Actions_Model::COLUMN_URLKEY});
      $form->public->setValues($action->{Actions_Model::COLUMN_PUBLIC});
      $form->time->setValues($action->{Actions_Model::COLUMN_TIME});
      $form->place->setValues($action->{Actions_Model::COLUMN_PLACE});
      $form->price->setValues($action->{Actions_Model::COLUMN_PRICE});
      $form->preprice->setValues($action->{Actions_Model::COLUMN_PREPRICE});
      $form->author->setValues($action->{Actions_Model::COLUMN_AUTHOR});
      $form->subname->setValues($action->{Actions_Model::COLUMN_SUBANME});
      if(isset ($form->titleImage)){
         $form->titleImage->setValues($action->{Actions_Model::COLUMN_IMAGE});
      }
      if($action->{Actions_Model::COLUMN_IMAGE} == null) {
         $form->image->setSubLabel($this->tr('Źádný obrázek'));
      } else {
         $form->image->setSubLabel($action->{Actions_Model::COLUMN_IMAGE});
      }
      
      if(isset ($form->form)){
         $form->form->setValues($action->{Actions_Model::COLUMN_FORM});
         if($action->{Actions_Model::COLUMN_FORM_SHOW_TO} != null){
            $form->formShowToDate->setValues(vve_date("%x", new DateTime($action->{Actions_Model::COLUMN_FORM_SHOW_TO})));
         }
      }

      // kontrola integrity data
      if($form->isSend()){
         if($form->save->getValues() == false){
            $this->link()->route('detail')->reload();
         }
         if($form->date_stop->isValid() && $form->date_start->isValid()
            && $form->date_start->getValues() != null && $form->date_stop->getValues() != null
            && ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))) {
               $form->date_stop->setError($this->tr('Konečné datum končí dříve než datum začátku'));
         }
      }
      if($form->isValid()) {
         // vybrání exist. obrázku
         if(isset ($form->titleImage)){
            $action->{Actions_Model::COLUMN_IMAGE} = $form->titleImage->getValues();
         }
         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null) {
            $imageObj = new File_Image($form->image);
            $crop = $this->category()->getParam('img_crop', VVE_ARTICLE_TITLE_IMG_C) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO;
            $imageObj->getData()->resize(
               $this->category()->getParam('img_width', VVE_ARTICLE_TITLE_IMG_W), 
               $this->category()->getParam('img_height', VVE_ARTICLE_TITLE_IMG_H), 
               $crop
               )->save();
            $imageObj->move(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR);
            $action->{Actions_Model::COLUMN_IMAGE} = $imageObj->getName();
         }
         
         // generování url klíče
         $urlkeys = $form->urlkey->getValues();
         $names = $form->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names, $action->{Actions_Model::COLUMN_ID});

         $oldDir = $action->{Actions_Model::COLUMN_URLKEY}[Locales::getDefaultLang()];
         $newDir = $urlkeys[Locales::getDefaultLang()];
         // přesun adresáře pokud existuje
         if($oldDir != $newDir AND is_dir($this->category()->getModule()->getDataDir().$oldDir)){
            rename($this->category()->getModule()->getDataDir().$oldDir, $this->category()->getModule()->getDataDir().$newDir);
         }

         $action->{Actions_Model::COLUMN_NAME} = $form->name->getValues();
         $action->{Actions_Model::COLUMN_SUBANME} = $form->subname->getValues();
         $action->{Actions_Model::COLUMN_AUTHOR} = $form->author->getValues();
         $action->{Actions_Model::COLUMN_TEXT} = $form->text->getValues();
         $action->{Actions_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues());
         $action->{Actions_Model::COLUMN_NOTE} = $form->note->getValues();
         $action->{Actions_Model::COLUMN_URLKEY} = $urlkeys;
         $action->{Actions_Model::COLUMN_DATE_START} = $form->date_start->getValues();
         $action->{Actions_Model::COLUMN_DATE_STOP} = $form->date_stop->getValues();
         $action->{Actions_Model::COLUMN_TIME} = $form->time->getValues();
         $action->{Actions_Model::COLUMN_PLACE} = $form->place->getValues();
         $action->{Actions_Model::COLUMN_PRICE} = $form->price->getValues();
         $action->{Actions_Model::COLUMN_PREPRICE} = $form->preprice->getValues();
         
         $action->{Actions_Model::COLUMN_ID_USER} = Auth::getUserId();
         $action->{Actions_Model::COLUMN_PUBLIC} = $form->public->getValues();
         if(isset($form->form)){
            $action->{Actions_Model::COLUMN_FORM} = $form->form->getValues();
            $action->{Actions_Model::COLUMN_FORM_SHOW_TO} = $form->formShowToDate->getValues();
         }
         
         $model->save($action);
         
         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
         $this->link()->route('detail', array('urlkey' => $action->{Actions_Model::COLUMN_URLKEY}))->reload();
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 1);

      $this->view()->form = $form;
      $this->view()->edit = true;
      $this->view()->action = $action;
   }

   /**
    * Vytvoří formulář pro úpravu akce
    * @return From
    */
   protected function createForm($delImg = false) {
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
      
      $eFile = new Form_Element_File('image', $this->tr('Obrázek'));
      $eFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'png')));
      $form->addElement($eFile, $fGrpOther);

      if(is_dir(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR)){
         $images = glob(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR.DIRECTORY_SEPARATOR . "*.{jpg,gif,png,JPG,GIF,PNG}", GLOB_BRACE);
         //print each file name
         if(!empty ($images)){
            $elemImgSel = new Form_Element_Select('titleImage', $this->tr('Uložené obrázky'));
            $elemImgSel->setOptions(array($this->tr('Žádný') => null));
            
            foreach($images as $image) {
               $elemImgSel->setOptions(array(basename($image) => basename($image)), true);
            }
            $form->addElement($elemImgSel, $fGrpOther);
         }
      }

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

      $eSub = new Form_Element_SaveCancel('save');
      $form->addElement($eSub);

      return $form;
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
         $textM = new Text_Model_Detail();
         $textM->saveText($form->text->getValues(), null, $this->category()->getId());

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
      $textM = new Text_Model_Detail();
      $text = $textM->getText($this->category()->getId());
      return $text;
   }

   public function featuredListController() {
      $model = new Actions_Model_List();

      $this->view()->actions = $model->getFeaturedActions($this->category()->getId());
      if($this->view()->action === false) return false;
   }

   public function currentActController() {
      $model = new Actions_Model_Detail();
      $this->view()->action = $model->getCurrentAction($this->category()->getId(), (int)$this->getRequestParam('from', 0));
//      if($this->view()->action === false) return false;
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

//      $form->addGroup('images', 'Nastavení obrázků');

//      $elemIW = new Form_Element_Text('img_width', 'Šířka titulního obrázku (px)');
//      $elemIW->addValidation(new Form_Validator_IsNumber());
//      $elemIW->setSubLabel('Výchozí: '.VVE_ARTICLE_TITLE_IMG_W.'px');
//      $form->addElement($elemIW, 'images');
//      if(isset($settings['img_width'])) {
//         $form->img_width->setValues($settings['img_width']);
//      }
//
//
//      $elemIH = new Form_Element_Text('img_height', 'Výška titulního obrázku (px)');
//      $elemIH->addValidation(new Form_Validator_IsNumber());
//      $elemIH->setSubLabel('Výchozí: '.VVE_ARTICLE_TITLE_IMG_H.'px');
//      $form->addElement($elemIH, 'images');
//      if(isset($settings['img_height'])) {
//         $form->img_height->setValues($settings['img_height']);
//      }
//
//      $elemCropI = new Form_Element_Checkbox('img_crop', 'Ořezávat titulní obrázek');
//      $elemCropI->setValues(true);
//      $form->addElement($elemCropI, 'images');
//      if(isset($settings['img_crop'])) {
//         $form->img_crop->setValues($settings['img_crop']);
//      }

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      } else {
         $form->type->setValues(self::DEFAULT_TIMEWINDOW_TYPE);
      }
      
      if($form->isValid()) {
         $settings['time'] = $form->time->getValues();
         $settings[self::PARAM_SHOW_EVENT_DIRECTLY] = $form->show_event_directly->getValues();
//         $settings['img_width'] = $form->img_width->getValues();
//         $settings['img_height'] = $form->img_height->getValues();
//         $settings['img_crop'] = $form->img_crop->getValues();
         // protože je vždy hodnota
         if($form->type->getValues() != self::DEFAULT_TIMEWINDOW_TYPE){
            $settings['type'] = $form->type->getValues();
         } else {
            $settings['type'] = self::DEFAULT_TIMEWINDOW_TYPE;
         }
      }
   }
}
?>