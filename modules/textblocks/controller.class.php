<?php

class TextBlocks_Controller extends Controller {
   const DEFAULT_RECORDS_ON_PAGE = 10;

   const DATA_DIR = 'text-blocks';

   protected function init()
   {
      $this->module()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení položek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new TextBlocks_Model();

      if($this->category()->getRights()->isWritable()){
         $formDel = new Form('block_del_');

         $elemId = new Form_Element_Hidden('id');
         $formDel->addElement($elemId);

         $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
         $formDel->addElement($elemSubmit);

         if($formDel->isValid()){
            $model->delete($formDel->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Blok byl smazán'));
            $this->link()->rmParam()->reload();
         }
         $this->view()->formDelete = $formDel;
      }

      $this->view()->blocks = $model
          ->where(TextBlocks_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
          ->records();
   }

   /**
    * Kontroler pro přidání položky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if ($addForm->isValid()) {
         $model = new TextBlocks_Model();
         $record = $model->newRecord();
         $this->processEditForm($addForm, $record);
         $this->infoMsg()->addMessage($this->tr('Blok byl uložen na konec'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $addForm;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      // načtení dat
      $model = new TextBlocks_Model();
      $record = $model->record($this->getRequest('id'));
      if($record == false) {
         throw new UnexpectedPageException();
      }

      $editForm = $this->createForm($record);

      if ($editForm->isValid()) {
         $this->processEditForm($editForm, $record);
         $this->infoMsg()->addMessage($this->tr('Blok byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->person = $record;
   }

   protected function processEditForm(Form $form, Model_ORM_Record $block = null)
   {
      $block->{TextBlocks_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
      $block->{TextBlocks_Model::COLUMN_NAME} = $form->name->getValues();
      $block->{TextBlocks_Model::COLUMN_TEXT} = $form->text->getValues();
      
      if ($form->image->getValues() != null) {
         $file = $form->image->createFileObject();
         $block->{TextBlocks_Model::COLUMN_IMAGE} = $file->getName();
         unset ($file);
      }
      if ($form->file->getValues() != null) {
         $file = $form->file->createFileObject();
         $block->{TextBlocks_Model::COLUMN_FILE} = $file->getName();
         unset ($file);
      }
      // pokud byla zadáno pořadí, zařadíme na pořadí. Jinak dáme na konec
      $block->save();
      
      return $block;
   }


   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm(Model_ORM_Record $block = null) {
      $form = new Form('block_');

      $iName = new Form_Element_Text('name', $this->tr('Jméno'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName);

      $iText = new Form_Element_TextArea('text', $this->tr('Text'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText);

      $iImage = new Form_Element_ImageSelector('image', $this->tr('Obrázek'));
      $iImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $iImage->setUploadDir($this->module()->getDataDir());
      $iImage->setOverWrite(false);
      $form->addElement($iImage);
      
      $iFile = new Form_Element_File('file', $this->tr('Připojený soubor'));
      $iFile->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::ALL));
      $iFile->setUploadDir($this->module()->getDataDir());
      $iFile->setOverWrite(false);
      $form->addElement($iFile);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      if($block){
         // element pro odstranění obrázku
         $form->name->setValues($block->{TextBlocks_Model::COLUMN_NAME});
         $form->image->setValues($block->{TextBlocks_Model::COLUMN_IMAGE});
         $form->text->setValues($block->{TextBlocks_Model::COLUMN_TEXT});
      }
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      return $form;
   }

   public function editOrderController()
   {
      $this->checkWritebleRights();
      
      $model = new TextBlocks_Model();
      $blocks = $model
          ->where(TextBlocks_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
          ->records();

      $form = new Form('blocks_order_');
      
      $eId = new Form_Element_Hidden('id');
      $eId->setDimensional();
      
      $form->addElement($eId);
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $ids = $form->id->getValues();
         
         $stmt = $model->query("UPDATE {THIS} SET `".TextBlocks_Model::COLUMN_ORDER."` = :ord WHERE ".TextBlocks_Model::COLUMN_ID." = :id");
         foreach ($ids as $index => $id) {
            $stmt->bindValue('id', $id);
            $stmt->bindValue('ord', $index+1);
            $stmt->execute();
         }
         
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->reload();
      }
      
      $this->view()->blocks = $blocks;
      $this->view()->form = $form;
   }


   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new People_Model();
      $model->where(People_Model::COLUMN_ID_CATEGORY. " = :idc", array('idc' => $category->getId()))->delete();
   }

   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings, Form &$form) {
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
      }
   }

}