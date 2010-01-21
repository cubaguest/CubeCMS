<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Orderform_View extends View {
 public function mainView() {
      $this->template()->addTplFile("shopbasket.phtml");
      $this->template()->addTplFile("products.phtml");
      $this->template()->addJsFile("basket.js");

      //vytvoření modelu pro načtení poboček z db
      $citiesM = new Orderform_Model_Contactservices($this->sys());
      $this->template()->mesta = $citiesM->getCityList();
      $this->template()->addTplFile("contactservices.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setArticleName($this->_("objednávkový formulář"));

      $jQuery = new JsPlugin_JQuery();
      $jQuery->addWidgentTabs();
      $this->template()->addJsPlugin($jQuery);

      // načtení prvků a barev
      $itemM = new Orderform_Model_Items($this->sys());
      $this->template()->products = $itemM->getItems();
      $this->template()->profileWindow = $itemM->getWindowProfiles();
      $this->template()->profileDoor = $itemM->getDoorProfiles();
      $this->template()->grids = $itemM->getGrids();

      // link na ajax
      $this->template()->ajaxLink = new Ajax_Link($this->sys()->module());

      // šablona pro prvky v košíku
      $itemTpl = new Template($this->sys());
      $itemTpl->addTplFile("item.phtml");
      $this->template()->itemTpl = $itemTpl;
	}

   public function basketAddItemAjaxView() {
      $this->template()->addTplFile("item.phtml");
      $this->template()->hideDelete = false;
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
      $this->template()->setArticleName($this->_("objednávkový formulář"));
      $this->template()->setActionName($this->_("potvrzení"));

      // načtení služeb
      $servicesM = new Orderform_Model_Contactservices($this->sys());
      $this->template()->services = $servicesM->getServices();

      $officeNM = new Contacts_Model_Detail($this->sys());
      $this->template()->officeName = $officeNM->getContactDetail($this->template()->buyDetails['office']);

      $officeM = new Contacts_Model_EntryList($this->sys());
      $this->template()->office = $officeM->getList($this->template()->buyDetails['office']);
   }
}

?>