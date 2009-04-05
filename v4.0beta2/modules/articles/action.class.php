<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ArticlesAction extends Action {
	

	const ACTION_ADD_ARTICLE_ABBR = 'aa';
	const ACTION_EDIT_ARTICLE_ABBR = 'ea';

    protected function init() {
		$this->addAction(self::ACTION_ADD_ARTICLE_ABBR, "addarticle", _m('pridani-clanku'));
		$this->addAction(self::ACTION_EDIT_ARTICLE_ABBR, "editarticle", _m('uprava-clanku'));
    }


	public function addArticle() {
		return $this->createAction(self::ACTION_ADD_ARTICLE_ABBR);
	}

	public function editArticle() {
		return $this->createAction(self::ACTION_EDIT_ARTICLE_ABBR);
	}

}
?>