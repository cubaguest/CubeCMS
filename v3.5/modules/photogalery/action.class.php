<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class PhotogaleryAction extends Action {
	const ACTION_ADD_GALERY_ABBR = 'ag';
	const ACTION_EDIT_GALERY_ABBR = 'eg';
	const ACTION_ADD_PHOTO_ABBR = 'ap';

    protected function init() {
		$this->addAction(self::ACTION_ADD_GALERY_ABBR, "addgalery", _('pridani-galerie'));
		$this->addAction(self::ACTION_EDIT_GALERY_ABBR, "editgalery", _('uprava-galerie'));
		$this->addAction(self::ACTION_ADD_PHOTO_ABBR, "addphoto", _('pridani-fotky'));
    }


	public function addGalery() {
		return $this->createAction(self::ACTION_ADD_GALERY_ABBR);
	}

	public function editGalery() {
		return $this->createAction(self::ACTION_EDIT_GALERY_ABBR);
	}
	
	public function addPhoto() {
		return $this->createAction(self::ACTION_ADD_PHOTO_ABBR);
	}

}
?>