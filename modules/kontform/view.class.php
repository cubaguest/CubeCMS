<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Kontform_View extends View {
    //Původní viewer:
    //
//	public function mainView() {
//		$this->template()->addTpl("kontform.tpl");
//      /*
//       * Kvůli překladu je to uvedeno tady místo šablony.
//       * Šablony se už totiž nepřekládají a jsou do nich pouze vloženy data
//       */
//		$this->template()->addVar('INPUT_NAME', _m('Jméno'));
//		$this->template()->addVar('INPUT_SURNAME', _m('Přijmení'));
//		$this->template()->addVar('INPUT_EMAIL', _m('E-mail'));
//		$this->template()->addVar('INPUT_TEXT_QUESTION', _m('Dotaz'));
//		$this->template()->addVar('BUTTON_TEXT_SEND', _m('Odeslat'));
//		$this->template()->addVar('BUTTON_RESET', _m('Vymazat formulář'));
//		$this->template()->setTplSubLabel(_m('Kontaktní formulář'));
//
//
//
//
//      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět'));;

//Nový viewer:
 public function mainView() {
     $this->template()->addTplFile("kontform.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setActionTitle($this->_m("kontaktní formulář"));
	}
	/*EOF mainView*/
}

?>