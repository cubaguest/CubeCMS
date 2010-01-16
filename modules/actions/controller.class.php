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
      if($this->getRequest('day') != null){
         $_SESSION['actionBack'] = array('day' => $this->getRequest('day'),
                                       'month' => $this->getRequest('month'),
                                       'year' => $this->getRequest('year'));
      } else {
         unset ($_SESSION['actionBack']);
      }

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
            break;
         case 'm':
            $dateStart->modify("+".$arr[0]." month");
            $dateEnd->modify("-".$arr[0]." month");
            break;
         case 'd':
         default:
            $dateStart->modify("+".$arr[0]." day");
            $dateEnd->modify("-".$arr[0]." day");
            break;
      }
      $timeNext = $dateStart->format("U");
      $timePrev = $dateEnd->format("U");

      $acM = new Actions_Model_List();
      $actions = $acM->getActions($this->category()->getId(), $startTime, $timeNext,
              !$this->getRights()->isWritable());

      $this->view()->template()->actions = $actions;
      $this->view()->template()->dateFrom = $startTime;
      $this->view()->template()->dateTo = $timeNext;

      // link další
      $this->view()->template()->linkNext = $this->link()->route('normaldate',
              array('day' => date('j', $timeNext) , 'month' => date('n', $timeNext),
                 'year' => date('Y', $timeNext)));
      // link předchozí
      $this->view()->template()->linkBack = $this->link()->route('normaldate',
              array('day' => date('j', $timePrev) , 'month' => date('n', $timePrev),
                 'year' => date('Y', $timePrev)));
   }

   public function showController(){
      $this->checkReadableRights();

      $model = new Actions_Model_Detail();
      $this->view()->action = $model->getAction($this->getRequest('urlkey'));
      if($this->view()->action == false){
         AppCore::setErrorPage(true);
         return false;
      }

      // odkaz zpět
      if(isset ($_SESSION['actionBack'])){
            $this->view()->linkBack = $this->link()->route('normaldate', 
                    array('day' => $_SESSION['actionBack']['day'],
               'month' => $_SESSION['actionBack']['month'],
               'year' => $_SESSION['actionBack']['year']));
      } else {
            $this->view()->linkBack = $this->link()->route();
      }

      // komponenta pro vypsání odkazů na sdílení
      $shares = new Component_Share();
      $shares->setConfig('url', (string)$this->link());
      $shares->setConfig('title', $this->view()->action->{Actions_Model_Detail::COLUMN_NAME});
      $this->view()->shares=$shares;
   }

   public function showPdfController() {
      $this->checkReadableRights();
      $this->view()->urlkey = $this->getRequest('urlkey');
   }

   public function archiveController(){
      $this->checkReadableRights();
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addController(){
      $this->checkWritebleRights();

      $form = $this->createForm();

      if($form->isValid()){
         $model = new Actions_Model_Detail();
         
         // pokud je nový obr nahrajeme jej
         $file = null;
         if($form->image->getValues() != null){
            $f = $form->image->getValues();
            $file = $f['name'];
         }

         // pokud není zadáno konečé datum
         $date_stop = $form->date_stop->getValues();
         if($date_stop == null){
            $date_stop = $form->date_start->getValues(); // +1 sekunda
            $date_stop->modify('+1 seconds');
         }

         // URL klíč
         $urlkeys = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkeys as $lang => $variable) {
            if($variable == null AND $names[$lang] == null) {
               $urlkeys[$lang] = null;
            } else if($variable == null) {
               $urlkeys[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkeys[$lang] = vve_cr_url_key($variable);
            }
         }

         $ids = $model->saveAction($names, $form->text->getValues(), $urlkeys,
                 $form->date_start->getValues(), $date_stop,
                 $form->time->getValues(),$form->place->getValues(),$form->price->getValues(),
                 $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues());

         $act = $model->getActionById($ids);

         $this->infoMsg()->addMessage($this->_('Akce byla uložena'));
         $this->link()->route('detail', array('urlkey' => $act->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
      }


      $this->view()->template()->form = $form;
      $this->view()->template()->edit = false;
   }

  /**
   * controller pro úpravu akce
   */
   public function editController() {
      $this->checkWritebleRights();


      $model = new Actions_Model_Detail();
      $action = $model->getAction($this->getRequest('urlkey'));
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

      if($action->{Actions_Model_Detail::COLUMN_IMAGE} == null){
         $form->image->setSubLabel($this->_('Źádný obrázek'));
      } else {
         $form->image->setSubLabel($action->{Actions_Model_Detail::COLUMN_IMAGE});
      }

      if($form->isValid()){
         $file = $action->{Actions_Model_Detail::COLUMN_IMAGE};

         // smazání opbrázku
         if($form->image->getValues() != null OR $form->del_image->getValues() == true){
            $fileR = new Filesystem_File($action->{Actions_Model_Detail::COLUMN_IMAGE}, $this->category()->getModule()->getDataDir());
            $fileR->remove();
            unset ($fileR);
            $file = null;
         }
         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null){
            $f = $form->image->getValues();
            $file = $f['name'];
         }

         // pokud není zadáno konečé datum
         $date_stop = $form->date_stop->getValues();
         if($date_stop == null){
            $date_stop = $form->date_start->getValues(); // +1 sekunda
            $date_stop->modify('+1 seconds');
         }

         // URL klíč
         $urlkeys = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkeys as $lang => $variable) {
            if($variable == null AND $names[$lang] == null) {
               $urlkeys[$lang] = null;
            } else if($variable == null) {
               $urlkeys[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkeys[$lang] = vve_cr_url_key($variable);
            }
         }

         $model->saveAction($names, $form->text->getValues(), $urlkeys,
                 $form->date_start->getValues(), $date_stop,
                 $form->time->getValues(),$form->place->getValues(),$form->price->getValues(),
                 $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues(),$action->{Actions_Model_Detail::COLUMN_ID});

         $act = $model->getActionById($action->{Actions_Model_Detail::COLUMN_ID});

         $this->infoMsg()->addMessage($this->_('Akce byla uložena'));
         $this->link()->route('detail', array('urlkey' => $act->{Actions_Model_Detail::COLUMN_URLKEY}))->reload();
      }


      $this->view()->template()->form = $form;
      $this->view()->template()->edit = true;
      $this->view()->template()->action = $action;

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

      $eFile = new Form_Element_File('image', $this->_('Obrázek'));
      $eFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'png')));
      $eFile->setUploadDir($this->category()->getModule()->getDataDir());
      $form->addElement($eFile);

      if($delImg === true){
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

   // RSS
   public function exportController(){
      $this->checkReadableRights();
      $this->view()->type = $this->getRequest('type', 'rss');
   }
}
?>