<?php
/**
 * Třída JsPluginu JQuery a jeho komponent.
 * Třída slouží pro javascriptů JQuery
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id$ VVE3.3.0 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída JsPluginu pro JQuery
 * @link          http://jquery.com/
 */

class JsPlugin_JQuery extends JsPlugin {
   const BASE_THEME_DIR = 'base';
   const JQUERY_VERSION = '1.4.2';
   const JQUERY_UI_VERSION = '1.8.2';

   /**
    * Pole s konfigurací pluginu
    * @var array
    */
   protected $config = array('theme' => self::BASE_THEME_DIR);

	protected function initJsPlugin() {}

	protected function setFiles() {
//		Přidání js soubrů pluginu
      if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1){
         $this->addFile("http://ajax.googleapis.com/ajax/libs/jquery/".self::JQUERY_VERSION."/jquery.min.js");
      } else {
         $this->addFile(new JsPlugin_JsFile("jquery-".self::JQUERY_VERSION.".min.js"));
      }
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	protected function generateFile(JsPlugin_JsFile $file) {}

  /**
   * Metody pro přidávání částí jQuery UI, effects
   */

  /**
   * Metoda přidá jádro pro efekty UI
   * @return JsPlugin_JQuery
   */
  public function addUICore() {
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1){
       $this->addFile("http://ajax.googleapis.com/ajax/libs/jqueryui/".self::JQUERY_UI_VERSION."/jquery-ui.min.js");
    } else {
       $this->addFile(new JsPlugin_JsFile("jquery.ui.core.min.js"));
    }
    $this->addFile(new JsPlugin_CssFile("jquery.ui.core.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.theme.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - widget (základní pro dialog a ostatní věci)
   * @return JsPlugin_JQuery
   */
  public function addUIWidget() {
    //deps
    $this->addUICore();
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addFile(new JsPlugin_JsFile("jquery.ui.widget.min.js"));
    return $this;
  }

  /**
   * Metoda přidá část UI - mouse (pro práci s myší)
   * @return JsPlugin_JQuery
   */
  public function addUIMouse() {
    //deps
    $this->addUICore();
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.mouse.min.js"));
    return $this;
  }

  /**
   * Metoda přidá část UI - position (pro práci s pozicemi boxů)
   * @return JsPlugin_JQuery
   */
  public function addUIPosition() {
     if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addFile(new JsPlugin_JsFile("jquery.ui.position.min.js"));
    return $this;
  }


  /**
   * Metoda přidá efekty UI - draggable (přesunování)
   * @return JsPlugin_JQuery
   */
  public function addUIDraggable() {
    //deps
    $this->addUICore();
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addUIWidget();
    $this->addUIMouse();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.draggable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - droppable (odstraňování)
   * @return JsPlugin_JQuery
   */
  public function addUIDroppable() {
    //deps
    $this->addUICore();
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addUIDraggable();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.droppable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - resizable (zěna velikosti)
   * @return JsPlugin_JQuery
   */
  public function addUIResizable() {
    //deps
    $this->addUICore();
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addUIMouse();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.resizable.min.js"));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.resizable.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - selectable (označování)
   * @return JsPlugin_JQuery
   */
  public function addUISelectable() {
    //deps
    $this->addUICore();
    if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) return;
    $this->addUIMouse();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.selectable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - sortable (řazení)
   * @return JsPlugin_JQuery
   */
  public function addUISortable() {
    //deps
    $this->addUICore();
    $this->addUIMouse();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.sortable.min.js"));
    return $this;
  }

  /*
   * Widgets
   */

  /**
   * Metoda přidá widgent UI - accordion (roztahování boxů)
   * @return JsPlugin_JQuery
   */
  public function addUIAccordion() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.accordion.min.js"));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.accordion.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }


  /**
   * Metoda přidá widgent UI - button
   * @return JsPlugin_JQuery
   */
  public function addUIButton() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.button.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - autocomplete
   * @return JsPlugin_JQuery
   */
  public function addUIAutoComplete() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addUIPosition();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.autocomplete.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widget UI - dialog (box dialogu)
   * @return JsPlugin_JQuery
   */
  public function addUIDialog() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addUIPosition();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.dialog.min.js"));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.dialog.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /**
   * Metoda přidá widget UI - slider (posunovač)
   * @return JsPlugin_JQuery
   */
  public function addUISlider() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.slider.min.js"));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.slider.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - tabs (tabulka boxů - záložky)
   * @return JsPlugin_JQuery
   */
  public function addUITabs() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.tabs.min.js"));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.tabs.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - datepicker (box s výběrem data)
   * @return JsPlugin_JQuery
   */
  public function addUIDatepicker() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    /**
     * @todo zkontrolovat závislos s obrázky umístěnými ve složce pluginu
     */
    $this->addFile(new JsPlugin_JsFile("jquery.ui.datepicker.min.js"));
    $this->addFile(new JsPlugin_JsFile("jquery.ui.datepicker-".Locales::getLang().".js",false,'ui/i18n/'));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.datepicker.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - progressbar (pregress bar)
   * @return JsPlugin_JQuery
   */
  public function addUIProgressBar() {
    //deps
    $this->addUICore();
    $this->addUIWidget();
    $this->addFile(new JsPlugin_JsFile("jquery.ui.progressbar.min.js"));
    $this->addFile(new JsPlugin_CssFile("jquery.ui.progressbar.css",false,'ui/themes/'.$this->getCfgParam('theme').URL_SEPARATOR));
    return $this;
  }

  /*
   * Efekty
   */

  /**
   * Metoda přidá efekt UI - core (jádro efektů)
   * @return JsPlugin_JQuery
   */
  public function addUIEffectCore() {
    $this->addFile(new JsPlugin_JsFile("jquery.effects.core.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - vybraný efekt
   * @param string $efectName -- název efektu
   * @return JsPlugin_JQuery
   */
  public function addUIEffect($efectName) {
     $this->addUIEffectCore();
    $this->addFile(new JsPlugin_JsFile("jquery.effects.".$efectName.".min.js"));
    return $this;
  }

  /*
   * PLUGINS
   */

  /**
   * Metoda přidá plugin pro práci s cookie
   * @return JsPlugin_JQuery
   */
  public function addPluginCookie() {
    $this->addFile(new JsPlugin_JsFile("jquery.cookie.min.js"));
    return $this;
  }

  /**
   * Metoda přidá plugin pro práci s metadaty
   * @return JsPlugin_JQuery
   */
  public function addPluginMetadata() {
    $this->addFile(new JsPlugin_JsFile("jquery.metadata.min.js"));
    return $this;
  }

  /**
   * Metoda přidá plugin pro ajax upload souborů
   * @return JsPlugin_JQuery
   */
  public function addPluginAjaxUploadFile() {
    $this->addFile(new JsPlugin_JsFile("ajaxupload.2.8.js"));
    return $this;
  }

  /**
   * Metoda přidá plugin OpacityRollover
   * @return JsPlugin_JQuery
   */
  public function addPluginOpacityRollOver() {
    $this->addFile(new JsPlugin_JsFile("jquery.opacityrollover.js"));
    return $this;
  }
}
?>