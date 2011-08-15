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

         for ($day = 1; $day <= 7; $day++) {
            $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text_'.$day, 
               sprintf($this->tr('Upravit %s'), $this->days[$day]),
               $this->link()->route('edit', array('day' => $day)));
            $toolET->setIcon('page_edit.png')->setTitle(
               sprintf($this->tr("Upravit text pro %s"),$this->days[$day]));
            $toolbox->addTool($toolET);
         }

         $this->toolbox = $toolbox;

      }

      // text nebyl zadán
      if($this->text == false){
         $this->text = new Object();
         $this->text->{Text_Model::COLUMN_TEXT} = $this->tr('Dnes nevaříme.');
         if($this->category()->getRights()->isWritable()){
            $this->text->{Text_Model::COLUMN_TEXT} = $this->tr('Menu pro tento den nebylo vytvořeno. Upravíte jej v administraci.');
         }
      } else {
         $this->text->{Text_Model::COLUMN_TEXT} = $this->template()->filter((string)$this->text->{Text_Model::COLUMN_TEXT}, array('anchors'));
      }
   }

   public function editView() {
      Template_Module::setEdit(true);
      $this->addTinyMCE('text');
      $this->addTinyMCE('textPanel', 'simple');
      $this->template()->addTplFile("textedit.phtml");
   }

   private function addTinyMCE($elem, $type = 'advanced') {
      if($type == 'none') return;
      $this->form->{$elem}->html()->addClass("mceEditor".$type);
      $this->tinyMCE = new Component_TinyMCE();
      switch ($type) {
         case 'simple':
            $settings = new Component_TinyMCE_Settings_AdvSimple2();
            break;
         case 'full':
            // TinyMCE
            $settings = new Component_TinyMCE_Settings_Full();
            $settings->setSetting('height', '600');
            break;
         case 'advanced':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            $settings->setSetting('height', '600');
            break;
      }
      $settings->setSetting('editor_selector', 'mceEditor'.$type);
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
}

?>