<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Pokus_Controller extends Controller {
   public function mainController() {

   }
   
   public function messagesController() {
      // testy hlášek
      $this->infoMsg()->addMessage('INFO hláška: Maecfenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec');
      $this->infoMsg()->addMessage('INFO hláška: Mafecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec');
      $this->infoMsg()->addMessage('INFO hláška: Mfaecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec');
      $this->errMsg()->addMessage('ERR hláška: Maecenfas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec');
      $this->errMsg()->addMessage('ERR hláška: Mafecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec');
      $this->errMsg()->addMessage('ERR hláška: Mfaecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec');
      trigger_error('COREERR hláška: Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis');
      trigger_error('COREERR hláška: Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis');
      trigger_error('COREERR hláška: Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis');
   }

   public function ajaxController(){
      $form = new Form('pokus_edit_');

      $elemText = new Form_Element_Text('text', 'text');
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemText);

      $elemSubmit = new Form_Element_Submit('send', 'send');
      $form->addElement($elemSubmit);
      
      if ($form->isValid()){
         $_SESSION['sended'] = $form->text->getValues();
         $this->infoMsg()->addMessage('odeslano');
         $this->link()->reload();
      }

      if(isset ($_SESSION['sended'])){
         $this->view()->send = $_SESSION['sended'];
         unset ($_SESSION['sended']);
      }

      $this->view()->form = $form;
   }


}

?>