<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class PokusView extends View {
   public function mainView() {
      $this->template()->addTpl("pokusform.tpl");

      $eplMail = $this->container()->getEplugin('mail');
      $eplMail->setTplSubName(_('pro odeslání'));
      $this->template()->addTpl($eplMail);
   }
}

?>