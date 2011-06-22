<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Redirect_View extends View {
   public function mainView()
   {
      $this->template()->addFile('tpl://redirect.phtml');
      $this->toolbox = new Template_Toolbox2();
      $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
      $this->link()->route(Routes::MODULE_SETTINGS));
      $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
      $this->toolbox->addTool($toolEView);
   }
}

?>