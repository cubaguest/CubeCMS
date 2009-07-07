<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Orderform_View extends View {
 public function mainView() {
      $this->template()->addTplFile("shopbasket.phtml");
      $this->template()->addTplFile("products.phtml");

      //vytvoření modelu pro načtení poboček z db
      $citiesM = new Orderform_Model_Contactservices($this->sys());
      $this->template()->mesta = $citiesM->getCityList();
      $this->template()->addTplFile("contactservices.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setActionTitle($this->_("objednávkový formulář"));

      $jQuery = new JsPlugin_JQuery();
      $jQuery->addWidgentTabs();
      $this->template()->addJsPlugin($jQuery);

      // načtení prvků a barev
      $itemM = new Orderform_Model_Items($this->sys());
      $this->template()->products = $itemM->getItems();
      $this->template()->productColors = $itemM->getColors();

      // link na ajax
      $this->template()->ajaxLink = new Ajax_Link($this->sys()->module());

      // šablona pro prvky v košíku
      $itemTpl = new Template($this->sys());
      $itemTpl->addTplFile("item.phtml");
      $this->template()->itemTpl = $itemTpl;
	}

   public function basketAddItemAjaxView() {
      $this->template()->addTplFile("item.phtml");
   }

   /**
    * Viewer pro potvrzení formuláře
    */
   public function confirmOrderView() {
      $this->template()->addTplFile("confirm.phtml");
      $this->template()->addCssFile("style.css");

      // šablona pro prvky v košíku
      $itemTpl = new Template($this->sys());
      $itemTpl->addTplFile("item.phtml");
      $this->template()->itemTpl = $itemTpl;

      // načtení služeb
      $servicesM = new Orderform_Model_Contactservices($this->sys());
      $this->template()->services = $servicesM->getServices();
   }
}

?>