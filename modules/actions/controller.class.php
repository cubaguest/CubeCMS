<?php
class Actions_Controller extends Controller {
  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
   }

   public function showController(){
      //      obsluha Mazání akce
      if($this->rights()->isWritable()){
         $form = new Form(self::FORM_PREFIX);

         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $actionDetail = new Actions_Model_Detail($this->sys());
            $action = $actionDetail->getActionDetailAllLangs($this->sys()->article());
            // smaž obrázek
            if($action[Actions_Model_Detail::COLUMN_ACTION_IMAGE] != null){
               // smaže strý obrázek
               $file = new File($action[Actions_Model_Detail::COLUMN_ACTION_IMAGE], $this->module()->getDir()->getDataDir());
               $file->remove();
            }

            if($actionDetail->deleteAction($form->getValue(self::FORM_INPUT_ID),
                  $this->getRights()->getAuth()->getUserId())){
               throw new UnexpectedValueException($this->_('Akci se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage($this->_('Akce byla smazána'));
            $this->getLink()->article()->action()->rmParam()->reload();
         }
      }
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addNewActionController(){
      $this->checkWritebleRights();

      $actionForm = new Form();
      $actionForm->setPrefix(self::FORM_PREFIX);

      $actionForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crInputText(self::FORM_INPUT_TEXT_SHORT, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSelectDate(self::FORM_INPUT_DATE_START)
      ->crSelectDate(self::FORM_INPUT_DATE_STOP)
      ->crInputFile(self::FORM_INPUT_IMAGE)
      ->crSubmit(self::FORM_BUTTON_SEND);
      //        Pokud byl odeslán formulář
      if($actionForm->checkForm()){
         $actionDetail = new Actions_Model_Detail($this->sys());
         try {
            $imageName = true;
            if($actionForm->getValue(self::FORM_INPUT_IMAGE) != null){
               $image = new File_Image($actionForm->getValue(self::FORM_INPUT_IMAGE));
               if($image->isImage()){
                  $image->saveImage($this->getModule()->getDir()->getDataDir());
                  $imageName = $image->getName();
               } else {
                  $imageName = false;
               }
            }
            if(!$imageName OR !$actionDetail->saveNewAction($actionForm->getValue(self::FORM_INPUT_LABEL),
                  $actionForm->getValue(self::FORM_INPUT_TEXT_SHORT),
                  $actionForm->getValue(self::FORM_INPUT_TEXT),
                  $actionForm->getValue(self::FORM_INPUT_DATE_START),
                  $actionForm->getValue(self::FORM_INPUT_DATE_STOP),
                  $imageName,
                  $this->getRights()->getAuth()->getUserId())){
               throw new UnexpectedValueException($this->_('Akci se nepodařilo uložit, chyba při ukládání.'), 1);
            }
         } catch (Exception $e) {
            new CoreErrors($e);
         }

         $this->infoMsg()->addMessage($this->_('Akce byla uložena'));
         $this->getLink()->article()->action()->rmParam()->reload();
      }

      $this->view()->errorItems = $actionForm->getErrorItems();
   }

  /**
   * controller pro úpravu akce
   */
   public function editActionController() {
      $this->checkWritebleRights();

      $actionForm = new Form();
      $actionForm->setPrefix(self::FORM_PREFIX);

      $actionForm->crInputText(self::FORM_INPUT_LABEL, true, true)
         ->crInputText(self::FORM_INPUT_TEXT_SHORT, true, true)
         ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
         ->crInputFile(self::FORM_INPUT_IMAGE)
         ->crInputCheckbox(self::FORM_INPUT_DELETE_IMAGE)
         ->crSelectDate(self::FORM_INPUT_DATE_START)
         ->crSelectDate(self::FORM_INPUT_DATE_STOP)
         ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($actionForm->checkForm()){
         $imageName = false;
         $actionModel = new Actions_Model_Detail($this->sys());
         $action = $actionModel->getActionDetailAllLangs($this->sys()->article());
         try {
            if($action[Actions_Model_Detail::COLUMN_ACTION_IMAGE] != null AND
               ($actionForm->getValue(self::FORM_INPUT_DELETE_IMAGE) == true OR 
                $actionForm->getValue(self::FORM_INPUT_IMAGE) != null)){
               // smaže strý obrázek
               $file = new File($action[Actions_Model_Detail::COLUMN_ACTION_IMAGE], $this->module()->getDir()->getDataDir());
               $file->remove();
               $imageName = null;
            }

            if($actionForm->getValue(self::FORM_INPUT_IMAGE) != null){
               $image = new File_Image($actionForm->getValue(self::FORM_INPUT_IMAGE));
               if($image->isImage()){
                  $image->saveImage($this->module()->getDir()->getDataDir());
                  $imageName = $image->getName();
               }
            }
            if(!$actionModel->saveEditAction(
                  $actionForm->getValue(self::FORM_INPUT_LABEL),
                  $actionForm->getValue(self::FORM_INPUT_TEXT_SHORT),
                  $actionForm->getValue(self::FORM_INPUT_TEXT),
                  $actionForm->getValue(self::FORM_INPUT_DATE_START),
                  $actionForm->getValue(self::FORM_INPUT_DATE_STOP),
                  $imageName,
                  $this->sys()->article())){
                  var_dump($imageName);
               throw new UnexpectedValueException($this->_('Akci se nepodařilo uložit, chyba při ukládání.'), 2);
            }
            $this->infoMsg()->addMessage($this->_('Akce byla uložena'));
            $this->getLink()->action()->reload();
         } catch (Exception $e) {
            new CoreErrors($e);
         }
      }
      $this->view()->errorItems = $actionForm->getErrorItems();
   }
}
?>