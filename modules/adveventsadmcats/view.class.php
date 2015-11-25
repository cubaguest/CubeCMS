<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class AdvEventsAdmCats_View extends AdvEventsBase_View {

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
      $delete->setConfirmMeassage($this->tr('Opravdu smazat kategorii?'));
      $delete->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolbox->addTool($delete);

      $this->template()->toolboxItem = $toolbox;
   }

   public function addCategoryView()
   {
//      $this->setTinyMCE($this->formEdit->desc);
      $this->template()->addFile('tpl://edit.phtml');
      Template_Navigation::addItem($this->tr('Přidání nové kategorie'), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->tr('Přidání nové kategorie'));
   }

   public function editCategoryView()
   {
      $this->setTinyMCE($this->formEdit->desc);
      $this->template()->addFile('tpl://edit.phtml');
      $titName = sprintf($this->tr('Úprava kateogire %s'), $this->evcat->{AdvEventsBase_Model_Categories::COLUMN_NAME});
      Template_Navigation::addItem($titName, $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($titName);
   }

   public function detailCategoryView()
   {
      $this->template()->addFile('tpl://detail.phtml');
      Template_Navigation::addItem($this->evcat->{AdvEventsBase_Model_Categories::COLUMN_NAME}, $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->evcat->{AdvEventsBase_Model_Categories::COLUMN_NAME});
   }

}
