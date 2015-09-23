<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class EmbeddedVideos_Controller extends Controller {

   public function mainController()
   {
      $model = new EmbeddedVideos_Model();
      $this->checkDeleteVideo();

      $videos = $model
          ->where(EmbeddedVideos_Model::COLUMN_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
          ->records();

      $this->view()->videos = $videos;
   }

   public function addVideoController()
   {
      $this->checkWritebleRights();

      $form = $this->createVideoForm();

      if ($form->isValid()) {
         $this->processVideoForm($form);
         $this->infoMsg()->addMessage($this->tr('Video bylo uloženo'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
   }

   public function editVideoController($id)
   {
      $this->checkWritebleRights();

      $video = EmbeddedVideos_Model::getRecord($id);

      if (!$video) {
         throw new InvalidArgumentException($this->tr('Požadované video nebylo nalezeno'));
      }

      $form = $this->createVideoForm($video);

      if ($form->isValid()) {
         $this->processVideoForm($form, $video);
         $this->infoMsg()->addMessage($this->tr('Video bylo uloženo'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
      $this->view()->video = $video;
   }

   public function sortVideosController()
   {
      $this->checkWritebleRights();

      $model = new EmbeddedVideos_Model();
      $videos = $model->where(EmbeddedVideos_Model::COLUMN_ID_CATEGORY . " = :idc", array('idc' => $this->category()->getId()))
          ->records();
      $form = new Form('videos_order_');

      $eId = new Form_Element_Hidden('id');
      $eId->setDimensional();

      $form->addElement($eId);

      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $ids = $form->id->getValues();

//         $stmt = $model->query("UPDATE {THIS} SET `".EmbeddedVideos_Model::COLUMN_ORDER."` = :ord WHERE ".EmbeddedVideos_Model::COLUMN_ID." = :id");
         foreach ($ids as $index => $id) {
            EmbeddedVideos_Model::setRecordPosition($id, $index + 1);
         }

         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->redirect();
      }

      $this->view()->videos = $videos;
      $this->view()->form = $form;
   }

   protected function checkDeleteVideo()
   {
      if (!$this->rights()->isWritable()) {
         return;
      }

      $fDelete = new Form('video_delete_');

      $eId = new Form_Element_Hidden('id');
      $fDelete->addElement($eId);

      $eSubmit = new Form_Element_Submit('delete', $this->tr('Smazat video'));
      $fDelete->addElement($eSubmit);

      if ($fDelete->isValid()) {
         $model = new EmbeddedVideos_Model();
         $model->delete($fDelete->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Video bylo smazáno'));
         $this->link()->redirect();
      }

      $this->view()->formDeleteVideo = $fDelete;
   }

   protected function createVideoForm(Model_ORM_Record $record = null)
   {
      $form = new Form('video_');

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setLangs();
//      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemUrl = new Form_Element_Text('url', $this->tr('Url adresa'));
      $form->addElement($elemUrl);

      $elemCode = new Form_Element_TextArea('code', $this->tr('Kód videa'));
      $form->addElement($elemCode);

      $elemSend = new Form_Element_SaveCancel('send');
      $form->addElement($elemSend);

      if ($record) {
         $form->name->setValues($record->{EmbeddedVideos_Model::COLUMN_NAME});
         $form->url->setValues($record->{EmbeddedVideos_Model::COLUMN_URL});
         $form->code->setValues($record->{EmbeddedVideos_Model::COLUMN_CODE});
      }

      if ($form->isSend()) {
         if ($form->isSend() && $form->send->getValues() == false) {
            $this->link()->route()->redirect();
         }
         if ($form->url->getValues() == null && $form->code->getValues() == null) {
            $form->url->setError($this->tr('Musí být zadána adresa nebo kód videa'));
         }
      }

      return $form;
   }

   protected function processVideoForm(Form $form, Model_ORM_Record $record = null)
   {
      if ($record == null) {
         $record = EmbeddedVideos_Model::getNewRecord();
         $record->{EmbeddedVideos_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
      }

      $record->{EmbeddedVideos_Model::COLUMN_NAME} = $form->name->getValues();
      $record->{EmbeddedVideos_Model::COLUMN_URL} = $form->url->getValues();
      $record->{EmbeddedVideos_Model::COLUMN_CODE} = $form->code->getValues();

      $record->save();
   }

   public function settings(&$settings, Form &$form)
   {
      
   }

   /* Autorun metody */

   public static function AutoRunDaily()
   {
      
   }

   public static function AutoRunHourly()
   {
      
   }

   public static function AutoRunMonthly()
   {
      
   }

   public static function AutoRunYearly()
   {
      
   }

   public static function AutoRunWeekly()
   {
      
   }

}
