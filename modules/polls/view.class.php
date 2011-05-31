<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Polls_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://polls.phtml');

      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_ADD);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_poll', $this->tr("Přidat anketu"),
         $this->link()->route('add'));
         $toolAdd->setIcon('poll_add.png')->setTitle($this->tr('Přidat novou anketu'));
         $this->toolbox->addTool($toolAdd);
         
         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
         
         // toolbox pro editaci anket
         $this->toolboxPoll = new Template_Toolbox2();
         $this->toolboxPoll->setIcon(Template_Toolbox2::ICON_WRENCH);
         
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('poll_edit', $this->tr("Upravit anketu"),
         $this->link()->route('edit', array('id' => 0)));
         $toolAdd->setIcon('poll_edit.png')->setTitle($this->tr('Upravit anketu'));
         $this->toolboxPoll->addTool($toolAdd);
         
         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('poll_delete.png')->setTitle($this->tr('Smazat anketu'))
            ->setConfirmMeassage($this->tr('Opravdu smazat anketu?'));
         $this->toolboxPoll->addTool($tooldel);
      }
      
   }

   public function addView() {
      $this->template()->addFile('tpl://edit.phtml');
   }

   public function editView() {
      $this->addView();
   }

   public function pollDataView() {
      if($this->poll != false AND $this->poll != null) {
         $this->template()->addTplFile('poll_read.phtml');
         $this->pollData = (string)$this->template();
      }
   }
}

?>