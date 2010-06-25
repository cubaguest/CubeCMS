<?php
class ArticlesList_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('text_edit', $this->_('Upravit text'),
                 $this->link()->route('editText'));
         $toolAdd->setIcon('page_edit.png')->setTitle($this->_("Upravit úvodní text"));
         $toolbox->addTool($toolAdd);
         $this->template()->toolbox = $toolbox;
      }
   }
   public function editTextView() {
      $this->template()->addTplFile('edittext.phtml');
   }
}

?>
