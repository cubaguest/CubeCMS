<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class BlogAction extends Action {
    const ACTION_ADD_SECTION_ABBR = 'as';
    const ACTION_ADD_BLOG_ABBR = 'ab';

    protected function init() {
		$this->addAction(self::ACTION_ADD_SECTION_ABBR, "addsection", _('pridani-sekce'));
        $this->addAction(self::ACTION_ADD_BLOG_ABBR, "addblog", _('pridani-blogu'));
    }


	public function addSection() {
		return $this->createAction(self::ACTION_ADD_SECTION_ABBR);
	}

	public function addBlog() {
		return $this->createAction(self::ACTION_ADD_BLOG_ABBR);
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