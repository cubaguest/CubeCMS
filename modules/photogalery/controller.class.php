<?php
/**
 * Kontroler pro obsluhu fotogalerie
 *
 * Jedná se o jednoúrovňovou fotogalerii s textem
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author 		$Author: $ $Date:$
 *              $LastChangedBy: $ $LastChangedDate: $
 */

class Photogalery_Controller extends Controller {
   const DIR_SMALL = 'small';
   const DIR_MEDIUM = 'medium';
   const DIR_ORIGINAL = 'original';

   const SMALL_WIDTH = 75;
   const SMALL_HEIGHT = 75;

   const MEDIUM_WIDTH = 600;
   const MEDIUM_HEIGHT = 400;

    /**
     * Kontroler pro zobrazení fotogalerii
     */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->view()->template()->addTplFile("list.phtml");
      $this->view()->template()->addCssFile("style.css");
   }

   public function edittextController() {
      $this->checkWritebleRights();

      $form = new Form("text_");

//      $label = new Form_Element_Text('label', $this->_('Nadpis'));
//      $label->setSubLabel($this->_('Doplní se k nadpisu kategorie a stránky'));
//      $label->setLangs();
//      $form->addElement($label);

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
//      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($textarea);

//      $textareaPanel = new Form_Element_TextArea('paneltext', $this->_("Text panelu"));
//      $textareaPanel->setSubLabel($this->_('Je zobrazen v panelu, pokud je panel zapnut'));
//      $textareaPanel->setLangs();
//      $form->addElement($textareaPanel);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId());
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
//         $form->label->setValues($text->{Text_Model_Detail::COLUMN_LABEL});
//         $form->paneltext->setValues($text->{Text_Model_Detail::COLUMN_TEXT_PANEL});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         try {
            $model->saveText($form->text->getValues(), null,
               null, $this->category()->getId());
            $this->infoMsg()->addMessage($this->_('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }

      // view
      $this->view()->template()->form = $form;
      
      $this->view()->template()->addTplFile("edittext.phtml");
      $this->view()->template()->addCssFile("style.css");
   }

   public function editimagesController(){
      $this->checkWritebleRights();

      $imagesM = new PhotoGalery_Model_Images();

      $addForm = new Form('addimage_');

      $addFile = new Form_Element_File('image', $this->_('Obrázek'));
      $addFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $addFile->setUploadDir($this->category()->getModule()->getDataDir());
      $addForm->addElement($addFile);

      $addSubmit = new Form_Element_Submit('send',$this->_('Odeslat'));
      $addForm->addElement($addSubmit);

      if($addForm->isValid()){
         $file = $addFile->getValues();
         $image = new Filesystem_File_Image($file['name'], $this->category()->getModule()->getDataDir());
         $image->saveAs($this->category()->getModule()->getDataDir().self::DIR_SMALL,
            self::SMALL_WIDTH, self::SMALL_HEIGHT, true);
         $image->saveAs($this->category()->getModule()->getDataDir().self::DIR_MEDIUM,
            self::MEDIUM_WIDTH, self::MEDIUM_HEIGHT);

         // uloženhí do db
         $imagesM->saveImage($this->category()->getId(), $image->getName(), $image->getName());

         $this->infoMsg()->addMessage($this->_('Obrázek byl uložen'));
         $this->link()->reload();
      }

      $editForm = new Form('editimage_');

      $imgName = new Form_Element_Text('name', $this->_('Název'));
      $imgName->setLangs();
      $editForm->addElement($imgName);

      $imgOrd = new Form_Element_Text('ord', $this->_('Pořadí'));
      $editForm->addElement($imgOrd);

      $imgDesc = new Form_Element_TextArea('desc', $this->_('Popis'));
      $imgDesc->setLangs();
      $editForm->addElement($imgDesc);

      $imgDel = new Form_Element_Checkbox('delete', $this->_('Smazat'));
      $editForm->addElement($imgDel);
      $imgId = new Form_Element_Hidden('id');
      $editForm->addElement($imgId);
      $imgFile = new Form_Element_Hidden('file');
      $editForm->addElement($imgFile);

      $submit = new Form_Element_Submit('save', $this->_('Uložit'));
      $editForm->addElement($submit);

      if($editForm->isValid()){
         $files = $editForm->file->getValues();
         $names = $editForm->name->getValues();
         $descs = $editForm->desc->getValues();
         $ordss = $editForm->ord->getValues();
         foreach ($editForm->id->getValues() as $id){
            if($editForm->delete->getValues($id) === true){
               // mažese
//               print ('smaz id'.$id."<br />");
               // smazání souborů
               $file = new Filesystem_File($files[$id], $this->category()->getModule()->getDataDir()
                  .self::DIR_SMALL.DIRECTORY_SEPARATOR);
               $file->remove();
               $file = new Filesystem_File($files[$id], $this->category()->getModule()->getDataDir()
                  .self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
               $file->remove();
               $file = new Filesystem_File($files[$id], $this->category()->getModule()->getDataDir());
               $file->remove();
               $imagesM->deleteImage($id);
            } else {
               // ukládají změny
//               print ('ulož id'.$id."<br />");
               $imagesM->saveImage($this->category()->getId(), null, $names[$id], $descs[$id],$ord[$id],$id);
            }
         }
      }


      $this->view()->template()->images = $imagesM->getImages($this->category()->getId());
      $this->view()->template()->addForm = $addForm;
      $this->view()->template()->editForm = $editForm;
      $this->view()->template()->addTplFile("editimages.phtml");
      $this->view()->template()->addCssFile("style.css");
   }
}
?>