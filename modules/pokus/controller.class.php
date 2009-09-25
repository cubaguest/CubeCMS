<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Pokus_Controller extends Controller {
	public function mainController() {
      $form = new Form('pokus_');
      $textElementL = new Form_Element_Text('label', $this->_('Popisek'));
      $textElementL->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($textElementL);

      $textElementN = new Form_Element_Text('name', $this->_('Jméno'));
//      $textElementN->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($textElementN);

      $textElementSN = new Form_Element_Text('surname', $this->_('Přijmení'));
      $textElementSN->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($textElementSN);

      $form->addElement(new Form_Element_Submit('send'));

      if($form->isValid()){
         $this->infoMsg()->addMessage('odeslano');
         $this->link()->reload();
      }

      $this->view()->form = $form;
	}

	
}

?>