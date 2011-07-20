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
      $this->template()->addFile('tpl://list-posts.phtml'); 
      
      if($this->category()->getRights()->isWritable()){
         // POSTY
         $this->toolboxPost = new Template_Toolbox2();
         $this->toolboxPost->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);
         $this->toolboxPost->setIcon(Template_Toolbox2::ICON_WRENCH);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_post', $this->tr("Upravit"),
         $this->link()->route('editPost'));
         $toolEdit->setIcon('comment_edit.png')->setTitle($this->tr('Upravit příspěvek'));
         $this->toolboxPost->addTool($toolEdit);
         
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
         
         $toolCensore = new Template_Toolbox2_Tool_Form($this->formPostCensore);
         $toolCensore->setIcon('comment_key.png')->setTitle($this->tr('Cenzurovat příspěvek'));
         $this->toolboxPost->addTool($toolCensore);
         
         $toolDelete = new Template_Toolbox2_Tool_Form($this->formPostDelete);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat příspěvek?'));
         $toolDelete->setIcon('comment_delete.png')->setTitle($this->tr('Smazat příspěvek'));
         $this->toolboxPost->addTool($toolDelete);
         
         $toolDelete = new Template_Toolbox2_Tool_Form($this->formTopicDelete);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat téma i s příspěvky?'));
         $toolDelete->setIcon('comment_delete.png')->setTitle($this->tr('Smazat téma'));
         $this->toolboxTopic->addTool($toolDelete);
      }
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
   }
   
   public function editTopicView()
   {
      $this->addTopicView();
   }

   public function addPostView()
   {
      $this->template()->addFile('tpl://edit-post.phtml');
      $this->form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_SOURCES, false);
      $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_TPLS, false);
      $settings = new Component_TinyMCE_Settings_AdvSimple2();
      $settings->setSetting('height', 300);
      $settings->setSetting('editor_selector', 'mceEditor');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
   
   public function editPostView()
   {
      $this->addPostView();
   }
   
   public function rssTopicController()
   {
      $feed = new Component_Feed(true);
   }
}
?>