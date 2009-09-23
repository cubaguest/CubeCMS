<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Pokus_Controller extends Controller {
	public function mainController() {
      $form = new Form('pokus_');
      $textElement = new Form_Element_Text('name', $this->_('Popisek'));
      $textElement->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($textElement);

      $form->addElement(new Form_Element_Submit('send'));

      if($form->isSend()){
         $this->infoMsg()->addMessage('odeslano');
      }

      $this->view()->form = $form;
	}

	
}

?>