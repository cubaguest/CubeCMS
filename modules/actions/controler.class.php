<?php
class ActionsController extends Controller {
  /**
   * Speciální imageinární sloupce
   * @var string
   */
   const ACTION_EDITABLE = 'editable';
   const ACTION_EDIT_LINK = 'editlink';
   const ACTION_SHOW_LINK = 'showlink';

  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'action_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_LABEL = 'label';
   const FORM_INPUT_TEXT = 'text';
   const FORM_INPUT_DATE_START = 'date_start';
   const FORM_INPUT_DATE_STOP = 'date_stop';
   const FORM_INPUT_IMAGE = 'image';
   const FORM_INPUT_DELETE_IMAGE = 'delete_image';

   /**
    * Parametr s počtem novinek na stránku
    * @var string
    */
   const PARAM_NUM_ACTIONS = 'scroll';

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      //		Vytvoření modelu
      $listActions = new ActionsListModel();
      //		Scrolovátka
      $scroll = new ScrollEplugin();
      $scroll->setCountRecordsOnPage($this->getModule()->getParam(self::PARAM_NUM_ACTIONS, 10));

      $scroll->setCountAllRecords($listActions->getCountActions());

      //		Vybrání novinek
         $actionArray = $listActions->getSelectedListActions($scroll->getStartRecord(), $scroll->getCountRecords());

      //		Přidání linku pro editaci a jestli se dá editovat
      if(!empty ($actionArray)){
         foreach ($actionArray as $key => $action) {
//               $actionArray[$key][self::ACTION_EDITABLE] = true;
//               $actionArray[$key][self::ACTION_EDIT_LINK] = $this->getLink()
//               ->article($action[ActionDetailModel::COLUMN_ACTION_LABEL],$action[ActionDetailModel::COLUMN_ACTION_ID])
//               ->action($this->getAction()->edit());
            //			Link pro zobrazení
            $actionArray[$key][self::ACTION_SHOW_LINK] = $this->getLink()
            ->article($action[ActionDetailModel::COLUMN_ACTION_LABEL],
               $action[ActionDetailModel::COLUMN_ACTION_ID]);
         }
      }

      //		Přenos do viewru
      $this->container()->addEplugin('scroll',$scroll);

      //		Link pro přidání
      if($this->getRights()->isWritable()){
         $this->container()->addLink('add_action',$this->getLink()->action($this->getAction()->addNAction()));
      }

      $this->container()->addData('ACTIONS_LIST', $actionArray);
      $this->container()->addData('IMAGES_DIR', $this->getModule()->getDir()->getDataDir(true));

//      $this->container()->addData('num_news', $numNewsArray);
   }

   public function showController(){
      $actionDetailM = new ActionDetailModel();
      $action = $actionDetailM->getActionDetailSelLang($this->getArticle()->getArticle());

      //      obsluha Mazání novinky
      if(($this->getRights()->isWritable() AND $actionDetailM->getIdUser()
            == $this->getRights()->getAuth()->getUserId()) OR
         $this->getRights()->isControll()){
         $form = new Form(self::FORM_PREFIX);

         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $actionDetail = new ActionDetailModel();

            if($actionDetail->deleteAction($form->getValue(self::FORM_INPUT_ID),
                  $this->getRights()->getAuth()->getUserId())){
               throw new UnexpectedValueException(_m('Akci se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage(_m('Akce byla smazána'));
            $this->getLink()->article()->action()->rmParam()->reload();
         }
      }

      $this->container()->addData('ACTION', $action);
      $this->container()->addData('ACTION_NAME', $action[ActionDetailModel::COLUMN_ACTION_LABEL]);

      if($this->getRights()->isControll() OR $action[ActionDetailModel::COLUMN_ACTION_ID]
         == $this->getRights()->getAuth()->getUserId()){
         $this->container()->addLink('EDIT_LINK', $this->getLink()->action($this->getAction()->edit()));
         $this->container()->addData('EDITABLE', true);
         $this->container()->addLink('ADD_ACTION',$this->getLink()->action($this->getAction()->addNAction())->article());
      }
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
      $this->container()->addData('IMAGES_DIR', $this->getModule()->getDir()->getDataDir(true));
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addNActionController(){
      $this->checkWritebleRights();

      $actionForm = new Form();
      $actionForm->setPrefix(self::FORM_PREFIX);

      $actionForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
//      ->crInputDate(self::FORM_INPUT_DATE_START)
//      ->crInputDate(self::FORM_INPUT_DATE_STOP)
      ->crInputFile(self::FORM_INPUT_IMAGE)
      ->crSubmit(self::FORM_BUTTON_SEND);
      //        Pokud byl odeslán formulář
      if($actionForm->checkForm()){
         $actionDetail = new ActionDetailModel();
         try {
            $imageName = true;
            if($actionForm->getValue(self::FORM_INPUT_IMAGE) != null){
               $image = new ImageFile($actionForm->getValue(self::FORM_INPUT_IMAGE));
               if($image->isImage()){
                  $image->saveImage($this->getModule()->getDir()->getDataDir());
                  $imageName = $image->getName();
               } else {
                  $imageName = false;
               }
            }

            if(!$imageName OR !$actionDetail->saveNewAction($actionForm->getValue(self::FORM_INPUT_LABEL),
                  $actionForm->getValue(self::FORM_INPUT_TEXT),
                  $actionForm->getValue(self::FORM_INPUT_DATE_START),
                  $actionForm->getValue(self::FORM_INPUT_DATE_STOP),
                  $imageName,
                  $this->getRights()->getAuth()->getUserId())){
               throw new UnexpectedValueException(_m('Akci se nepodařilo uložit, chyba při ukládání.'), 1);
            }
         } catch (Exception $e) {
            new CoreErrors($e);
         }

         $this->infoMsg()->addMessage(_m('Akce byla uložena'));
         $this->getLink()->article()->action()->rmParam()->reload();
      }

      $this->container()->addData('ACTION_DATA', $actionForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $actionForm->getErrorItems());
      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * controller pro úpravu novinky
   */
   public function editController() {
      $this->checkWritebleRights();

      $actionForm = new Form();
      $actionForm->setPrefix(self::FORM_PREFIX);

      $actionForm->crInputText(self::FORM_INPUT_LABEL, true, true)
            ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
            ->crInputFile(self::FORM_INPUT_IMAGE)
            ->crInputCheckbox(self::FORM_INPUT_DELETE_IMAGE)
            ->crSubmit(self::FORM_BUTTON_SEND);

      //      Načtení hodnot prvků
      $actionModel = new ActionDetailModel();
      $actionModel->getActionDetailAllLangs($this->getArticle());
      //      Nastavení hodnot prvků
      $actionForm->setValue(self::FORM_INPUT_LABEL, $actionModel->getLabelsLangs());
      $actionForm->setValue(self::FORM_INPUT_TEXT, $actionModel->getTextsLangs());

      $label = $actionModel->getLabelsLangs();
      $this->container()->addData('ACTION_NAME', $label[Locale::getLang()]);

      //        Pokud byl odeslán formulář
      if($actionForm->checkForm()){
         $imageName = true;
         try {
            $deleted = false;
            if($actionForm->getValue(self::FORM_INPUT_DELETE_IMAGE) == true AND
               $actionModel->getFile() != null){
               // smaže strý obrázek
               $file = new File($actionModel->getFile(), $this->getModule()->getDir()->getDataDir());
               $file->remove();
               $deleted = true;
            }

            if($actionForm->getValue(self::FORM_INPUT_IMAGE) != null){
               $image = new ImageFile($actionForm->getValue(self::FORM_INPUT_IMAGE));
               if($image->isImage()){
                  if(!$deleted AND $actionModel->getFile() != null){
                     // smaže strý obrázek
                     $file = new File($actionModel->getFile(), $this->getModule()->getDir()->getDataDir());
                     $file->remove();
                  }
                  $image->saveImage($this->getModule()->getDir()->getDataDir());
                  $imageName = $image->getName();
               } else {
                  $imageName = false;
               }
            }

            if(!$imageName OR !$actionModel->saveEditAction(
                  $actionForm->getValue(self::FORM_INPUT_LABEL),
                  $actionForm->getValue(self::FORM_INPUT_TEXT),
                  $imageName,
                  $this->getArticle())){
               throw new UnexpectedValueException(_m('Akci se nepodařilo uložit, chyba při ukládání.'), 2);
            }
            $this->infoMsg()->addMessage(_m('Akce byla uložena'));
            $this->getLink()->action()->reload();
         } catch (Exception $e) {
            new CoreErrors($e);
         }
      }

//    Data do šablony
      $this->container()->addData('ACTION_DATA', $actionForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $actionForm->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }
}
?>