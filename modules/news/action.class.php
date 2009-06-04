<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class NewsAction extends Action {
	

	const ACTION_ADD_NEWS_ABBR = 'an';

    protected function init() {
		$this->addAction(self::ACTION_ADD_NEWS_ABBR, "addnews", $this->_m('pridani-novinky'));
    }


	public function addNews() {
		return $this->createAction(self::ACTION_ADD_NEWS_ABBR);
	}

}
?>