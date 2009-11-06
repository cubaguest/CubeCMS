<?php
/**
 * Třída JsPluginu TabContent.
 * Třída slouží pro práci se záložkovým menu (tj. boxy se záložkovým 
 * přepínáním obsahu). Je úzce zpata z šablonou.
 * //TODO dodělat tvorbu scriptu pro spuštění, tak aby se dal vložit přímo do šablony a ghenerován byl zde.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id$ VVE3.3.0 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída JsPluginu pro záložkový box
 * @link          http://jquery.com/
 */

class JsPlugin_JQuery extends JsPlugin {
	protected function initJsPlugin() {}
   
	protected function setFiles() {
//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery-1.3.2.min.js"));
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
   */
  public function addUICore() {
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.UICore.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - draggable (přesunování)
   */
  public function addUIDraggable() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Draggable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - droppable (odstraňování)
   */
  public function addUIDroppable() {
    //deps
    $this->addUICore();
    $this->addUIDraggable();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Droppable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - resizable (zěna velikosti)
   */
  public function addUIResizable() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Resizable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - selectable (označování)
   */
  public function addUISelectable() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Selectable.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - sortable (řazení)
   */
  public function addUISortable() {
    //deps
    $this->addUICore();
    $this->addUIDraggable();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Sortable.min.js"));
    return $this;
  }

  /*
   * Widgents
   */

  /**
   * Metoda přidá widgent UI - accordion (roztahování boxů)
   */
  public function addWidgentAccordion() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Accordion.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - dialog (box dialogu)
   */
  public function addWidgentDialog() {
    //deps
    $this->addUICore();
    $this->addUIDraggable();
    $this->addUIResizable();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Dialog.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - slider (posunovač)
   */
  public function addWidgentSlider() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Slider.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - tabs (tabulka boxů - záložky)
   */
  public function addWidgentTabs() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Tabs.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - datepicker (box s výběrem data)
   */
  public function addWidgentDatepicker() {
    //deps
    $this->addUICore();
    /**
     * @todo zkontrolovat závislos s obrázky umístěnými ve složce pluginu
     */
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Datepicker.min.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - progressbar (pregress bar)
   */
  public function addWidgentProgressBar() {
    //deps
    $this->addUICore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.Progressbar.min.js"));
    return $this;
  }

  /*
   * Efekty
   */

  /**
   * Metoda přidá efekt UI - core (jádro efektů)
   */
  public function addEffectCore() {
    //deps
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.EffectsCore.min.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - all (všechny efekty)
   */
  public function addEffectAll() {
    //deps
    $this->addEffectCore();
    $this->addFile(new JsPlugin_JsFile("jquery-ui-1.7.EffectAll.min.js"));
    return $this;
  }

  /*
   * PLUGINS
   */

  /**
   * Metoda přidá efekty UI - all (všechny efekty)
   */
  public function addPluginCookie() {
    $this->addFile(new JsPlugin_JsFile("jquery-cookie.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - all (všechny efekty)
   */
  public function addPluginAjaxUploadFile() {
    $this->addFile(new JsPlugin_JsFile("ajaxupload.2.8.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - all (všechny efekty)
   */
  public function addPluginOpacityRollOver() {
    $this->addFile(new JsPlugin_JsFile("jquery.opacityrollover.js"));
    return $this;
  }
}
?>