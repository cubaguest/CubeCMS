<?php
class Actions_Controller extends Controller {
   const MAIN_IMAGE_WIDTH = 345;
   const MAIN_IMAGE_HEIGHT = 230;

   const DEFAULT_TIMEWINDOW = 1;
   const DEFAULT_TIMEWINDOW_TYPE = 'month';

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
            $linkNextLabel = sprintf($this->ngettext('+ %s year','+ %s years',(int)$time), $time);
            $linkBackLabel = sprintf($this->ngettext('- %s year','- %s years',(int)$time), $time);
            break;
         case 'month':
            $dateNext->modify("+".$time." month");
            $datePrev->modify("-".$time." month");
            $linkNextLabel = sprintf($this->ngettext('+ %s month','+ %s months',(int)$time), $time);
            $linkBackLabel = sprintf($this->ngettext('- %s month','- %s months',(int)$time), $time);
            break;
         case 'day':
         default:
            $dateNext->modify("+".$time." day");
            $datePrev->modify("-".$time." day");
            $linkNextLabel = sprintf($this->ngettext('+ %s day','+ %s days',(int)$time), $time);
            $linkBackLabel = sprintf($this->ngettext('- %s day','- %s days',(int)$time), $time);
            break;
      }

      $acM = new Actions_Model_List();
      $actions = $acM->getActions($this->category()->getId(), $currentDateO, $dateNext,
              !$this->getRights()->isWritable());

      $this->view()->actions = $actions;
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
   }

   protected function deleteAction($action) {
      $this->deleteActionData($action);
      $this->infoMsg()->addMessage(sprintf($this->tr('Akce "%s" byla smazána'), $action->{Actions_Model_Detail::COLUMN_NAME}));
      $this->link()->reload($this->view()->linkBack);
   }

   protected function deleteActionData($action) {
      // obrázek akce
      if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
         $fileObj = new Filesystem_File($action->{Actions_Model_Detail::COLUMN_IMAGE},
                 $this->category()->getModule()->getDataDir()
                         .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
         $fileObj->delete();
      }
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
      if($form->isSend() AND $form->date_stop->getValues() != null
              AND ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))) {
         $form->date_stop->setError($this->tr('Končné datum končí dříve než datum zčátku'));
      }

      if($form->isSend() AND $form->save->getValues() == false){
          $this->link()->route()->reload();
      }

      if($form->isValid()) {
         $model = new Actions_Model_Detail();

         $file = null;
         // vybrání exist. obrázku
         if(isset ($form->titleImage)){
            $file = $form->titleImage->getValues();
         }
         
         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null) {
            $f = $form->image->getValues();
            $image = new Filesystem_File_Image($form->image);
            $image->move(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR);
            $image->resampleImage($this->category()->getParam('img_width', VVE_ARTICLE_TITLE_IMG_W),
               $this->category()->getParam('img_height', VVE_ARTICLE_TITLE_IMG_H),
               $this->category()->getParam('img_crop', true));
            $image->save();
            $file = $image->getName();
            unset ($fileObj);
         }

         $ids = $model->saveAction($form->name->getValues(), $form->subname->getValues(),
                 $form->author->getValues(),
                 $form->text->getValues(), $form->note->getValues(), $form->urlkey->getValues(),
                 $form->date_start->getValues(), $form->date_stop->getValues(),
                 $form->time->getValues(),$form->place->getValues(),
                 $form->price->getValues(),$form->preprice->getValues(),
                 $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues());

         $act = $model->getActionById($ids);

         $this->infoMsg()->addMessage($this->tr('Akce byla uložena'));
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
      $form->note->setValues($action->{Actions_Model_Detail::COLUMN_NOTE});
      $dateS = new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_START});
      $form->date_start->setValues(strftime("%x",$dateS->format("U")));
      if($action->{Actions_Model_Detail::COLUMN_DATE_START} != $action->{Actions_Model_Detail::COLUMN_DATE_STOP}
              AND $action->{Actions_Model_Detail::COLUMN_DATE_STOP} != null) {
         $dateE = new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_STOP});
         $form->date_stop->setValues(strftime("%x",$dateE->format("U")));
      }
      $form->urlkey->setValues($action->{Actions_Model_Detail::COLUMN_URLKEY});
      $form->public->setValues($action->{Actions_Model_Detail::COLUMN_PUBLIC});
      $form->time->setValues($action->{Actions_Model_Detail::COLUMN_TIME});
      $form->place->setValues($action->{Actions_Model_Detail::COLUMN_PLACE});
      $form->price->setValues($action->{Actions_Model_Detail::COLUMN_PRICE});
      $form->preprice->setValues($action->{Actions_Model_Detail::COLUMN_PREPRICE});
      $form->author->setValues($action->{Actions_Model_Detail::COLUMN_AUTHOR});
      $form->subname->setValues($action->{Actions_Model_Detail::COLUMN_SUBANME});
      if(isset ($form->titleImage)){
         $form->titleImage->setValues($action->{Actions_Model_Detail::COLUMN_IMAGE});
      }

      if($action->{Actions_Model_Detail::COLUMN_IMAGE} == null) {
         $form->image->setSubLabel($this->tr('Źádný obrázek'));
      } else {
         $form->image->setSubLabel($action->{Actions_Model_Detail::COLUMN_IMAGE});
      }

      // kontrola integrity data
      if($form->isSend() AND $form->date_stop->getValues() != null
              AND ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))) {
         $form->date_stop->setError($this->tr('Končné datum končí dříve než datum zčátku'));
      }

      if($form->isSend() AND $form->save->getValues() == false){
          $this->link()->route('detail')->reload();
      }

      if($form->isValid()) {
         $file = $action->{Actions_Model_Detail::COLUMN_IMAGE};

         // vybrání exist. obrázku
         if(isset ($form->titleImage)){
            $file = $form->titleImage->getValues();
         }
         // pokud je nový obr nahrajeme jej
         if($form->image->getValues() != null) {
            $f = $form->image->getValues();
            $file = $f['name'];
            $fileObj = new Filesystem_File($form->image);
            $fileObj->move(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR);
            $image = new Filesystem_File_Image($fileObj);
            $image->resampleImage($this->category()->getParam('img_width', VVE_ARTICLE_TITLE_IMG_W),
               $this->category()->getParam('img_height', VVE_ARTICLE_TITLE_IMG_H),
               $this->category()->getParam('img_crop', true));
            $image->save();
            unset ($fileObj,$image);
         }

         // pokud není zadáno konečé datum
         $model->saveAction($form->name->getValues(), $form->subname->getValues(), $form->author->getValues(),
                 $form->text->getValues(), $form->note->getValues(), $form->urlkey->getValues(),
                 $form->date_start->getValues(), $form->date_stop->getValues(),
                 $form->time->getValues(),$form->place->getValues(),$form->price->getValues(),
                 $form->preprice->getValues(), $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues(),$action->{Actions_Model_Detail::COLUMN_ID});

         $actionNew = $model->getActionById($action->{Actions_Model_Detail::COLUMN_ID});

         // přejmenování složky
         if($action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 != $actionNew[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
            AND file_exists($this->category()->getModule()->getDataDir()
                            .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()])) {
            $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir()
                            .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
            $dir->rename($actionNew[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
         }

         $this->infoMsg()->addMessage($this->tr('Akce byla uložena'));
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

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->setLangs();
      $eName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eName);

      $esName = new Form_Element_Text('subname', $this->tr('Pod název'));
      $esName->setLangs();
      $form->addElement($esName);

      $eAuthor = new Form_Element_Text('author', $this->tr('Autor/ účinkující'));
      $form->addElement($eAuthor);

      $eText = new Form_Element_TextArea('text', $this->tr('Text'));
      $eText->setLangs();
      $eText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eText);

      $eNote = new Form_Element_Text('note', $this->tr('Poznámka'));
      $eNote->setLangs();
      $eNote->html()->setAttrib('size', 50);
      $form->addElement($eNote);

      $eDateS = new Form_Element_Text('date_start', $this->tr('Od'));
      $eDateS->addValidation(new Form_Validator_NotEmpty());
      $eDateS->addValidation(new Form_Validator_Date());
      $eDateS->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateS);

      $eDateT = new Form_Element_Text('date_stop', $this->tr('Do'));
      $eDateT->addValidation(new Form_Validator_Date());
      $eDateT->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateT);

      $eTime = new Form_Element_Text('time', $this->tr('Čas konání'));
      $eTime->addValidation(new Form_Validator_Time());
      $form->addElement($eTime);

      $ePlace = new Form_Element_Text('place', $this->tr('Místo konání'));
      $form->addElement($ePlace);

      $ePrice = new Form_Element_Text('price', $this->tr('Cena'));
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice);

      $ePrePrice = new Form_Element_Text('preprice', $this->tr('Cena v předprodeji'));
      $ePrePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrePrice);

      $eFile = new Form_Element_File('image', $this->tr('Obrázek'));
      $eFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'png')));
      $form->addElement($eFile);

      if(is_dir(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR)){
         $images = glob(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR.DIRECTORY_SEPARATOR . "*.{jpg,gif,png}", GLOB_BRACE);
         //print each file name
         if(!empty ($images)){
            $elemImgSel = new Form_Element_Select('titleImage', $this->tr('Uložené obrázky'));
            $elemImgSel->setOptions(array($this->tr('Žádný') => null));
            
            foreach($images as $image) {
               $elemImgSel->setOptions(array(basename($image) => basename($image)), true);
            }
            $form->addElement($elemImgSel);
         }
      }

      $eUrlKey = new Form_Element_Text('urlkey', $this->tr('Url klíč'));
      $eUrlKey->setLangs();
      $eUrlKey->setSubLabel($this->tr('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($eUrlKey);

      $ePub = new Form_Element_Checkbox('public', $this->tr('Veřejný'));
      $ePub->setSubLabel($this->tr('Veřejný - viditelný všem návštěvníkům'));
      $ePub->setValues(true);
      $form->addElement($ePub);

      $eSub = new Form_Element_SaveCancel('save');
      $form->addElement($eSub);

      return $form;
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
      $elemTimeWindow->setSubLabel('Udává délku časového okna, pomocí kterého se vybírají zobrazené akce.<br /> Výchozí: '.self::DEFAULT_TIMEWINDOW.'');
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


      $form->addGroup('images', 'Nastavení obrázků');

      $elemIW = new Form_Element_Text('img_width', 'Šířka titulního obrázku (px)');
      $elemIW->addValidation(new Form_Validator_IsNumber());
      $elemIW->setSubLabel('Výchozí: '.VVE_ARTICLE_TITLE_IMG_W.'px');
      $form->addElement($elemIW, 'images');
      if(isset($settings['img_width'])) {
         $form->img_width->setValues($settings['img_width']);
      }


      $elemIH = new Form_Element_Text('img_height', 'Výška titulního obrázku (px)');
      $elemIH->addValidation(new Form_Validator_IsNumber());
      $elemIH->setSubLabel('Výchozí: '.VVE_ARTICLE_TITLE_IMG_H.'px');
      $form->addElement($elemIH, 'images');
      if(isset($settings['img_height'])) {
         $form->img_height->setValues($settings['img_height']);
      }

      $elemCropI = new Form_Element_Checkbox('img_crop', 'Ořezávat titulní obrázek');
      $elemCropI->setValues(true);
      $form->addElement($elemCropI, 'images');
      if(isset($settings['img_crop'])) {
         $form->img_crop->setValues($settings['img_crop']);
      }

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      } else {
         $form->type->setValues(self::DEFAULT_TIMEWINDOW_TYPE);
      }

      if($form->isValid()) {
         $settings['time'] = $form->time->getValues();
         $settings['img_width'] = $form->img_width->getValues();
         $settings['img_height'] = $form->img_height->getValues();
         $settings['img_crop'] = $form->img_crop->getValues();
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