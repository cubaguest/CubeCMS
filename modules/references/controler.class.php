<?php
class ReferencesController extends Controller {
  /**
   * Speciální imageinární sloupce
   * @var string
   */
   const REFERENCE_EDIT_LINK = 'edit_link';

  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'reference_';
   const FORM_PREFIX_OTHER = 'other_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_NAME = 'name';
   const FORM_INPUT_LABEL = 'label';
   const FORM_INPUT_FILE = 'file';
   const FORM_INPUT_TEXT = 'text';

   /**
    * Adresíř s malými obrázky
    */
   const IMAGES_SMALL_DIR = 'small';

   /**
    * Parametry s velikostí malého obrázku
    */
   const PARAM_IMAGE_WIDTH = 'width';
   const PARAM_IMAGE_HEIGHT = 'height';
   const PARAM_SMALL_IMAGE_WIDTH = 'smallwidth';
   const PARAM_SMALL_IMAGE_HEIGHT = 'smallheight';

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      if($this->getrights()->iswritable()){
         $this->checkDeleteReference();
      }

      //		Vytvoření modelu
      $referModel = new ReferenceModel();

      $referencesArr = $referModel->getReferencesList();

      //		link pro přidání
      if($this->getrights()->iswritable()){
         $this->container()->addlink('LINK_TO_ADD_REFERENCE',$this->getLink()->action($this->getaction()->add()));
         $this->container()->addlink('LINK_TO_EDIT_OTHER_REFERENCE',$this->getLink()->action($this->getaction()->editOtherReference()));
         // přidání linků pro editaci
         foreach ($referencesArr as $refKey => $refer) {
            $referencesArr[$refKey][self::REFERENCE_EDIT_LINK] =
            $this->getLink()->article($refer[ReferenceModel::COLUMN_REFERENCE_NAME],
               $refer[ReferenceModel::COLUMN_REFERENCE_ID])
            ->action($this->getaction()->edit());
         }
      }

      $this->container()->addData('OTHER_REFERENCES', $referModel->getOtherReferences());


      $this->container()->addData('REFERENCES', $referencesArr);
      $this->container()->addData('IMAGES_DIR', $this->getModule()->getDir()->getDataDir());
      $this->container()->addData('IMAGES_SMALL_DIR', $this->getModule()->getDir()->getDataDir()
         .self::IMAGES_SMALL_DIR.URL_SEPARATOR);
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addController(){
      $this->checkWritebleRights();

      $referenceForm = new Form();
      $referenceForm->setPrefix(self::FORM_PREFIX);

      $referenceForm->crInputText(self::FORM_INPUT_NAME, true, true)
      ->crTextArea(self::FORM_INPUT_LABEL, true, true, Form::CODE_HTMLDECODE)
      ->crInputFile(self::FORM_INPUT_FILE, true)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($referenceForm->checkForm()){
         $image = new ImageFile($referenceForm->getValue(self::FORM_INPUT_FILE));

         if($image->isImage()){
            $refM = new ReferenceModel();

            try {
               //            Uložení obrázků
               // malý
               $image->saveImage($this->getModule()->getDir()->getDataDir()
                  .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR,
                  $this->getModule()->getParam(self::PARAM_SMALL_IMAGE_WIDTH),
                  $this->getModule()->getParam(self::PARAM_SMALL_IMAGE_HEIGHT));
               // velký
               $image->saveImage($this->getModule()->getDir()->getDataDir(),
                  $this->getModule()->getParam(self::PARAM_IMAGE_WIDTH),
                  $this->getModule()->getParam(self::PARAM_IMAGE_HEIGHT));
               $refM->saveNewReference($referenceForm->getValue(self::FORM_INPUT_NAME),
                  $referenceForm->getValue(self::FORM_INPUT_LABEL),
                  $image->getName());
               $this->infoMsg()->addMessage(_('Reference byla uložena'));
               $this->getLink()->action()->reload();
            } catch (Exception $e) {
               echo $e->getMessage();
               new CoreException(_('Referenci se nepodařilo uložit, chyba:').$e->getMessage(), 1);
            }


         }
      }

      $this->container()->addData('REFERENCE_DATA', $referenceForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $referenceForm->getErrorItems());
      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * controller pro úpravu novinky
   */
   public function editController() {
      $this->checkWritebleRights();

      $referenceForm = new Form();
      $referenceForm->setPrefix(self::FORM_PREFIX);

      $referenceForm->crInputText(self::FORM_INPUT_NAME, true, true)
      ->crTextArea(self::FORM_INPUT_LABEL, true, true, Form::CODE_HTMLDECODE)
      ->crInputFile(self::FORM_INPUT_FILE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //      Načtení hodnot prvků
      $referM = new ReferenceModel();
      $referM->loadReferenceDetailAllLangs($this->getArticle()->getArticle());
      //      Nastavení hodnot prvků
      $referenceForm->setValue(self::FORM_INPUT_NAME, $referM->getNamesLangs());
      $referenceForm->setValue(self::FORM_INPUT_LABEL, $referM->getLabelsLangs());

      $label = $referM->getNamesLangs();
      $this->container()->addData('REFERENCE_NAME', $label[Locale::getLang()]);

      //        Pokud byl odeslán formulář
      if($referenceForm->checkForm()){
            $imageName = null;
         // byl odeslán obrázek
         if($referenceForm->getValue(self::FORM_INPUT_FILE)){
            $image = new ImageFile($referenceForm->getValue(self::FORM_INPUT_FILE));
            // kontrola jestli je obrázek
            if($image->isImage()){
               // smažeme starý
               try {
                  $oldImage = new File($referM->getFile(), $this->getModule()->getDir()->getDataDir());
                  $oldImage->remove();
                  $oldImage = new File($referM->getFile(), $this->getModule()->getDir()->getDataDir()
                     .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR);
                  $oldImage->remove();
               } catch (Exception $e) {
                  new CoreException(_('Chyba při mazání obrázku.').$e->getMessage(),2);
               }

               // malý
               $image->saveImage($this->getModule()->getDir()->getDataDir()
                  .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR,
                  $this->getModule()->getParam(self::PARAM_SMALL_IMAGE_WIDTH),
                  $this->getModule()->getParam(self::PARAM_SMALL_IMAGE_HEIGHT));
               // velký
               $image->saveImage($this->getModule()->getDir()->getDataDir(),
                  $this->getModule()->getParam(self::PARAM_IMAGE_WIDTH),
                  $this->getModule()->getParam(self::PARAM_IMAGE_HEIGHT));
               $imageName = $image->getName();
            }
         }
         //            Uložení dat do db
         try {
            $referM->saveEditReference($referenceForm->getValue(self::FORM_INPUT_NAME),
               $referenceForm->getValue(self::FORM_INPUT_LABEL),
               $imageName, $this->getArticle()->getArticle());
         } catch (Exception $e) {
            new CoreException(_('Chyba při ukládání obrázku').$e->getMessage(),3);
         }

         $this->infoMsg()->addMessage(_('Reference byla uložena'));
         $this->getLink()->action()->article()->reload();
      }

      //    Data do šablony
      $this->container()->addData('REFERENCE_DATA', $referenceForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $referenceForm->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * metoda pro mazání novinky
   */
   private function checkDeleteReference() {
      $delForm = new Form(self::FORM_PREFIX);
      $delForm->crSubmit(self::FORM_BUTTON_DELETE)
      ->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric');

      if($delForm->checkForm()){
         $referM = new ReferenceModel();
         $referM->loadReferenceDetailAllLangs($delForm->getValue(self::FORM_INPUT_ID));
         // smazání obrázků
         try {
            $oldImage = new File($referM->getFile(), $this->getModule()->getDir()->getDataDir());
            $oldImage->remove();
            $oldImage = new File($referM->getFile(), $this->getModule()->getDir()->getDataDir()
               .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR);
            $oldImage->remove();

            // vymaz z db
            $referM->deleteReference($delForm->getValue(self::FORM_INPUT_ID));

            $this->infoMsg()->addMessage(_('Reference byla smazána'));
            $this->getLink()->reload();

         } catch (Exception $e) {
            new CoreException(_('Referenci se nepodařilo smazat').$e->getMessage(), 4);
         }

      }
   }

   public function editotherrefController() {
      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX.self::FORM_PREFIX_OTHER);

      $form->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
            ->crSubmit(self::FORM_BUTTON_SEND);

      $refM = new ReferenceModel();
      $form->setValue(self::FORM_INPUT_TEXT, $refM->getOtherRefAllLang());

 //        Pokud byl odeslán formulář
      if($form->checkForm()){
         if($refM->saveEditOtherReferences($form->getValue(self::FORM_INPUT_TEXT))){
            $this->infoMsg()->addMessage(_('Ostatní reference byly uloženy'));
            $this->getLink()->action()->reload();
         } else {
            new CoreException(_('Ostatní reference se nepodařilo uložit, chyba při ukládání.'), 1);
         }
      }
//    Data do šablony
      $this->container()->addData('REFERENCE_OTHER_DATA', $form->getValues());
      $this->container()->addData('ERROR_ITEMS', $form->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }

}

?>