<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class CodeBook_View extends View {
	public function mainView()
   {
      $this->template()->addFile('tpl://codebook:main.phtml');
      
      $toolbox = new Template_Toolbox2();

      $toolHome = new Template_Toolbox2_Tool_Redirect('editItem', $this->tr('Upravit položku'));
      $toolHome->setIcon(Template_Toolbox2::ICON_PEN)->setAction($this->link()->route("edit"));
      $toolbox->addTool($toolHome);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE)->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
      $toolRemove->setImportant(true);
      $toolbox->addTool($toolRemove);

      $this->toolboxItem = $toolbox;
   }
   
	public function addView()
   {
      Template::setFullWidth(true);
      $this->template()->addFile('tpl://codebook:edit.phtml');
      Template_Navigation::addItem($this->tr('Přidání položky'), $this->link());
   }
   
	public function editView()
   {
      Template::setFullWidth(true);
      $this->template()->addFile('tpl://codebook:edit.phtml');
      Template_Navigation::addItem(sprintf($this->tr('Úprava položky %s'), $this->itemName), $this->link());
   }
}
