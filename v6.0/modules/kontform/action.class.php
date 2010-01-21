<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Kontform_Action extends Action {

   const ACTION_EDIT_MAILS_ABBR = 'em';

   protected function init() {
      $this->addAction(self::ACTION_EDIT_MAILS_ABBR, "editMails", _('uprava-emailu'));
   }
   
   public function editMails() {
      return $this->createAction(self::ACTION_EDIT_MAILS_ABBR);
   }

}
?>