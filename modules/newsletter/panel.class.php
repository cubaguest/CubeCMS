<?php
class NewsLetter_Panel extends Panel {
	
	public function panelController() {
      $form = $this->createRegisterForm();
      $this->template()->form = $form;
   }
	
	public function panelView() {
		$this->template()->addFile("tpl://panel.phtml");
	}

   private function createRegisterForm() {
      $form = new Form('regmail_');

      $elemMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $form->addElement($elemMail);

      $elemSend = new Form_Element_Submit('send', $this->_('Registrovat'));
      $form->addElement($elemSend);
      $form->setAction($this->link());

      return $form;
   }
}
?>