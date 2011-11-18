<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class DayMenu_View extends View {
   
   public function init() {
      parent::init();
      $this->days = array(
          1 => $this->tr('pondělí'), $this->tr('úterý'), $this->tr('středa'), $this->tr('čtvrtek'),
           $this->tr('pátek'), $this->tr('sobota'), $this->tr('neděle'),
      );
   }

   public function mainView() {
      $this->template()->addFile('tpl://text.phtml');

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit menu'),
               $this->link()->route('edit')->param('date', $this->date->format('d-m-Y')));
         $toolET->setIcon('page_edit.png')->setTitle($this->tr("Upravit text menu"));
         $toolbox->addTool($toolET);
         
//         $toolETTomorow = new Template_Toolbox2_Tool_PostRedirect('edit_text_tomorow', $this->tr('Upravit menu na zítřek'),
//               $this->link()->route('edit')->param('date', $this->date->format('D-M-Y')));
//         $toolETTomorow->setIcon('page_edit.png')->setTitle($this->tr("Upravit text menu na zítřek"));
//         $toolbox->addTool($toolETTomorow);
         $this->toolbox = $toolbox;
      }

      // text nebyl zadán
      if($this->text == false){
         $this->text = new Object();
         $this->text->{DayMenu_Model::COLUMN_TEXT} = $this->tr('Dnes nevaříme nebo menu nebylo připraveno.');
         if($this->category()->getRights()->isWritable()){
            $this->text->{DayMenu_Model::COLUMN_TEXT} = $this->tr('Menu pro tento den nebylo vytvořeno. Upravíte jej v administraci.');
         }
      } else {
         $this->text->{DayMenu_Model::COLUMN_TEXT} = $this->template()->filter((string)$this->text->{DayMenu_Model::COLUMN_TEXT}, array('anchors'));
      }
   }

   public function editView() {
      Template_Module::setEdit(true);
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->setTinyMCE($this->form->textPanel, 'simple');
      $this->template()->addTplFile("textedit.phtml");
   }
}

?>