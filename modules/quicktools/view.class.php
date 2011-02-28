<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class QuickTools_View extends View {
   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_ADD);

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_tool', $this->tr('Přidat nástroj'),
                 $this->link()->route('addTool'));
         $toolAdd->setIcon('page_edit.png')->setTitle($this->tr("Přidat nástroj"));
         $toolbox->addTool($toolAdd);

         $this->toolbox = $toolbox;

         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }
   }

   public function addToolView()
   {
      $this->template()->addFile('tpl://edit.phtml');
   }
   
   public function editToolView()
   {
      $this->edit = true;
      $this->addToolView();
   }

   public static function renderTools()
   {
      $tpl = new Template_ModuleStatic(new Url_Link_ModuleRequest(), 'quicktools');
      $tpl->addTplFile('toolbox.phtml');
      $tpl->tools = QuickTools_Controller::getAllTools();
      $tpl->renderTemplate();
   }
}

?>