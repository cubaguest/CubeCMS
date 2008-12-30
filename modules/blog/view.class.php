<?php
/**
 * Viewer modulu blogu
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class BlogView extends View {
	
	/**
	 * Inicializace
	 */
	public function init() {
		$this->template()->addCss('style.css');
	}
	
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addTpl('addButtons.tpl');
			$this->template()->addVar('LINK_TO_ADD_SECTION_NAME', _('Přidat sekci'));
			$this->template()->addVar('LINK_TO_ADD_BLOG_NAME', _('Přidat blog'));
//
//			$this->template()->addVar('WRITABLE', true);
//
////			$this->template()->addVar('IN_SECTION', $this->getModel()->inSection);
//
//			$this->template()->addVar('LINK_TO_ADD_SECTION_NAME', _("Přidat sekci"));
////			$this->template()->addVar('LINK_TO_ADD_SECTION', $this->getModel()->linkToAddSection);
//
//			$this->template()->addVar('LINK_TO_ADD_BLOG_NAME', _("Přidat blog"));
////			$this->template()->addVar('LINK_TO_ADD_BLOG', $this->getModel()->linkToAddBlog);
//

		}
		$this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
		$this->template()->addTpl('blogList.tpl');

		$this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
		$this->container()->getEplugin('scroll')->assignToTpl($this->template());
	}

	public function showView() {
		;
	}

	/**
	 * Metoda pro přiřazení listu galeríí
	 *
	 */
	private function assignBlogList() {
		$this->template()->addTpl("listGaleries.tpl");

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
	}
	
	/**
	 * Viewer pro přidání sekce
	 */
	public function addsectionView() {
		$this->template()->setTplSubLabel(_('Přidání sekce blogů'));
		
		$this->template()->addTpl('buttonBack.tpl');
		
		$this->template()->addTpl('editSection.tpl');
		
//		$this->template()->addVar('SECTION_ARRAY', $this->getModel()->sectionArray);
		
		$this->template()->addVar('SECTION_LABEL_NAME', _('Název sekce'));
		$this->template()->addVar('SECTION_FIELDSET_NAME', _('Název sekce'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		
		$this->template()->addTpl('buttonBack.tpl');
//		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
	}
	
	
}

?>