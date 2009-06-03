<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class PokusController extends Controller {
	public function mainController() {
      $eplMail = new MailEplugin();

      $eplMail->crTrValue('pokus', _m('pokusný text'))
      ->crTrValue('sudy', _m('Jestli byla položka vynucena'), MailEplugin::TRANSLATE_TYPE_BOOLEAN)
      ->crTrValue('pocet', _m('Počet  znaků zprávy'));
      $this->container()->addEplugin('mail', $eplMail);

      $form = new Form('pokus_');
      $form->crInputText('name')
      ->crSubmit('send');

      if($form->checkForm()){
         $eplMail->setTrValue('pokus', $form->getValue('name'));
         if(strlen($form->getValue('name')) % 2){
            $eplMail->setTrValue('sudy', true);
         } else {
            $eplMail->setTrValue('sudy', false);
         }
         $eplMail->setTrValue('pocet', mb_strlen($form->getValue('name')));

         $eplMail->sendMails();
      }

	}

	
}

?>