<?php
class PhotogaleryView extends View {
	/**
	 * Sloupec s polem obrázků
	 * @var string
	 */
	const COLUM_SMALL_IMAGES = 'images';
	const COLUM_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
	const COLUM_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';
	const COLUM_PHOTOS_FILE 	= 'file';
//	const COLUM_PHOTOS_LABEL 	= 'label';
	const COLUM_PHOTOS_LABEL_IMAG 			= 'photolabel';
	const COLUM_PHOTOS_SHOW_LINK 			= 'photoshowlink';
	

	const COLUM_SECTION_ID 					= 'id_section';
	const COLUM_SECTION_LABEL_IMAG 			= 'sectionlabel';
	const COLUM_GALERY_LABEL_IMAG 			= 'galerylabel';
	const COLUM_GALERY_TEXT_IMAG 			= 'galerytext';	
	const COLUM_GALERY_ID 					= 'id_galery';
	
	/**
	 * Počet fotek v jednom řádku
	 * @var integer
	 */
	const NUMBER_OF_PHOTO_IN_ROW = 3;
	
	/**
	 * Inicializace
	 */
	public function init() {
		$this->template()->addVar('NUMBER_OF_PHOTOS_IN_ROW', self::NUMBER_OF_PHOTO_IN_ROW);
		$this->template()->addCss('style.css');
	}
	
	
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addVar('WRITABLE', true);
			
			$this->template()->addTpl('addSectionButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_SECTION_NAME', _("Přidat sekci"));
			$this->template()->addVar('LINK_TO_ADD_SECTION', $this->container()->getLink('add_section'));
			
			$this->template()->addTpl('addGaleryButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_GALERY_NAME', _("Přidat galerii"));
			$this->template()->addVar('LINK_TO_ADD_GALERY', $this->container()->getLink('add_galery'));			
			
			$this->template()->addTpl('addPhotosButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _("Přidat fotky"));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS', $this->container()->getLink('add_photo'));
		}
		
		$this->template()->addTpl('sectionList.tpl');
		$this->template()->addCss('style.css');
		
		$this->template()->addVar('SECTIONS', $this->container()->getData('sections'));
		
		$this->assignGaleryList();
	}
	
	/**
	 * Metoda pro přiřazení listu galeríí
	 *
	 */
	private function assignGaleryList() {
		$this->template()->addVar("ADD_TEXT", _("Přidáno"));
		$this->template()->addVar('NUM_PHOTOS', _('Počet fotek'));
		$this->template()->addVar('NOT_ANY_GALERY', _('Žádná galerie nebyla přidána'));
		$this->template()->addVar('IMAGES_DIR', $this->container()->getData('small_images_dir'));
		
		
		//TODO scrolovátka
//		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
//		$this->getModel()->scroll->assignToTpl($this->template());
	}
	
	
	public function sectionShowView() {
		if($this->getRights()->isWritable()){
			$this->template()->addVar('SECTION_EDIT', true);
						
			$this->template()->addTpl('addGaleryButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_GALERY_NAME', _("Přidat galerii"));
			$this->template()->addVar('LINK_TO_ADD_GALERY', $this->container()->getLink('add_galery'));			
			
			$this->template()->addTpl('addPhotosButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _("Přidat fotky"));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS', $this->container()->getLink('add_photo'));

			$this->template()->addVar('LINK_TO_EDIT_SECTION', $this->container()->getLink('edit_section'));

			$this->assignEditFormButtons();
			
			$this->template()->addJsPlugin(new SubmitForm());
		}
		
		$this->template()->addVar('SECTION', $this->container()->getData('section'));
		$this->template()->setSubTitle($this->container()->getData('section_label'), true);
		$this->template()->setTplSubLabel($this->container()->getData('section_label'));
		
		
		$this->template()->addVar('GALERIES', $this->container()->getData('galeries'));
		
		$this->template()->addTpl('listGaleries.tpl');
		
//		Je stejný jako hlavní viewer
		$this->assignGaleryList();
		
		$this->assignButtonBack();
	}
	
	/**
	 * Viewer pro přidání sekce
	 */
	public function addsectionView()
	{
		$this->template()->addTpl('editSection.tpl');
		
		$this->template()->addVar('SECTION_ARRAY', $this->container()->getData('section'));

		$this->template()->addVar('SECTION_LABEL_NAME', _('Název sekce'));
		$this->template()->addVar('SECTION_FIELDSET_NAME', _('Název sekce'));
		
		$this->assignSendFormButtons();
				
		$this->template()->setTplSubLabel(_('Přidání sekce'));
		
		$this->assignButtonBack();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
	
	/**
	 * Viewer pro editaci sekce
	 */
	public function sectionEditsectionView() {
		$this->template()->addTpl('editSection.tpl');
		
		$this->template()->addVar('SECTION_ARRAY', $this->container()->getData('section'));
		
		$this->template()->addVar('SECTION_LABEL_NAME', _('Název sekce'));
		$this->template()->addVar('SECTION_FIELDSET_NAME', _('Název sekce'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		
		$this->template()->setTplSubLabel(_('Úprava sekce').' '.$this->container()->getData('section_label'));

		$this->assignButtonBack();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
	
	
	/**
	 * Viewer pro přidání galerie 
	 */
	public function addgaleryView() {
		
		$this->template()->addVar('SELECT_SECTION_NAME', _('Výběr sekce'));
		
		$newSectionArray = array();
		
		foreach ($this->container()->getData('sections') as $section) {
			$newSectionArray[$section[self::COLUM_SECTION_ID]] = $section[self::COLUM_SECTION_LABEL_IMAG];
		}
		
		$this->template()->addVar('SECTIONS', $newSectionArray);
		$this->template()->addVar('SECTION_SELECT', $this->container()->getData('section_select'));
		$this->template()->addVar('SECTIONS_SELECT_NAME', _('Výběr sekce'));

		$this->template()->addVar('DATE_SELECT_NAME', _('Datum vytvoření'));
		$this->template()->addVar('DATE_SELECT', $this->container()->getData('date_select'));
		
		$this->template()->addVar('GALERY_ARRAY', $this->container()->getData('galery'));
		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));
		
		$this->template()->addTpl('editGalery.tpl');
		
		$this->assignSendFormButtons();
		
		$this->template()->setTplSubLabel(_('Přidání galerie'));
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
		
		$this->assignButtonBack();
	}
	
	/**
	 * Viewer pro přidání fotky
	 */
	public function addphotosView() {
		$this->template()->addTpl('addPhoto.tpl');
		
//		Uprava pole s galeriema pro zobrazení ve smart
		$previousSection = null;
		foreach ($this->container()->getData('galeries') as $galery){
			if($previousSection != $galery[self::COLUM_SECTION_LABEL_IMAG]){
				$previousSection = $galery[self::COLUM_SECTION_LABEL_IMAG];
				$galeryArray[$galery[self::COLUM_SECTION_LABEL_IMAG]] = array();
				$galeryArray[$galery[self::COLUM_SECTION_LABEL_IMAG]][$galery[self::COLUM_GALERY_ID]]=$galery[self::COLUM_GALERY_LABEL_IMAG];
			} else {
				$galeryArray[$galery[self::COLUM_SECTION_LABEL_IMAG]][$galery[self::COLUM_GALERY_ID]]=$galery[self::COLUM_GALERY_LABEL_IMAG];
			}
		}
		
		$this->template()->addVar('GALERIES', $galeryArray);
		$this->template()->addVar('GALERY_SELECTED', $this->container()->getData('galery_sel'));
		$this->template()->addVar('ADD_TO_GALERY', _('Přidání do galerie'));
		
//		Nová galerie
		$this->template()->addVar('GALERY', $this->container()->getData('galery'));
		$this->template()->addVar('ADD_NEW_SECTION_OR_GALERY', _('Přidání nové galerie'));
		$this->template()->addVar('CREATE_NEW_GALERY_NAME', _('Vytvoření nové galerie'));
		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));		
		$this->template()->addVar('DATE_SELECT_NAME', _('Datum vytvoření'));
		$this->template()->addVar('DATE_SELECT', $this->container()->getData('date_select'));
		
//		Sekce
		foreach ($this->container()->getData('sections') as $section) {
			$newSectionArray[$section[self::COLUM_SECTION_ID]] = $section[self::COLUM_SECTION_LABEL_IMAG];
		}
		$this->template()->addVar('SECTIONS', $newSectionArray);
		$this->template()->addVar('SELECT_SECTION_NAME', _('Výběr sekce'));
		$this->template()->addVar('SELECT_SECTION_NAME', _('Zvolení sekce pro novou galerii'));
		
		
//		Fotky
		$this->template()->addVar('PHOTO', $this->container()->getData('photo'));
		$this->template()->addVar('ADD_PHOTO', _('Přidání fotky'));
		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
		$this->template()->addVar('PHOTO_FILE_NAME', _('Soubor s fotkou (nebo zip archív)'));
		
		
//		Ostatní
		$this->template()->setTplSubLabel(_('Přidání fotek'));
		$this->container()->getEplugin('progressbar')->assignToTpl($this->template());
				
		$this->assignSendFormButtons();
		
//		Záložky s jazyky
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
		
		$this->assignButtonBack();
	}
	
	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddgaleryView(){
		$this->addgaleryView();
	}

	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddphotosView(){
		$this->addphotosView();
	}
	
	/**
	 * Viewer pro zobrazení detailu galerie
	 */
	public function showView() {
		
		if($this->getRights()->isWritable()){
			$this->template()->addVar('GALERY_EDIT', true);
			$this->template()->addVar('PHOTO_EDIT', true);
			
			$this->template()->addTpl('addPhotosButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _("Přidat fotky"));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS', $this->container()->getLink('add_photo'));
			
			$this->template()->addVar('LINK_TO_EDIT_GALERY', $this->container()->getLink('edit_galery'));
			
//			Editace fotek
			$this->template()->addVar('LINK_TO_EDIT_PHOTOS', $this->container()->getLink('edit_photos'));
			$this->template()->addVar('DELETE_SELECTED_PHOTOS', _('vybrané fogorafie'));
			$this->template()->addVar('NOT_ANY_PHOTO_CHECKED', _('Nebyla vybrána žádná fotka'));
			$this->template()->addJS('function.js');
			
			$this->template()->addVar('CHECK_ALL_CHECKBOXS', _('Zaškrtnout vše'));
			$this->template()->addVar('UNCHECK_ALL_CHECKBOXS', _('Odškrtnout vše'));
			
			$this->assignEditFormButtons();
		}

		$this->template()->addTpl('listGalery.tpl');
		
		
		$this->template()->addVar('GALERY', $this->container()->getData('galery'));
		$galName = $this->container()->getData('galery');
		$this->template()->setTplSubLabel($galName[self::COLUM_GALERY_LABEL_IMAG]);
		$this->template()->setSubTitle($galName[self::COLUM_GALERY_LABEL_IMAG], true);
				
		$this->template()->addVar("PHOTOS", $this->container()->getData('photos'));
		$this->template()->addVar("IMAGES_DIR", $this->container()->getData('small_images_dir'));
		$this->template()->addVar("NOT_ANY_PHOTO", _('Není vložena žádná fotka'));

		$this->assignGaleryList();
		
		$this->assignButtonBack();
	}
	
	/**
	 * Viewer pro zobrazení fotky
	 */
	public function showPhotoView() {
		if($this->getRights()->isWritable()){
			$this->template()->addVar('PHOTO_EDIT', true);
			
			$this->template()->addVar('BUTTON_EDIT', _('Upravit'));
			$this->template()->addVar('BUTTON_DELETE', _('Smazat'));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat fotku'));

			$this->template()->addVar('EDIT_LINK', $this->container()->getLink('link_edit'));
			
			$this->template()->addJsPlugin(new SubmitForm());
		}
		
		//scrolovátka
		$scroll = $this->container()->getEplugin('scroll');
		$this->template()->addTpl($scroll->getTpl(), true);
		$scroll->assignToTpl($this->template());
		
		$this->template()->addTpl('showPhoto.tpl');

		$this->template()->setTplSubLabel($this->container()->getData('galery_label'));
		$this->template()->setTplSubLabel(' - '.$this->container()->getData('photo_label'), true);
		$this->template()->setSubTitle($this->container()->getData('galery_label'));
		$this->template()->setSubTitle($this->container()->getData('photo_label'), true);

		
//		Js plugin pro zobrazení velké fotky
		$lightbox = new LightBox();
		$this->template()->addJsPlugin($lightbox);
		
		$this->template()->addVar('PHOTO', $this->container()->getData('photo'));

//		Cesty k fotkám
		$this->template()->addVar("DIR_TO_MEDIUM_PHOTO", $this->container()->getData('images_dir'));
		$this->template()->addVar("DIR_TO_PHOTO", $this->container()->getData('images_big_dir'));
		
		$this->assignButtonBack();
	}
	
	/**
	 * Viewer pro editaci galerie
	 */
	public function editgaleryView() {
		$this->template()->addVar('SELECT_SECTION_NAME', _('Výběr sekce'));
		
		$newSectionArray = array();
		
		foreach ($this->container()->getData('sections') as $section) {
			$newSectionArray[$section[self::COLUM_SECTION_ID]] = $section[self::COLUM_SECTION_LABEL_IMAG];
		}
		
		$this->template()->addVar('SECTIONS', $newSectionArray);
		$this->template()->addVar('SECTION_SELECT', $this->container()->getData('section_select'));
		$this->template()->addVar('SECTIONS_SELECT_NAME', _('Výběr sekce'));

		$this->template()->addVar('DATE_SELECT_NAME', _('Datum vytvoření'));
		$this->template()->addVar('DATE_SELECT', $this->container()->getData('date_select'));
		
		$this->template()->addVar('GALERY_ARRAY', $this->container()->getData('galery'));
		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));
		
		$this->assignSendFormButtons();
		
		$this->template()->addTpl('editGalery.tpl');
		
		$this->template()->setTplSubLabel(_('Úprava galerie').' - '.$this->container()->getData('galery_label'));
		
		$this->assignButtonBack();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
	
	
	/**
	 * Viewer pro úpravu fotky
	 */
	public function editphotoView() {
		
		$this->template()->setTplSubLabel(_('Úprava fotky ').' - '.$this->container()->getData('photo_label'));
		$this->template()->setSubTitle(_('Úprava fotky ').' - '.$this->container()->getData('photo_label'));
		
		
		$this->template()->addTpl('editPhoto.tpl');

		$this->template()->addVar("PHOTO_A", $this->container()->getData('photo'));
		$this->template()->addVar("PHOTO_ID", $this->container()->getData('photo_id'));
		
		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
		
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));

		$this->assignButtonBack();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}

	/**
	 * Viewer pro editaci více fotek
	 */
	public function editphotosView() {
//		Fotky
		$this->template()->addVar('PHOTOS', $this->container()->getData('photos'));
		$this->template()->addVar('PHOTOS_DIR', $this->container()->getData('photos_dir'));
		$this->template()->addTpl('editPhotoList.tpl');
		$this->template()->setTplSubLabel(_('Úprava fotek'));
		
		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
		
		$this->assignButtonBack();
		$this->assignSendFormButtons();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
	
	
	/**
	 * Metoda pro vygenerování odkazu zpět
	 */
	private function assignButtonBack(){
		//		tlačítko zpět
		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar("LINK_TO_BACK", $this->container()->getLink('link_back'));
		$this->template()->addVar("LINK_TO_BACK_NAME", _('Zpět'));
	}
	
	private function assignEditFormButtons() {
		$this->template()->addVar('BUTTON_EDIT', _('Upravit'));
		$this->template()->addVar('BUTTON_DELETE', _('Smazat'));
		$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat'));
		$this->template()->addJsPlugin(new SubmitForm());
	}

	private function assignSendFormButtons() {
		$this->template()->addVar('BUTTON_RESET', _('obnovit'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
	}
	
}

?>