<?php
class Actions_Controller extends Controller {

   protected $action = null;
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // uložení datumu do session pokud existuje - kvuli návratu
      // odkaz zpět
      $this->link()->backInit();

      $timeSpace = $this->category()->getModule()->getParam('time', "1_M");
      $arr = explode("_", $timeSpace);

      $startTime = mktime(0, 0, 0, $this->getRequest('month', date("n")),
              $this->getRequest('day', date("j")), $this->getRequest('year', date("Y")));

      $dateStart = new DateTime(date('Y-m-d',$startTime));
      $dateEnd = new DateTime(date('Y-m-d',$startTime));
      switch (strtolower($arr[1])) {
         case 'y':
            $dateStart->modify("+".$arr[0]." year");
            $dateEnd->modify("-".$arr[0]." year");
            $linkNextLabel = sprintf($this->ngettext('+ %s year','+ %s years',(int)$arr[0]), $arr[0]);
            $linkBackLabel = sprintf($this->ngettext('- %s year','- %s years',(int)$arr[0]), $arr[0]);
            break;
         case 'm':
            $dateStart->modify("+".$arr[0]." month");
            $dateEnd->modify("-".$arr[0]." month");
            $linkNextLabel = sprintf($this->ngettext('+ %s month','+ %s months',(int)$arr[0]), $arr[0]);
            $linkBackLabel = sprintf($this->ngettext('- %s month','- %s months',(int)$arr[0]), $arr[0]);
            break;
         case 'd':
         default:
            $dateStart->modify("+".$arr[0]." day");
            $dateEnd->modify("-".$arr[0]." day");
            $linkNextLabel = sprintf($this->ngettext('+ %s day','+ %s days',(int)$arr[0]), $arr[0]);
            $linkBackLabel = sprintf($this->ngettext('- %s day','- %s days',(int)$arr[0]), $arr[0]);
            break;
      }
      $timeNext = $dateStart->format("U");
      $timePrev = $dateEnd->format("U");

      $acM = new Actions_Model_List();
      $actions = $acM->getActions($this->category()->getId(), $startTime, $timeNext,
              !$this->getRights()->isWritable());

      $this->view()->actions = $actions;
      $this->view()->dateFrom = $startTime;
      $this->view()->dateTo = $timeNext;

      // link další
      $this->view()->linkNext = $this->link()->route('normaldate',
              array('day' => date('j', $timeNext) , 'month' => date('n', $timeNext),
              'year' => date('Y', $timeNext)));
      $this->view()->linkNextLabel = $linkNextLabel;
      // link předchozí
      $this->view()->linkBack = $this->link()->route('normaldate',
              array('day' => date('j', $timePrev) , 'month' => date('n', $timePrev),
              'year' => date('Y', $timePrev)));
      $this->view()->linkBackLabel = $linkBackLabel;

      // načtení textu
      $this->view()->text = $this->loadActionsText();
   }

   public function showController() {
      $this->checkReadableRights();

      $model = new Actions_Model_Detail();
      $this->view()->action = $model->getAction($this->getRequest('urlkey'), $this->category()->getId());
      if($this->view()->action == false) {
         AppCore::setErrorPage(true);
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

         $elemSubmit = new Form_Element_SubmitImage('delete');
         $delForm->addElement($elemSubmit);
         if($delForm->isValid()) {
            $this->deleteAction($this->view()->action);
         }
      }

      // komponenta pro vypsání odkazů na sdílení
      $shares = new Component_Share();
      $shares->setConfig('url', (string)$this->link());
      $shares->setConfig('title', $this->view()->action->{Actions_Model_Detail::COLUMN_NAME});
      $this->view()->shares=$shares;
      $this->view()->imagesDir=$this->view()->action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()];
   }

   protected function deleteAction($action) {
      $this->deleteActionData($action);
      $this->infoMsg()->addMessage(sprintf($this->_('Akce "%s" byla smazána'), $action->{Actions_Model_Detail::COLUMN_NAME}));
      $this->view()->linkBack->reload();
   }

   protected function deleteActionData($action) {
      // obrázek akce
      if($file->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
         $fileObj = new Filesystem_File($action->{Actions_Model_Detail::COLUMN_IMAGE},
                 $this->category()->getModule()->getDataDir()
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
         $fileObj->delete();
      }
      // smazání adresáře
      $dir = $this->category()->getModule()->getDataDir().$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()];
      if(file_exists($dir) AND is_dir($dir)){
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
      if($form->isSend() AND $form->date_stop->getValues() != null
         AND ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))){
            $form->date_stop->setError($this->_('Končné datum končí dříve než datum zčátku'));
      }
      if($form->isValid()) {
         $model = new Actions_Model_Detail();

         // pokud je nový obr nahrajeme jej
         $file = null;
         if($form->image->getValues() != null) {
            $f = $form->image->getValues();
            $file = $f['name'];
         }

         // pokud není zadáno konečé datum
         $date_stop = $form->date_stop->getValues();
         if($date_stop == null) {
            $date_stop = $form->date_start->getValues(); // +1 sekunda
            $date_stop->modify('+1 seconds');
         }

         $ids = $model->saveAction($form->name->getValues(), $form->subname->getValues(),
                 $form->author->getValues(),
                 $form->text->getValues(), $form->urlkey->getValues(),
                 $form->date_start->getValues(), $date_stop,
                 $form->time->getValues(),$form->place->getValues(),
                 $form->price->getValues(),$form->preprice->getValues(),
                 $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues());

         $act = $model->getActionById($ids);

         // přesunutí obrázku akce do správného adresáře
         if($form->image->getValues() != null) {
            $fileObj = new Filesystem_File($file, AppCore::getAppCacheDir());
            $fileObj->move($this->category()->getModule()->getDataDir().$act[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
            unset ($fileObj);
         }

         $this->infoMsg()->addMessage($this->_('Akce byla uložena'));
         $this->link()->route('detail', array('urlkey' => $act->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
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


      $model = new Actions_Model_Detail();
      $action = $model->getAction($this->getRequest('urlkey'), $this->category()->getId());
      if($action == false) return false;

      $form = $this->createForm(true);

      $form->name->setValues($action->{Actions_Model_Detail::COLUMN_NAME});
      $form->text->setValues($action->{Actions_Model_Detail::COLUMN_TEXT});
      $form->date_start->setValues(strftime("%x",$action->{Actions_Model_Detail::COLUMN_DATE_START}));
      $form->date_stop->setValues(strftime("%x",$action->{Actions_Model_Detail::COLUMN_DATE_STOP}));
      $form->urlkey->setValues($action->{Actions_Model_Detail::COLUMN_URLKEY});
      $form->public->setValues($action->{Actions_Model_Detail::COLUMN_PUBLIC});
      $form->time->setValues($action->{Actions_Model_Detail::COLUMN_TIME});
      $form->place->setValues($action->{Actions_Model_Detail::COLUMN_PLACE});
      $form->price->setValues($action->{Actions_Model_Detail::COLUMN_PRICE});
      $form->preprice->setValues($action->{Actions_Model_Detail::COLUMN_PREPRICE});
      $form->author->setValues($action->{Actions_Model_Detail::COLUMN_AUTHOR});
      $form->subname->setValues($action->{Actions_Model_Detail::COLUMN_SUBANME});

      if($action->{Actions_Model_Detail::COLUMN_IMAGE} == null) {
         $form->image->setSubLabel($this->_('Źádný obrázek'));
      } else {
         $form->image->setSubLabel($action->{Actions_Model_Detail::COLUMN_IMAGE});
      }

      // kontrola integrity data
      if($form->isSend() AND $form->date_stop->getValues() != null
         AND ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))){
            $form->date_stop->setError($this->_('Končné datum končí dříve než datum zčátku'));
      }

      if($form->isValid()) {
         $file = $action->{Actions_Model_Detail::COLUMN_IMAGE};

         // smazání opbrázku
         if(($form->image->getValues() != null OR $form->del_image->getValues() == true)
                 AND $file != null) {
            $fileR = new Filesystem_File($action->{Actions_Model_Detail::COLUMN_IMAGE},
                    $this->category()->getModule()->getDataDir()
                    .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
            $fileR->remove();
            unset ($fileR);
            $file = null;
         }
         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null) {
            $f = $form->image->getValues();
            $file = $f['name'];
            $fileObj = new Filesystem_File($file, AppCore::getAppCacheDir());
            $fileObj->move($this->category()->getModule()->getDataDir()
                    .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
            unset ($fileObj);
         }

         // pokud není zadáno konečé datum
         $date_stop = $form->date_stop->getValues();
         if($date_stop == null) {
            $date_stop = $form->date_start->getValues(); // +1 sekunda
            $date_stop->modify('+1 seconds');
         }

         $model->saveAction($form->name->getValues(), $form->subname->getValues(), $form->author->getValues(),
                 $form->text->getValues(), $form->urlkey->getValues(),
                 $form->date_start->getValues(), $date_stop,
                 $form->time->getValues(),$form->place->getValues(),$form->price->getValues(),
                 $form->preprice->getValues(), $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues(),$action->{Actions_Model_Detail::COLUMN_ID});

         $actionNew = $model->getActionById($action->{Actions_Model_Detail::COLUMN_ID});

         // přejmenování složky
         if($action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]
                 != $actionNew[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]) {
            $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir()
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
            $dir->rename($actionNew[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
         }

         $this->infoMsg()->addMessage($this->_('Akce byla uložena'));
         $this->link()->route('detail', array('urlkey' => $actionNew->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
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
   public function createForm($delImg = false) {
      $form = new Form('action_');

      $eName = new Form_Element_Text('name', $this->_('Název'));
      $eName->setLangs();
      $eName->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($eName);

      $esName = new Form_Element_Text('subname', $this->_('Pod název'));
      $esName->setLangs();
      $form->addElement($esName);

      $eAuthor = new Form_Element_Text('author', $this->_('Autor/účinkující'));
      $form->addElement($eAuthor);

      $eText = new Form_Element_TextArea('text', $this->_('Text'));
      $eText->setLangs();
      $eText->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($eText);

      $eDateS = new Form_Element_Text('date_start', $this->_('Od'));
      $eDateS->addValidation(new Form_Validator_NotEmpty());
      $eDateS->addValidation(new Form_Validator_Date());
      $eDateS->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateS);

      $eDateT = new Form_Element_Text('date_stop', $this->_('Do'));
      $eDateT->addValidation(new Form_Validator_Date());
      $eDateT->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateT);

      $eTime = new Form_Element_Text('time', $this->_('Čas konání'));
      $eTime->addValidation(new Form_Validator_Time());
      $form->addElement($eTime);

      $ePlace = new Form_Element_Text('place', $this->_('Místo konání'));
      $form->addElement($ePlace);

      $ePrice = new Form_Element_Text('price', $this->_('Cena'));
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice);

      $ePrePrice = new Form_Element_Text('preprice', $this->_('Cena v předprodeji'));
      $ePrePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrePrice);

      $eFile = new Form_Element_File('image', $this->_('Obrázek'));
      $eFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'png')));
      $eFile->setUploadDir(AppCore::getAppCacheDir());
      $form->addElement($eFile);

      if($delImg === true) {
         $eDelImg = new Form_Element_Checkbox('del_image', null);
         $eDelImg->setSubLabel($this->_('Smazat nahraný obrázek'));
         $form->addElement($eDelImg);
      }

      $eUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $eUrlKey->setLangs();
      $eUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($eUrlKey);

      $ePub = new Form_Element_Checkbox('public', $this->_('Veřejný'));
      $ePub->setSubLabel($this->_('Veřejný - viditelný všem návštěvníkům'));
      $ePub->setValues(true);
      $form->addElement($ePub);

      $eSub = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($eSub);

      return $form;
   }

   public function editLabelController(){
      $this->checkControllRights();
      $form = new Form('modlabel');

      $elemText = new Form_Element_TextArea('text', $this->_('Popis'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemS);

      if($form->isValid()){
         $textM = new Text_Model_Detail();
         $textM->saveText($form->text->getValues(), null, null, $this->category()->getId());

         $this->infoMsg()->addMessage($this->_('Úvodní text byl uložen'));
         $this->link()->route()->reload();
      }

      // načtení textu
      $text = $this->loadActionsText();
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $this->view()->form = $form;
   }

   private function loadActionsText(){
      $textM = new Text_Model_Detail();
      $text = $textM->getText($this->category()->getId());
      return $text;
   }

   // RSS
   public function exportController() {
      $this->checkReadableRights();
      $this->view()->type = $this->getRequest('type', 'rss');
   }

   public function featuredListController() {
      $model = new Actions_Model_List();

      $this->view()->actions = $model->getFeaturedActions($this->category()->getId());
      if($this->view()->action === false) return false;
   }

   public function currentActController() {
      $model = new Actions_Model_Detail();
      $this->view()->action = $model->getCurrentAction($this->category()->getId());
//      if($this->view()->action === false) return false;
   }
}
?>