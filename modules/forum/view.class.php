<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class Forum_View extends View {

   public function mainView() {
      $this->template()->addTplFile('list-topics.phtml');

      if ($this->category()->getRights()->isControll()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_topics', $this->tr("Přidat téma"),
         $this->link()->route('addTopic'));
         $toolAdd->setIcon('comment_add.png')->setTitle($this->tr('Přidat nové téma'));
         $this->toolbox->addTool($toolAdd);
      }
      
   }
   
   public function showTopicView()
   {
      $this->template()->addFile('tpl://list-messages.phtml'); 
      
      if($this->category()->getRights()->isWritable()){
         // příspěvky
         $this->toolboxMessage = new Template_Toolbox2();
         $this->toolboxMessage->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);
         $this->toolboxMessage->setIcon(Template_Toolbox2::ICON_WRENCH);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_message', $this->tr("Upravit"),
         $this->link()->route('editMessage'));
         $toolEdit->setIcon('comment_edit.png')->setTitle($this->tr('Upravit příspěvek'));
         $this->toolboxMessage->addTool($toolEdit);
         
         if($this->topic->{Forum_Model_Topics::COLUMN_ID_USER} == Auth::getUserId()
            || $this->category()->getRights()->isControll()){
            // TOPIC
            $this->toolboxTopic = new Template_Toolbox2();
            $this->toolboxTopic->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);
            $this->toolboxTopic->setIcon(Template_Toolbox2::ICON_WRENCH);
         
            $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_topic', $this->tr("Upravit"),
            $this->link()->route('editTopic'));
            $toolEdit->setIcon('comment_edit.png')->setTitle($this->tr('Upravit téma'));
            $this->toolboxTopic->addTool($toolEdit);
         }
      }
      
      if($this->category()->getRights()->isControll()){
         
         $toolCensore = new Template_Toolbox2_Tool_Form($this->formMessageCensore);
         $toolCensore->setIcon('comment_key.png')->setTitle($this->tr('Cenzurovat příspěvek'));
         $this->toolboxMessage->addTool($toolCensore);
         
         $toolDelete = new Template_Toolbox2_Tool_Form($this->formMessageDelete);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat příspěvek včetně reakcí?'));
         $toolDelete->setIcon('comment_delete.png')->setTitle($this->tr('Smazat příspěvek'));
         $this->toolboxMessage->addTool($toolDelete);
         
         $toolDelete = new Template_Toolbox2_Tool_Form($this->formTopicDelete);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat téma i s příspěvky?'));
         $toolDelete->setIcon('comment_delete.png')->setTitle($this->tr('Smazat téma'));
         $this->toolboxTopic->addTool($toolDelete);
      }
      
      Template_Navigation::addItem($this->topic->{Forum_Model_Topics::COLUMN_NAME}, $this->link());
   }
   
   public function addTopicView()
   {
      $this->template()->addFile('tpl://edit-topic.phtml');
      $this->form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_SOURCES, false);
      $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_TPLS, false);
      $settings = new Component_TinyMCE_Settings_AdvSimple2();
      $settings->setSetting('editor_selector', 'mceEditor');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
      
      if($this->topic != null){
         Template_Navigation::addItem( $this->topic->{Forum_Model_Topics::COLUMN_NAME}, $this->link()->route('showTopic'));
         Template_Navigation::addItem( $this->tr('Úprava'), $this->link());
      } else {
         Template_Navigation::addItem($this->tr('Přidání témata'), $this->link());
      }
   }
   
   public function editTopicView()
   {
      $this->addTopicView();
   }

   public function addMessageView()
   {
      $this->template()->addFile('tpl://edit-message.phtml');
      $this->form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_SOURCES, false);
      $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_TPLS, false);
      $settings = new Component_TinyMCE_Settings_AdvSimple2();
      $settings->setSetting('height', 300);
      $settings->setSetting('editor_selector', 'mceEditor');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
      if($this->message != null){
         Template_Navigation::addItem($this->message->{Forum_Model_Topics::COLUMN_NAME}, $this->link()->route('showTopic'));
         Template_Navigation::addItem(sprintf( $this->tr('ǔprava "%s"'), $this->message->{Forum_Model_Messages::COLUMN_NAME}), $this->link());
      } else {
         Template_Navigation::addItem($this->topic->{Forum_Model_Topics::COLUMN_NAME}, $this->link()->route('showTopic'));
         Template_Navigation::addItem($this->tr('Přidání příspěvku'), $this->link());
      }
   }
   
   public function editMessageView()
   {
      $this->addMessageView();
   }
   
   public function rssTopicView()
   {
      $feed = new Component_Feed(true);
      $feed ->setConfig('type', $this->type);
      $feed ->setConfig('title', $this->topic->{Forum_Model_Topics::COLUMN_NAME}." - ".$this->category()->getName());
      $feed ->setConfig('desc', $this->topic->{Forum_Model_Topics::COLUMN_TEXT_CLEAR});
      $feed ->setConfig('link', $this->link()->route('showTopic'));
      
      foreach ($this->messages as $msg) {
         $desc = null;
         if($msg->{Forum_Model_Messages::COLUMN_NAME} != null){
            $desc .= "<h2>".$msg->{Forum_Model_Messages::COLUMN_NAME}."</h2>";
         }
         $desc .= $msg->{Forum_Model_Messages::COLUMN_TEXT};
         
         $desc .= $this->tr("Přidal").": <i>".$msg->{Forum_Model_Messages::COLUMN_CREATED_BY}."</i>";

         $feed->addItem($msg->{Forum_Model_Messages::COLUMN_NAME},$desc,
                 $this->link()->route('showTopic')->anchor('message-'.$msg->{Forum_Model_Messages::COLUMN_ID}),
                 new DateTime($msg->{Forum_Model_Messages::COLUMN_DATE_ADD}),
                 $msg->{Forum_Model_Messages::COLUMN_CREATED_BY}, $msg->{Forum_Model_Messages::COLUMN_EMAIL});
      }
      $feed->flush();
   }
   
   public function cancelMessageNotifyView()
   {
      $this->template()->addFile('tpl://cancel-notify.phtml');
   }

}
?>