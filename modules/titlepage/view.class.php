<?php
class TitlePage_View extends View {
   public function mainView() {
      $this->template()->addTplFile('main.phtml');
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('item', $this->_('Upravit položky'),
                 $this->link()->route('editList'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->_("upravit zobrazené položky"));
         $toolbox->addTool($toolAdd);
         $this->template()->toolbox = $toolbox;
      }
   }

   public function editListView() {
      $this->template()->addTplFile('editlist.phtml');

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('item', $this->_('Přidat položku'),
                 $this->link()->route('addSelectItem'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->_("Přidat novou položku"));
         $toolbox->addTool($toolAdd);
         $this->template()->toolbox = $toolbox;
      }
   }

   public function addSelectItemView() {
      $this->template()->addTplFile('addselect.phtml');
   }
   public function addItemView() {
      switch ($this->type) {
         case TitlePage_Controller::ITEM_TYPE_TEXT:
            $this->template()->addTplFile('edit-text.phtml');
            break;
         case TitlePage_Controller::ITEM_TYPE_MENU:
            $this->template()->addTplFile('edit-menu.phtml');
            break;
         case TitlePage_Controller::ITEM_TYPE_VIDEO:
            $this->template()->addTplFile('edit-video.phtml');
            break;
         case TitlePage_Controller::ITEM_TYPE_ARTICLE:
         case TitlePage_Controller::ITEM_TYPE_ARTICLEWGAL:
         case TitlePage_Controller::ITEM_TYPE_NEWS:
            $this->template()->addTplFile('edit-article.phtml');
            break;
         case TitlePage_Controller::ITEM_TYPE_ACTION:
         case TitlePage_Controller::ITEM_TYPE_ACTIONWGAL:
            $this->template()->addTplFile('edit-action.phtml');
            break;
         default:
            break;
      }
      $this->edit = false;
   }

   public function editItemView() {
      $this->addItemView();
      $this->edit = true;
   }
}

?>
