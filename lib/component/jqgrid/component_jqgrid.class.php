<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_JqGrid extends Component {
   private $request = null;

   private $respond = null;


   public function  __construct() {
      $this->request = new Component_JqGrid_Request();
      $this->respond = new Component_JqGrid_Respond();
      $this->respond()->setPage($this->request()->page);
      $this->respond()->setRecordsOnPage($this->request()->rows);
   }

   /**
    * Metodfa vrací objekt odpovědi
    * @return Component_JqGrid_Respond
    */
   public function respond() {
      return $this->respond;
   }

   /**
    * Metodfa vrací objekt požadavku
    * @return Component_JqGrid_Request
    */
   public function request() {
      return $this->request;
   }

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {
      $this->template()->addJsPlugin(new Component_JqGrid_JsPlugin());
   }
}
?>
