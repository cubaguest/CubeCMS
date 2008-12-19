<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class BlogAction extends Action {
	
	public function addSection() {
		$actionAbbr = 'as';
		$this->addAction($actionAbbr, "addsection", _('pridani-sekce'));
		return $this->createActionUrl($actionAbbr);
	}

	public function addBlog() {
		$actionAbbr = 'ab';
		$this->addAction($actionAbbr, "addblog", _('pridani-blogu'));
		return $this->createActionUrl($actionAbbr);
	}
//	public function actions() {
//		$this->addAction("addsection", "as");
//		$this->addAction("addblog", "ab");
//
//		$this->addAction("editsection", "es");
//		$this->addAction("editblog", "eb");
//	}
	
}
?>