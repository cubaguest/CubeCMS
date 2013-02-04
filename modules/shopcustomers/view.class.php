<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopCustomers_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);

      $toolboxItem = new Template_Toolbox2();
      $del = new Template_Toolbox2_Tool_Form($this->formDelete);
      $del->setIcon(Template_Toolbox2::ICON_DELETE)
            ->setConfirmMeassage($this->tr('Smazat zákazníka?'))
         ;
      $toolboxItem->addTool($del);

      $this->toolboxItem = $toolboxItem;
   }
}

?>