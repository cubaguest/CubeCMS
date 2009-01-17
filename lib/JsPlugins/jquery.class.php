<?php
/**
 * Třída JsPluginu TabContent.
 * Třída slouží pro práci se záložkovým menu (tj. boxy se záložkovým 
 * přepínáním obsahu). Je úzce zpata z šablonou.
 * //TODO dodělat tvorbu scriptu pro spuštění, tak aby se dal vložit přímo do šablony a ghenerován byl zde.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form.class.php 434 2008-12-30 00:35:31Z jakub $ VVE3.3.0 $Revision: 434 $
 * @author      $Author: $ $Date:  $
 *              $LastChangedBy:  $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu pro záložkový box
 */

class JQuery extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("jQuery");
	}
	protected function initFiles() {
		//$this->addCssFile('tabcontent.css');
		
//		Přidání js soubrů pluginu
		$this->addJsFile(new JsPluginJsFile("jquery.js"));
	}
	
	/**
	 * Metda vytvoří výchozí konfigurační soubor
	 */
	protected function generateFile() {
	}

  /**
   * Metody pro přidávání částí jQuery UI, effects
   */

  /**
   * Metoda přidá jádro pro efekty UI
   */
  public function addUICore() {
    $this->addJsFile(new JsPluginJsFile("jquery-ui-core.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - draggable (přesunování)
   */
  public function addUIDraggable() {
    //deps
    $this->addUICore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-draggable.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - droppable (odstraňování)
   */
  public function addUIDroppable() {
    //deps
    $this->addUICore();
    $this->addUIDraggable();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-droppable.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - resizable (zěna velikosti)
   */
  public function addUIResizable() {
    //deps
    $this->addUICore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-resizable.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - selectable (označování)
   */
  public function addUISelectable() {
    //deps
    $this->addUICore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-selectable.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - sortable (řazení)
   */
  public function addUISortable() {
    //deps
    $this->addUICore();
    $this->addUIDraggable();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-sortable.packed.js"));
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

    $this->addJsFile(new JsPluginJsFile("jquery-ui-accordion.packed.js"));
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

    $this->addJsFile(new JsPluginJsFile("jquery-ui-dialog.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - slider (posunovač)
   */
  public function addWidgentSlider() {
    //deps
    $this->addUICore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-slider.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - tabs (tabulka boxů - záložky)
   */
  public function addWidgentTabs() {
    //deps
    $this->addUICore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-tabs.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - datepicker (box s výběrem data)
   */
  public function addWidgentDatepicker() {
    //deps
    /**
     * @todo zkontrolovat závislos s obrázky umístěnými ve složce pluginu
     */
    $this->addJsFile(new JsPluginJsFile("jquery-ui-datepicker.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá widgent UI - progressbar (pregress bar)
   */
  public function addWidgentProgressBar() {
    //deps
    $this->addUICore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-progressbar.packed.js"));
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

    $this->addJsFile(new JsPluginJsFile("jquery-ui-effect-core.packed.js"));
    return $this;
  }

  /**
   * Metoda přidá efekty UI - all (všechny efekty)
   */
  public function addEffectAll() {
    //deps
    $this->addEffectCore();

    $this->addJsFile(new JsPluginJsFile("jquery-ui-effect-all.packed.js"));
    return $this;
  }



}

?>