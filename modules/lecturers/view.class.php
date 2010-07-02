<?php
class Lecturers_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('lecturer_add', $this->_('Přidat lektora'), $this->link()->route('add'));
         $toolAdd->setIcon('user_add.png')->setTitle($this->_("Přidat nového lektora"));
         $toolbox->addTool($toolAdd);

         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('lecture_edit', $this->_("Upravit lektora"));
         $toolEdit->setIcon('user_edit.png')->setTitle($this->_('Upravit lektora'));
         $toolboxEdit->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('user_delete.png');
         $toolDelete->setConfirmMeassage($this->_('Opravdu smazat lektora?'));
         $toolboxEdit->addTool($toolDelete);

//         $toolDelete = new Template_Toolbox2_Tool_PostConfirm('lecturer_delete', $this->_('Smazat lektora'));
//         $toolDelete->setIcon('user_delete.png')->setTitle($this->_('Smazat lektora'));
//         $toolDelete->setSubmitValue('lecturer_id', 0);
//         $toolDelete->setConfirmMeassage($this->_('Opravdu smazat lektora?'));
//         $toolboxEdit->addTool($toolDelete);
         
         $this->toolboxEdit = $toolboxEdit;
      }
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->edit = true;
      $this->addView();
      // cestak obrázků
      $this->imagePath = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.Lecturers_Controller::DATA_DIR.URL_SEPARATOR;
   }
}

?>
