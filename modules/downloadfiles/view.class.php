<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class PressReports_View extends View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://main.phtml');
      if($this->category()->getRights()->isWritable()) {
         $this->controlls = true;
         
         $toolbox = new Template_Toolbox2();
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('press_report_add', $this->tr('Přidat zprávu'), $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr("Přidat novou zprávu"));
         $toolbox->addTool($toolAdd);
         
         $toolbox->setIcon(Template_Toolbox2::ICON_ADD);
         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon('page_edit.png');

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('press_report_edit', $this->tr("Upravit zprávu"));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit tiskovou zprávu'));
         $toolboxEdit->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('page_delete.png');
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat tiskovou zprávu?'));
         $toolboxEdit->addTool($toolDelete);
         
         foreach ($this->template()->reports as &$report) {
            $toolboxEdit->press_report_edit->setAction($this->link()->route('edit', array('id' => $report->{PressReports_Model::COLUMN_ID})));
            $this->formDelete->id->setValues($report->{PressReports_Model::COLUMN_ID});
            $toolDelete->setConfirmMeassage(sprintf($this->tr('Opravdu smazat tiskovou zprávu %s ?'), $report->{PressReports_Model::COLUMN_NAME}));
            $report->toolbox = clone $toolboxEdit;
         }
         
//          $this->toolboxItem = $toolboxEdit;
      }
   }

   public function addView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit.phtml');
   }

   public function editView()
   {
      $this->addView();
   }     
}
?>