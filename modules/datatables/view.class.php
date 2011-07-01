<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class DataTables_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit data'),
                 $this->link()->route('edit'));
         $toolET->setIcon('table_edit.png')->setTitle($this->tr("Upravit data"));
         $toolbox->addTool($toolET);
         
         $this->toolbox = $toolbox;

         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }

      // text nebyl zadán
      if($this->text == false){
         $this->text = new Object();
         $this->text->{Text_Model::COLUMN_TEXT} = null;
         if($this->category()->getRights()->isWritable()){
            $this->text->{Text_Model::COLUMN_TEXT} = $this->tr('Text nebyl vytvořen. Upravíte jej v administraci.');
         }
      } else {
         $this->text->{Text_Model::COLUMN_TEXT} = $this->template()->filter((string)$this->text->{Text_Model::COLUMN_TEXT}, array('anchors'));
      }
   }
   
   public function editView() {
      $this->template()->setEdit(true);
      $this->template()->addFile('tpl://edit.phtml');
   }
}

?>