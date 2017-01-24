<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopProductGeneralAdmin_Controller extends Shop_Product_Controller {

   protected function init()
   {
      parent::init();
      $this->checkControllRights();
   }
   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {

      $this->checkListAction();
      //		Kontrola práv
      $this->loadProducts(
         $this->getRequestParam('num', $this->category()->getParam('scroll', 20)),
         $this->getRequestParam('sort'),
         $this->getRequestParam('idc'), true);

      $idp = $this->getRequestParam('idp', false);
      $action = $this->getRequestParam('action', false);
      if($idp && $action == 'duplicate'){
         $this->processDuplicateProduct((int)$idp);
         $this->infoMsg()->addMessage($this->tr('Zboží bylo duplikováno'));
         $this->link()->rmParam(array('action', 'idp'))->redirect();
      } else if($idp && $action == 'delete'){
         $this->processDeleteProduct((int)$idp);
         $this->infoMsg()->addMessage($this->tr('Zboží bylo smazáno'));
         $this->link()->rmParam(array('action', 'idp'))->redirect();
      } else if($idp && $action == 'changeState'){
         $model = new Shop_Model_Product();
         $product = $model->record($idp);
         $this->processChangeProductState($product, !$product->{Shop_Model_Product::COLUMN_ACTIVE});
         $this->infoMsg()->addMessage($this->tr('Stav byl změněn'));
         $this->link()->rmParam(array('action', 'idp'))->redirect();
      }

      // načtení kateogrií
      $modelCats = new Model_Category();
      $modelCats
         ->columns(array(Model_Category::COLUMN_NAME, 'products' => 'COUNT('.Shop_Model_Product::COLUMN_ID.')'))
         ->join(Model_Category::COLUMN_ID, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID_CATEGORY, array(Shop_Model_Product::COLUMN_ID))
         ->where(Model_Category::COLUMN_MODULE." = :module", array('module' => 'shopproductgeneral'))
         ->groupBy(Shop_Model_Product::COLUMN_ID_CATEGORY);

      $this->view()->catsList = $modelCats->records();

   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController($urlkey) {
      $this->checkWritebleRights();
      $this->editProduct((string)$urlkey);
      if($this->view()->product == false OR $this->view()->product == null){
         return false;
      }
   }
   
   public function editImagesController($urlkey) {
      $this->checkWritebleRights();
      $this->editProductImages((string)$urlkey);
      if($this->view()->product == false OR $this->view()->product == null){
         return false;
      }
   }

   
   public function editParamsController($urlkey) {
      $this->checkWritebleRights();
      $this->editProductParams((string)$urlkey);
      if($this->view()->product == false OR $this->view()->product == null){
         return false;
      }
   }
   
   protected function editCompleteCallback(Model_ORM_Record $product)
   {
      $this->infoMsg()->addMessage($this->tr('Zboží bylo uloženo'));
      $this->link()->route()->redirect();
   }
   
   protected function editCancelCallback($isEdit = true)
   {
      $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
      if($isEdit){
         $this->link()->route()->reload();
      } else {
         $this->link()->route()->reload();
      }
   }

   protected function checkListAction()
   {
      if(!isset($_POST['list-action']) || !isset($_POST['product']) || empty($_POST['product'])){
         return;
      }

      $action = $_POST['list-action'];
      $products = $_POST['product'];
      $model = new Shop_Model_Product();

      $whereProductId = array();
      foreach ($products as $id => $val) {
         if($val == 'on') {
            $whereProductId[':pid_'.$id] = $id;
         }
      }

      if($action == 'activate' || $action == 'deactivate'){
         $model
            ->where(Shop_Model_Product::COLUMN_ID." IN (".implode(',', array_keys($whereProductId)).")", $whereProductId)
            ->update(array(Shop_Model_Product::COLUMN_ACTIVE => ($action == 'activate') ));
      } else if($action == 'delete'){
         /**
          * Tohle nejde udělat najednou, protože se musí přepočítat relace a pořadí
          */
         foreach ($products as $id => $val) {
            if($val == 'on') {
               $model->delete((int)$id);
            }
         }
      } else if(strpos($action, 'move_to_') !== false){
         $idCat = (int)str_replace('move_to_', null, $action);
         $model
            ->where(Shop_Model_Product::COLUMN_ID." IN (".implode(',', array_keys($whereProductId)).")", $whereProductId)
            ->update(array(Shop_Model_Product::COLUMN_ID_CATEGORY => $idCat ));
      } else {
         throw new UnexpectedValueException($this->tr('Předané operace zboží neexistuje'));
         return;
      }

      $this->infoMsg()->addMessage($this->tr('Zboží bylo upraveno'));
      $this->link()->redirect();
   }

   public function addController() {
      $this->checkWritebleRights();
      $this->editProduct();
   }
   
   public function detailController()
   {
      //		Kontrola práv
      $product = $this->loadCategoryProduct();
      
      if($product == false){
         return false;
      }

      if( VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK ||
         ($product->{Shop_Model_Product::COLUMN_QUANTITY} > 0 && !VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK )
         || !$product->{Shop_Model_Product::COLUMN_STOCK}){
         $this->createAddToCartForm($product->getPK(), true);
      }
      $this->processDeleteProduct($product);
      $this->processDuplicateProduct($product);
      $this->processChangeProductState($product);
      $this->view()->linkBack = $this->link()->route();

      $this->view()->product = $product;
   }

   public function editVariantsController($urlkey)
   {
      $this->checkWritebleRights();
      $this->editProductVariants((string)$urlkey);
      if($this->view()->product == false OR $this->view()->product == null){
         return false;
      }
   }

//   protected function checkChangeState()
//   {
//      if($this->getRequestParam('id', false) !== false && $this->getRequestParam('changeState', false) !== false){
//         (new Shop_Model_Product())->where(Shop_Model_Product::COLUMN_ID." = :idp", array('idp' => $this->getRequestParam('id')))
//            ->update(array(Shop_Model_Product::COLUMN_ACTIVE => (bool)$this->getRequestParam('changeState')));
//         $this->link()->rmParam('id')->rmParam('changeState')->redirect();
//      }
//
//   }

   protected function loadProducts($productsOnPage = 0, $sort = 'p', $idCategory = 0, $joinCategory = false)
   {
      $model = new Shop_Model_Product();
      $model->setSelectAllLangs(false);
      $model
         ->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
         ->join(Shop_Model_Product::COLUMN_ID, 'Shop_Model_Product_Combinations', Shop_Model_Product_Combinations::COLUMN_ID_PRODUCT,
         array(
            'priceMin' => '(MIN(`'.Shop_Model_Product_Combinations::COLUMN_PRICE.'`) + `'.Shop_Model_Product::COLUMN_PRICE.'`)',
            'priceMax' => '(MAX(`'.Shop_Model_Product_Combinations::COLUMN_PRICE.'`) + `'.Shop_Model_Product::COLUMN_PRICE.'`)',
//            'quantity' => 'SUM(`'.Shop_Model_Product_Combinations::COLUMN_QTY.'`) + `'.Shop_Model_Product::COLUMN_QUANTITY.'`',
            'quantity' => 'SUM(`'.Shop_Model_Product_Combinations::COLUMN_QTY.'`)',
         ))
         ->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY, array(
            Model_Category::COLUMN_NAME, Model_Category::COLUMN_MODULE, Model_Category::COLUMN_URLKEY,
         ))
      ;

      if($idCategory != 0){
         $model->where( Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc ', array('idc' => $idCategory) );
      } else {
//         $model->where(
//               // .'('.Shop_Model_Product_Combinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_Product_Combinations::COLUMN_IS_DEFAULT.' IS NULL )'
//               // .'AND '
//               Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL ',
//            array()
//         );
      }
      $model->groupBy(Shop_Model_Product::COLUMN_ID);

      // řazení
      $sortTypes = self::getSortTypes();
      if(isset ($sortTypes[$sort])){
         $model->order(array($sortTypes[$sort]['column'] => $sortTypes[$sort]['sort']));
      }

      $scrollComponent = null;
      if($productsOnPage != 0){
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, $productsOnPage);
      }

      if($scrollComponent instanceof Component_Scroll){
         $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }
      $this->view()->scrollComp = $scrollComponent;
//      Debug::log($model->getSQLQuery());
      $this->view()->products = $model->records();
   }

}
