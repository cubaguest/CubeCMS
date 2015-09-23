<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class EmbeddedVideos_View extends View {

   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');

      if ($this->category()->getRights()->isWritable()) {
         if (!$this->toolbox instanceof Template_Toolbox2) {
            $this->toolbox = new Template_Toolbox2();
            $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         }

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_video', $this->tr('Přidat video'), $this->link()->route('addVideo'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat video"));
         $this->toolbox->addTool($toolAdd);

         $toolSort = new Template_Toolbox2_Tool_PostRedirect('sort_videos', $this->tr('Řadit videa'), $this->link()->route('sortVideos'));
         $toolSort->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr("Upravit pořadí videí"));
         $this->toolbox->addTool($toolSort);

         if ($this->videos) {
            // toolboxy pro bloky
            $toolbox = new Template_Toolbox2();
            $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_video', $this->tr('Upravit video'));
            $toolEdit
                ->setIcon(Template_Toolbox2::ICON_PEN)
                ->setTitle($this->tr("Upravit video"));
            $toolbox->addTool($toolEdit);

            $tooldel = new Template_Toolbox2_Tool_Form($this->formDeleteVideo);
            $tooldel->setIcon(Template_Toolbox2::ICON_DELETE)->setTitle($this->tr('Smazat video'))
                ->setConfirmMeassage($this->tr('Opravdu smazat video?'))
                ->setImportant(true);
            $toolbox->addTool($tooldel);

            foreach ($this->videos as $video) {
               $toolbox->edit_video->setAction($this->link()->route('editVideo', array('id' => $video->getPK())));
               $toolbox->video_delete_->getForm()->id->setValues($video->getPK());
               $video->toolbox = clone $toolbox;
            }
         }
      }
   }

   public function addVideoView()
   {
      $this->template()->addFile('tpl://edit.phtml');
      Template_Navigation::addItem($this->tr('Přidání videa'), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->tr('Přidání videa'));
      Template::setFullWidth(true);
   }

   public function editVideoView()
   {
      $this->template()->addFile('tpl://edit.phtml');
      Template_Navigation::addItem(sprintf($this->tr('Úprava videa %s'), $this->video->{EmbeddedVideos_Model::COLUMN_NAME}), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle(sprintf($this->tr('Úprava videa %s'), $this->video->{EmbeddedVideos_Model::COLUMN_NAME}));
      Template::setFullWidth(true);
   }

   public function sortVideosView()
   {
      $this->template()->addFile('tpl://sort.phtml');
      Template_Navigation::addItem($this->tr('Řazení videí'), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->tr('Řazení videí'));
      Template::setFullWidth(true);
   }

}
