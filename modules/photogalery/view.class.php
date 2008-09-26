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
			
			$this->template()->addVar('IN_SECTION', $this->getModel()->inSection);
			
			$this->template()->addTpl('addSectionButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_SECTION_NAME', _("Přidat sekci"));
			$this->template()->addVar('LINK_TO_ADD_SECTION', $this->getModel()->linkToAddSection);
			
			$this->template()->addTpl('addGaleryButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_GALERY_NAME', _("Přidat galerii"));
			$this->template()->addVar('LINK_TO_ADD_GALERY', $this->getModel()->linkToAddGalery);			
			
			$this->template()->addTpl('addPhotosButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _("Přidat fotky"));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS', $this->getModel()->linkToAddPhotos);
		}
		$this->assignGaleryList();
	}
	
	/**
	 * Metoda pro přiřazení listu galeríí
	 *
	 */
	private function assignGaleryList() {
		$this->template()->addTpl("listGaleries.tpl");

//		převedení obrázků z galerie na zvláštní pole
		foreach ($this->getModel()->allGaleryArray as $key => $galery) {
			for ($i=0; $i < $this->getModel()->countOfPhotos; $i++){
				if($galery[self::COLUM_PHOTOS_FILE_PREFIX_IMAG.$i] != null){
					$this->getModel()->allGaleryArray[$key][self::COLUM_SMALL_IMAGES][] =
						array(self::COLUM_PHOTOS_FILE => $galery[self::COLUM_PHOTOS_FILE_PREFIX_IMAG.$i],
							  self::COLUM_PHOTOS_LABEL_IMAG => $galery[self::COLUM_PHOTOS_LABEL_PREFIX_IMAG.$i]);
				}
			}
		}
		
//		echo "<pre>";
//		print_r($this->getModel()->allGaleryArray);
//		echo "</pre>";
		
		$this->template()->addVar("GALERIES_LIST_ARRAY", $this->getModel()->allGaleryArray);
		$this->template()->addVar("GALERIES_COUNT_PHOTOS", $this->getModel()->countOfPhotos);
		$this->template()->addVar("GALERIES_DIR_TO_PHOTOS", $this->getModel()->dirToImages);
		$this->template()->addVar("GALERIES_DIR_TO_MEDIUM_PHOTOS", $this->getModel()->dirToMediumImages);
		$this->template()->addVar("GALERIES_DIR_TO_SMALL_PHOTOS", $this->getModel()->dirToSmallImages);

		$this->template()->addVar("ADD_TEXT", _("Přidáno"));
		$this->template()->addVar("NOT_ANY_IMAGE", _("Není uložen žádný obrázek"));

//		$this->template()->addVar("DATE_FORMAT", _("%d.%m.%Y "));
		
		$this->template()->addVar("GALERIES_LIST_NAME", _("Galerie"));
		
		//TODO scrolovátka
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		$this->getModel()->scroll->assignToTpl($this->template());
		
		$this->template()->addTpl('buttonBack.tpl');
		

		$this->template()->addVar('GALERY_TEXT_AUTHOR', _('Autor'));
		$this->template()->addVar('GALERY_TEXT_LABEL', _('Popis'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Text'));
		
		//		Js plugin pro zobrazení velké fotky
		$lightbox = new LightBox();
		$this->template()->addJsPlugin($lightbox);
	}
	
	
	public function sectionShowView() {
		if($this->getRights()->isWritable()){
			$this->template()->addVar('WRITABLE', true);
			
			$this->template()->addVar('IN_SECTION', $this->getModel()->inSection);
						
			$this->template()->addTpl('addGaleryButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_GALERY_NAME', _("Přidat galerii"));
			$this->template()->addVar('LINK_TO_ADD_GALERY', $this->getModel()->linkToAddGalery);			
			
			$this->template()->addTpl('addPhotosButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _("Přidat fotky"));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS', $this->getModel()->linkToAddPhotos);

			$this->template()->addVar('LINK_TO_EDIT_SECTION', $this->getModel()->linkToEdit);
			$this->template()->addVar('SECTION_ID', $this->getModel()->sectionId);
			$this->template()->addVar('SECTION_NAME', $this->getModel()->sectionName);
			$this->template()->addVar('BUTTON_EDIT', _('Upravit'));
			$this->template()->addVar('BUTTON_DELETE', _('Smazat'));
			$this->template()->addVar('DELETE_SECTION_CONFIRM_MESSAGE', _('Opravdu smazat sekci'));
			
			$this->template()->addJsPlugin(new SubmitForm());
		}
		
//		Je stejný jako hlavní viewer
		$this->assignGaleryList();
		
		$this->template()->setTplSubLabel($this->getModel()->sectionName);
		
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět na kompletní seznam galerií'));
	}
	
	/**
	 * Viewer pro přidání sekce
	 */
	public function addsectionView()
	{
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->addTpl('editSection.tpl');
		
		$this->template()->addVar('SECTION_ARRAY', $this->getModel()->sectionArray);
		
		$this->template()->addVar('SECTION_LABEL_NAME', _('Název sekce'));
		$this->template()->addVar('SECTION_FIELDSET_NAME', _('Název sekce'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		
		$this->template()->setTplSubLabel(_('Přidání sekce'));
		
		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
	}
	
	/**
	 * Viewer pro editaci sekce
	 */
	public function sectionEditsectionView() {
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->addTpl('editSection.tpl');
		
		$this->template()->addVar('SECTION_ARRAY', $this->getModel()->sectionArray);
		
		$this->template()->addVar('SECTION_LABEL_NAME', _('Název sekce'));
		$this->template()->addVar('SECTION_FIELDSET_NAME', _('Název sekce'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		
		$this->template()->setTplSubLabel(_('Úprava sekce').' '.$this->getModel()->defaultName);

		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));		
	}
	
	
	/**
	 * Viewer pro přidání galerie 
	 */
	public function addgaleryView() {
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->addTpl('editGaleryHeader.tpl');
		$this->template()->addTpl('selectSection.tpl');
		
		$this->template()->addVar('SELECT_SECTION_NAME', _('Výběr sekce'));
		
		$newSectionArray = array();
		
		foreach ($this->getModel()->sectionArray as $section) {
			$newSectionArray[$section[self::COLUM_SECTION_ID]] = $section[self::COLUM_SECTION_LABEL_IMAG];
		}
		$this->getModel()->sectionArray = $newSectionArray;		
		
		$this->template()->addVar('SELECT_SECTION', $this->getModel()->sectionArray);
		$this->template()->addVar('CREATE_NEW_SECTION', $this->getModel()->newSectionArray);
		$this->template()->addVar('CREATE_NEW_SECTION_NAME', _('Vytvoření nové sekce'));
		$this->template()->addVar('SECTION_LABEL_NAME', _('Název'));

		$this->template()->addVar('GALERY_ARRAY', $this->getModel()->newGaleryArray);
		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));
		$this->template()->addVar('GALERY_NEW_NAME', _('Název galerie'));
		
		
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));		
		
		$this->template()->addTpl('editGalery.tpl');
		
		$this->template()->setTplSubLabel(_('Přidání galerie'));
		
		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
	}
	
	/**
	 * Viewer pro přidání fotky
	 */
	public function addphotosView() {
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->addTpl('addPhoto.tpl');
		
		$this->template()->addVar('ADD_NEW_SECTION_OR_GALERY', _('Přidání nové sekce a galerie'));

		$this->template()->addVar('ADD_TO_GALERY', _('Přidání do galerie'));
		
		$this->template()->addVar('SELECT_SECTION_NAME', _('Výběr sekce'));
		
		$newSectionArray = array();
		
		foreach ($this->getModel()->sectionArray as $section) {
			$newSectionArray[$section[self::COLUM_SECTION_ID]] = $section[self::COLUM_SECTION_LABEL_IMAG];
		}
		$this->getModel()->sectionArray = $newSectionArray;		

		
		$this->template()->addVar('SELECT_SECTION', $this->getModel()->sectionArray);
		$this->template()->addVar('CREATE_NEW_SECTION', $this->getModel()->newSectionArray);
		$this->template()->addVar('CREATE_NEW_SECTION_NAME', _('Vytvoření nové sekce'));
		$this->template()->addVar('SELECT_SECTION_NAME', _('Zvolení sekce pro novou galerii'));
		$this->template()->addVar('ADD_TO_EXIST_SECTION', _('Přidání do existující sekce'));
		$this->template()->addVar('SECTION_LABEL_NAME', _('Název'));

		$this->template()->addVar('CREATE_NEW_GALERY', $this->getModel()->newGaleryArray);
		$this->template()->addVar('CREATE_NEW_GALERY_NAME', _('Vytvoření nové galerie'));
		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));
		
		
		$this->template()->addVar('GALERY_NEW_NAME', _('Výběr galerie'));
		
		$this->template()->addVar('SELECT_GALERY_NAME', _('Výběr galerie'));
		
		$galeryArray = array();
		
		$previousSection = null;
		foreach ($this->getModel()->galeryArray as $galery){
			if($previousSection != $galery[self::COLUM_SECTION_LABEL_IMAG]){
				$previousSection = $galery[self::COLUM_SECTION_LABEL_IMAG];
				$galeryArray[$galery[self::COLUM_SECTION_LABEL_IMAG]] = array();
				$galeryArray[$galery[self::COLUM_SECTION_LABEL_IMAG]][$galery[self::COLUM_GALERY_ID]]=$galery[self::COLUM_GALERY_LABEL_IMAG];
			} else {
				$galeryArray[$galery[self::COLUM_SECTION_LABEL_IMAG]][$galery[self::COLUM_GALERY_ID]]=$galery[self::COLUM_GALERY_LABEL_IMAG];
			}
		}
		$this->getModel()->galeryArray = $galeryArray;		
		
		$this->template()->addVar('SELECT_GALERY', $this->getModel()->galeryArray);
		$this->template()->addVar('SELECTED_GALERY_ID', $this->getModel()->idSelectedGalery);
		
		$this->template()->addVar('ADD_PHOTO', _('Přidání fotky'));
		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
		$this->template()->addVar('PHOTO_FILE_NAME', _('Soubor s fotkou (nebo zip archív)'));
		$this->template()->addVar('PHOTO_ARRAY', $this->getModel()->photoArray);

		
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));		
		
		
		$this->template()->setTplSubLabel(_('Přidání fotek'));;
		
		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
	}
	
	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddgaleryView(){
		$this->addgaleryView();
		
		$this->template()->addVar('SELECTED_SECTION', $this->getModel()->idSelectedSection);
	}

	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddphotosView(){
		$this->addphotosView();
		
		$this->template()->addVar('SELECTED_SECTION', $this->getModel()->idSelectedSection);
	}
	
	/**
	 * Viewer pro zobrazení detailu galerie
	 */
	public function showView() {
		
		if($this->getRights()->isWritable()){
			$this->template()->addVar('WRITABLE', true);
			
			$this->template()->addVar('BUTTON_EDIT', _('Upravit'));
			$this->template()->addVar('BUTTON_DELETE', _('Smazat'));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat fotku'));
			
			$this->template()->addTpl('addPhotosButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_PHOTOS_NAME', _("Přidat fotky"));
			$this->template()->addVar('LINK_TO_ADD_PHOTOS', $this->getModel()->linkToAddPhotos);
			
			$this->template()->addVar('LINK_TO_EDIT_GALERY', $this->getModel()->linkToEditGalery);
			$this->template()->addVar('GALERY_ID', $this->getModel()->galeryInfo[self::COLUM_GALERY_ID]);
			
			
			$this->template()->addVar('GALERY_LABEL', $this->getModel()->galeryInfo[self::COLUM_GALERY_LABEL_IMAG]);
			$this->template()->addVar('DELETE_GALERY_CONFIRM_MESSAGE', _('Opravdu smazat galerii'));
			
			$this->template()->addJsPlugin(new SubmitForm());
		}

		$this->template()->addTpl('listGalery.tpl');
		
		$this->template()->addVar('GALERY_LIST_ARRAY', $this->getModel()->galeryArray);
		
		$this->template()->addVar("GALERY_DIR_TO_PHOTOS", $this->getModel()->dirToImages);
		$this->template()->addVar("GALERY_DIR_TO_SMALL_PHOTOS", $this->getModel()->dirToSmallImages);
		$this->template()->addVar("GALERY_TEXT", $this->getModel()->galeryInfo[self::COLUM_GALERY_TEXT_IMAG]);

		$this->template()->addVar("LINK_TO_BACK", $this->getModel()->linkToBack);
		$this->template()->addVar("LINK_TO_BACK_NAME", _('Zpět'));

		
		
		$this->template()->setTplSubLabel($this->getModel()->galeryInfo[self::COLUM_GALERY_LABEL_IMAG]);
	}
	
	/**
	 * Viewer pro zobrazení fotky
	 */
	public function showPhotoView() {
		$this->template()->setTplSubLabel($this->getModel()->photoDetailArray[self::COLUM_GALERY_LABEL_IMAG].' - '.$this->getModel()->photoDetailArray[self::COLUM_PHOTOS_LABEL_IMAG]);
		
		if($this->getRights()->isWritable()){
			$this->template()->addVar('WRITABLE', true);
			
			$this->template()->addVar('BUTTON_EDIT', _('Upravit'));
			$this->template()->addVar('BUTTON_DELETE', _('Smazat'));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat fotku'));
			
			$this->template()->addJsPlugin(new SubmitForm());
			
		}
		
		//scrolovátka
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		$this->getModel()->scroll->assignToTpl($this->template());
		
		$this->template()->addTpl('showPhoto.tpl');

//		scrolovátka
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		
//		tlačítko zpět
		$this->template()->addTpl('buttonBack.tpl');
		
//		Js plugin pro zobrazení velké fotky
		$lightbox = new LightBox();
		$this->template()->addJsPlugin($lightbox);
		
		$this->template()->addVar('PHOTO', $this->getModel()->photoDetailArray);

		$this->template()->addVar("LINK_TO_BACK", $this->getModel()->linkToBack);
		$this->template()->addVar("LINK_TO_BACK_NAME", _('Zpět do galerie'));
		
		$this->template()->addVar("DIR_TO_PHOTO", $this->getModel()->dirToImages);
		$this->template()->addVar("DIR_TO_MEDIUM_PHOTO", $this->getModel()->dirToMediumImages);
		
	}
	
	/**
	 * Viewer pro editaci galerie
	 */
	public function editgaleryView() {
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->addTpl('editGaleryHeader.tpl');
				
		$this->template()->addVar('SELECT_SECTION_NAME', _('Výběr sekce'));
		
		$this->template()->addVar('GALERY_ARRAY', $this->getModel()->galeryArray);
		$this->template()->addVar('GALERY_ID', $this->getModel()->idGalery);
		$this->template()->addVar('GALERY_LABEL_NAME', _('Název galerie'));
		$this->template()->addVar('GALERY_TEXT_NAME', _('Popis galerie'));
		$this->template()->addVar('GALERY_NEW_NAME', _('Název galerie'));
		
		
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));		
		
		$this->template()->addTpl('editGalery.tpl');
		
		$this->template()->setTplSubLabel(_('Úprava galerie').' - '.$this->getModel()->nameGalery);
		
		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
	}
	
	
	/**
	 * Viewer pro úpravu fotky
	 */
	public function editphotoView() {
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->setTplSubLabel(_('Úprava fotky ').' - '.$this->getModel()->photoName);
		
		
		$this->template()->addTpl('editPhoto.tpl');
		$this->template()->addVar("DIR_TO_SMALL_PHOTO", $this->getModel()->dirToSmallImages);
		
		
		
		$this->template()->addVar('PHOTO_ARRAY', $this->getModel()->photoArray);
		$this->template()->addVar('PHOTO_FILE', $this->getModel()->photoFile);
		$this->template()->addVar('PHOTO_NAME', $this->getModel()->photoName);
		$this->template()->addVar('PHOTO_ID', $this->getModel()->idPhoto);
		
		$this->template()->addVar('PHOTO_LABEL_NAME', _('Název fotky'));
		$this->template()->addVar('PHOTO_TEXT_NAME', _('Text fotky'));
		
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));

		$this->template()->addTpl('buttonBack.tpl');
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
	}

}

?>