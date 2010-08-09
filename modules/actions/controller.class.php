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
      $this->view()->imagesDir=$this->view()->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()];
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

         $ids = $model->saveAction($form->name->getValues(), $form->subname->getValues(),
                 $form->author->getValues(),
                 $form->text->getValues(), $form->note->getValues(), $form->urlkey->getValues(),
                 $form->date_start->getValues(), $form->date_stop->getValues(),
                 $form->time->getValues(),$form->place->getValues(),
                 $form->price->getValues(),$form->preprice->getValues(),
                 $file, $this->category()->getId(), Auth::getUserId(),
                 $form->public->getValues());

         $act = $model->getActionById($ids);

         // přesunutí obrázku akce do správného adresáře
         if($form->image->getValues() != null) {
            $fileObj = new Filesystem_File($file, AppCore::getAppCacheDir());
            $fileObj->move($this->category()->getModule()->getDataDir().$act[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
            $image = new Filesystem_File_Image($fileObj);
            $image->resampleImage(self::MAIN_IMAGE_WIDTH, self::MAIN_IMAGE_HEIGHT,true);
            $image->save();
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

      if($action->{Actions_Model_Detail::COLUMN_IMAGE} == null) {
         $form->image->setSubLabel($this->_('Źádný obrázek'));
      } else {
         $form->image->setSubLabel($action->{Actions_Model_Detail::COLUMN_IMAGE});
      }

      // kontrola integrity data
      if($form->isSend() AND $form->date_stop->getValues() != null
              AND ($form->date_start->getValues()->format("U") > $form->date_stop->getValues()->format("U"))) {
         $form->date_stop->setError($this->_('Končné datum končí dříve než datum zčátku'));
      }

      if($form->isValid()) {
         $file = $action->{Actions_Model_Detail::COLUMN_IMAGE};

         // smazání opbrázku
         if(($form->image->getValues() != null OR $form->del_image->getValues() == true)
                 AND $file != null) {
            $fileR = new Filesystem_File($action->{Actions_Model_Detail::COLUMN_IMAGE},
                    $this->category()->getModule()->getDataDir()
                            .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
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
                    .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
            $image = new Filesystem_File_Image($fileObj);
            $image->resampleImage(self::MAIN_IMAGE_WIDTH, self::MAIN_IMAGE_HEIGHT,true);
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
      $eName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eName);

      $esName = new Form_Element_Text('subname', $this->_('Pod název'));
      $esName->setLangs();
      $form->addElement($esName);

      $eAuthor = new Form_Element_Text('author', $this->_('Autor/ účinkující'));
      $form->addElement($eAuthor);

      $eText = new Form_Element_TextArea('text', $this->_('Text'));
      $eText->setLangs();
      $eText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eText);

      $eNote = new Form_Element_Text('note', $this->_('Poznámka'));
      $eNote->setLangs();
      $eNote->html()->setAttrib('size', 50);
      $form->addElement($eNote);

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

   public function editLabelController() {
      $this->checkControllRights();
      $form = new Form('modlabel');

      $elemText = new Form_Element_TextArea('text', $this->_('Popis'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemS);

      if($form->isValid()) {
         $textM = new Text_Model_Detail();
         $textM->saveText($form->text->getValues(), null, $this->category()->getId());

         $this->infoMsg()->addMessage($this->_('Úvodní text byl uložen'));
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

   // RSS
   public function exportController() {
      $this->checkReadableRights();
      $this->view()->type = $this->getRequest('type', 'rss');
      $model = new Actions_Model_List();
      $this->view()->actions = $model->getActionsByAdded($this->category()->getId(), VVE_FEED_NUM);
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

   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nasatvení časového okna');

      $elemTimeWindow = new Form_Element_Text('time', 'Délka časového okna');
      $elemTimeWindow->setSubLabel('Udává délku časového okna, pomocí kterého se vybírají zobrazené akce.<br /> Výchozí: '.self::DEFAULT_TIMEWINDOW.'');
      $elemTimeWindow->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemTimeWindow,'basic');

      if(isset($settings['time'])) {
         $form->time->setValues($settings['time']);
      }

      $elemTimeWindowType = new Form_Element_Select('type', 'Jednotka časového okna');
      $types = array('Den' => 'day', 'Měsíc' => 'month', 'Rok' => 'year');
      $elemTimeWindowType->setOptions($types);
      $elemTimeWindowType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TIMEWINDOW_TYPE, $types).'');
      $form->addElement($elemTimeWindowType,'basic');


      $form->addGroup('images', 'Nastavení obrázků');

      $elemIW = new Form_Element_Text('img_width', 'Šířka titulního obrázku (px)');
      $elemIW->addValidation(new Form_Validator_IsNumber());
      $elemIW->setSubLabel('Výchozí: '.self::MAIN_IMAGE_WIDTH.'px');
      $form->addElement($elemIW, 'images');
      if(isset($settings['img_width'])) {
         $form->img_width->setValues($settings['img_width']);
      }


      $elemIH = new Form_Element_Text('img_height', 'Výška titulního obrázku (px)');
      $elemIH->addValidation(new Form_Validator_IsNumber());
      $elemIH->setSubLabel('Výchozí: '.self::MAIN_IMAGE_HEIGHT.'px');
      $form->addElement($elemIH, 'images');
      if(isset($settings['img_height'])) {
         $form->img_height->setValues($settings['img_height']);
      }

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      } else {
         $form->type->setValues(self::DEFAULT_TIMEWINDOW_TYPE);
      }

      if($form->isValid()) {
         $settings['time'] = $form->time->getValues();
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