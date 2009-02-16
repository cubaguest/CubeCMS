<?php
class PhotogaleryView extends View {
	/**
	 * Inicializace
	 */
	public function init() {
		$this->template()->addCss('style.css');
	}
	
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addVar('WRITABLE', true);
			
			$this->template()->addTpl('addGaleryButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_GALERY_NAME', _("Přidat galerii"));

            //Jsplugin pro zobrazení editace
            $jQuery = new JQuery();
            $this->template()->addJsPlugin($jQuery);
		}
      $this->template()->addTpl('galeriesList.tpl');

      //TODO korektní cestu
      $this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
      $this->container()->getEplugin('scroll')->assignToTpl($this->template());

      $this->template()->addVar('NOT_ANY_GALERY', _('Źádná galerie nebyla nahrána'));
	}

    /**
	 * Viewer pro přidání galerie
	 */
	public function addgaleryView() {
		$this->template()->addVar('DATE_SELECT_NAME', _('Datum vytvoření'));
		$this->template()->addVar('DATE_SELECT', $this->container()->getData('date_select'));

		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));
		$this->template()->addVar('PHOTO_FILE_LABEL', _('Obrázek'));
		$this->template()->addVar('FORM_ADD_PHOTO_LABEL_LABEL', _('Popis'));
		$this->template()->addVar('FORM_ADD_PHOTO_FILE_LABEL', _('Soubor'));
		$this->template()->addVar('ADD_INPUTS_FIELDS', _('Přidat další soubor'));
		$this->template()->addVar('ADD_NEW_GALERY_NAME', _('Přidat novou galerii'));
		$this->template()->addVar('ADD_EXISTING_GALERY_NAME', _('Přidat do existující galerie'));

		$this->template()->addTpl('addGalery.tpl');

		$this->template()->setTplSubLabel(_('Přidání galerie'));

		$jQuery = new JQuery();
      $jQuery->addWidgentTabs();
		$this->template()->addJsPlugin($jQuery);

		$this->template()->addVar("BUTTON_BACK_NAME", _('Zpět'));
		$this->template()->addVar("BUTTON_SEND", _('Odeslat'));
		$this->template()->addVar("BUTTON_RESET", _('Obnovit'));
	}

   /**
	 * Viewer pro zobrazení detailu galerie
	 */
	public function showView() {

		if($this->getRights()->isWritable()){
			$this->template()->addVar('EDITABLE', true);
			$this->template()->addVar('BUTTON_DELETE_PHOTO', _('Smazat fotku'));
			$this->template()->addVar('DELETE_PHOTO_CONFIRM_MESSAGE', _('Opravdu smazat fotku'));
			$this->template()->addVar('DELETE_GALERY_CONFIRM_MESSAGE', _('Opravdu smazat galerii i s fotkami'));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _('Přidat fotky'));
			$this->template()->addVar('LINK_TO_EDIT_GALERY_NAME', _('Upravit galerii'));
			$this->template()->addVar('LINK_TO_DELETE_GALERY_NAME', _('Smazat galerii'));

         $this->template()->addJsPlugin(new SubmitForm());
		}

      $jQuery = new JQuery();
      $this->template()->addJsPlugin($jQuery);
      $this->template()->addJsPlugin(new LightBox());

		$this->template()->addTpl('galeryList.tpl');

      $this->template()->addVar('NOT_ANY_PHOTOS', _('Nebyly uloženy žádné fotky'));

      $this->template()->addVar("BUTTON_BACK_NAME", _('Zpět'));

      $galery = $this->container()->getData('GALERY_DATA');
      $this->template()->setTplSubLabel($galery[GaleryDetailModel::COLUMN_GALERY_LABEL]);
      $this->template()->setSubTitle($galery[GaleryDetailModel::COLUMN_GALERY_LABEL], true);
	}

   /**
    * Viewer pro přidání fotky v galerii
    */
   public function addphotoView() {

		$jQuery = new JQuery();
      $jQuery->addWidgentTabs();
		$this->template()->addJsPlugin($jQuery);
      
      $this->template()->addTpl('addPhoto.tpl');

      $this->template()->addVar('PHOTO_FILE_LABEL', _('Obrázek'));
		$this->template()->addVar('FORM_ADD_PHOTO_LABEL_LABEL', _('Popis'));
		$this->template()->addVar('FORM_ADD_PHOTO_FILE_LABEL', _('Soubor'));
		$this->template()->addVar('ADD_INPUTS_FIELDS', _('Přidat další soubor'));

      $this->template()->addVar("BUTTON_BACK_NAME", _('Zpět'));
		$this->template()->addVar("BUTTON_SEND", _('Odeslat'));
		$this->template()->addVar("BUTTON_RESET", _('Obnovit'));

      $this->template()->setTplSubLabel(_('Přidání fotky do galerie').' &#132'.$this->container()->getData('GALERY_LABEL').'&#132');
   }

   /**
	 * Viewer pro editaci galerie
	 */
	public function editgaleryView() {
		$this->template()->addVar('DATE_SELECT_NAME', _('Datum vytvoření'));

		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));

		$jQuery = new JQuery();
      $jQuery->addWidgentTabs();
		$this->template()->addJsPlugin($jQuery);

      $this->template()->addTpl('editGalery.tpl');

		$this->template()->setTplSubLabel(_('Úprava galerie').' &#132'.$this->container()->getData('GALERY_LABEL').'&#132');

      $this->template()->addVar("BUTTON_BACK_NAME", _('Zpět'));
		$this->template()->addVar("BUTTON_SEND", _('Odeslat'));
		$this->template()->addVar("BUTTON_RESET", _('Obnovit'));
	}

	/**
	 * Viewer pro zobrazení fotky
	 */
//	public function showPhotoView() {
//		if($this->getRights()->isWritable()){
//			$this->template()->addVar('PHOTO_EDIT', true);
//
//			$this->template()->addVar('BUTTON_EDIT', _('Upravit'));
//			$this->template()->addVar('BUTTON_DELETE', _('Smazat'));
//			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat fotku'));
//
//			$this->template()->addVar('EDIT_LINK', $this->container()->getLink('link_edit'));
//
//			$this->template()->addJsPlugin(new SubmitForm());
//		}
//
//		//scrolovátka
//		$scroll = $this->container()->getEplugin('scroll');
//		$this->template()->addTpl($scroll->getTpl(), true);
//		$scroll->assignToTpl($this->template());
//
//		$this->template()->addTpl('showPhoto.tpl');
//
//		$this->template()->setTplSubLabel($this->container()->getData('galery_label'));
//		$this->template()->setTplSubLabel(' - '.$this->container()->getData('photo_label'), true);
//		$this->template()->setSubTitle($this->container()->getData('galery_label'));
//		$this->template()->setSubTitle($this->container()->getData('photo_label'), true);
//
//
////		Js plugin pro zobrazení velké fotky
//		$lightbox = new LightBox();
//		$this->template()->addJsPlugin($lightbox);
//
//		$this->template()->addVar('PHOTO', $this->container()->getData('photo'));
//
////		Cesty k fotkám
//		$this->template()->addVar("DIR_TO_MEDIUM_PHOTO", $this->container()->getData('images_dir'));
//		$this->template()->addVar("DIR_TO_PHOTO", $this->container()->getData('images_big_dir'));
//
//		$this->assignButtonBack();
//	}
	
	
	
	/**
	 * Viewer pro úpravu fotky
	 */
//	public function editphotoView() {
//
//		$this->template()->setTplSubLabel(_('Úprava fotky ').' - '.$this->container()->getData('photo_label'));
//		$this->template()->setSubTitle(_('Úprava fotky ').' - '.$this->container()->getData('photo_label'));
//
//
//		$this->template()->addTpl('editPhoto.tpl');
//
//		$this->template()->addVar("PHOTO_A", $this->container()->getData('photo'));
//		$this->template()->addVar("PHOTO_ID", $this->container()->getData('photo_id'));
//
//		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
//		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
//
//		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
//		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
//
//		$this->assignButtonBack();
//
//		$tabcontent = new TabContent();
//		$this->template()->addJsPlugin($tabcontent);
//	}

	/**
	 * Viewer pro editaci více fotek
	 */
//	public function editphotosView() {
//		Fotky
//		$this->template()->addVar('PHOTOS', $this->container()->getData('photos'));
//		$this->template()->addVar('PHOTOS_DIR', $this->container()->getData('photos_dir'));
//		$this->template()->addTpl('editPhotoList.tpl');
//		$this->template()->setTplSubLabel(_('Úprava fotek'));
//
//		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
//		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
//
//		$this->assignButtonBack();
//		$this->assignSendFormButtons();
//
//		$tabcontent = new TabContent();
//		$this->template()->addJsPlugin($tabcontent);
//	}
}

?>