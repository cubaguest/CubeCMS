<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class News_Action extends Action {
   const ACTION_ADD_NEWS_ABBR = 'an';
   const ACTION_EDIT_NEWS_ABBR = 'en';

   protected function init() {
      $this->addAction(self::ACTION_ADD_NEWS_ABBR, "addnews", $this->_m('pridani-novinky'));
      $this->addAction(self::ACTION_EDIT_NEWS_ABBR, "editnews", $this->_m('uprava-novinky'));
   }

   public function addNews() {
      return $this->createAction(self::ACTION_ADD_NEWS_ABBR);
   }
   public function editNews() {
      return $this->createAction(self::ACTION_EDIT_NEWS_ABBR);
   }

}
?>