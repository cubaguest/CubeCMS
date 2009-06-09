<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextView extends View {
	public function mainView() {
      if((bool)$this->sys()->module()->getParam(TextController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
      }
      $this->template()->addTplFile("text.phtml");
      $this->template()->addCssFile("style.css");

      $model = new TextDetailModel();
      $this->template()->text = $model->getText();
	}
	/*EOF mainView*/
	
	public function edittextView() {
		$this->template()->addTpl("textedit.tpl");
		$this->template()->addVar('TEXT_NAME', _m('Text'));
		$this->template()->addVar('BUTTON_TEXT_SEND', _m('Odeslat'));
		$this->template()->addVar('BUTTON_RESET', _m('Obnovit'));
		$this->template()->setTplSubLabel(_m('Úprava textu'));
		
		$tinymce = new TinyMce();
      if($this->getModule()->getParam(TextController::PARAM_THEME, 'advanced') == 'simple'){
         $tinymce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if($this->getModule()->getParam(TextController::PARAM_FILES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->getModule()->getParam(TextController::PARAM_THEME, 'advanced') == 'full'){
         $tinymce->setTheme(TinyMce::TINY_THEME_FULL);
      }
      
		//NOTE soubory
      if($this->getModule()->getParam(TextController::PARAM_FILES, true)){
         $eplFiles = $this->container()->getEplugin('files');
         $this->template()->addTpl($eplFiles->getTpl(), true);
         $eplFiles->assignToTpl($this->template());
         $tinymce->setImagesList($eplFiles->getImagesListLink());
         $tinymce->setLinksList($eplFiles->getLinksListLink());
      }

      $this->template()->addJsPlugin($tinymce);
      if((bool)$this->getModule()->getParam(TextController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
         $this->template()->addVar('LIGHTBOX', true);
      }
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět'));;
	}
	// EOF edittextView
}

?>