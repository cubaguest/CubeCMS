<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class AdvEventsAdmOrganizers_View extends AdvEventsBase_View {

   public function mainView()
   {
      parent::mainView();
      $this->template()->addFile('tpl://main.phtml');
      Template_Core::setFullWidth(true);

      // toolbo itemu
      $toolbox = new Template_Toolbox2();
      $toolbox->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

      $edit = new Template_Toolbox2_Tool_Redirect('edit', $this->tr('Upravit'));
      $edit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolbox->addTool($edit);

      $delete = new Template_Toolbox2_Tool_Form($this->formDelete);
      $delete->setConfirmMeassage($this->tr('Opravdu smazat organizátora?'));
      $delete->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolbox->addTool($delete);

      $this->template()->toolboxItem = $toolbox;
   }

   public function addOrganizerView()
   {
//      $this->setTinyMCE($this->formEdit->desc);
      $this->template()->addFile('tpl://edit.phtml');
      Template_Navigation::addItem($this->tr('Přidání nového organizátora'), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->tr('Přidání nového organizátora'));
   }

   public function editOrganizerView()
   {
//      $this->setTinyMCE($this->formEdit->desc);
      $this->template()->addFile('tpl://edit.phtml');
      $titName = sprintf($this->tr('Úprava organizátora %s'), $this->organizator->{AdvEventsBase_Model_Organizers::COLUMN_NAME});
      Template_Navigation::addItem($titName, $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($titName);
   }

   public function detailOrganizerView()
   {
      $this->template()->addFile('tpl://detail.phtml');
      Template_Navigation::addItem($this->organizator->{AdvEventsBase_Model_Organizers::COLUMN_NAME}, $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->organizator->{AdvEventsBase_Model_Organizers::COLUMN_NAME});
   }

}
