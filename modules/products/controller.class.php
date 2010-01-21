<?php
class Products_Controller extends Controller {
   /**
    * Název parametru s počtem článků na stránce
    */
   const PARAM_NUM_PRODUCTS_ON_PAGE = 'scroll';

   /**
    * Parametr jestli se používají soubory
    */
   const PARAM_FILES = 'files';

  /**
   * Speciální imageinární sloupce
   * @var string
   */
   const PRODUCT_EDITABLE = 'editable';
   const PRODUCT_EDIT_LINK = 'editlink';
   const PRODUCT_SHOW_LINK = 'showlink';

  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'product_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_LABEL = 'label';
   const FORM_INPUT_TEXT = 'text';
   const FORM_INPUT_IMAGE = 'image';

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      $this->checkReadableRights();
   }

   public function showController(){
      $this->checkReadableRights();

      $productDetail = new Products_Model_Detail($this->sys());
      $this->view()->product = $productDetail->getProductDetailSelLang($this->sys()->article()->getArticle());

      if($this->rights()->isControll() OR
         ($this->rights()->isWritable() AND $this->module()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == false) OR
         ($this->rights()->isWritable() AND $this->module()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == true AND
            $productDetail->getIdUser() == $this->rights()->getAuth()->getUserId())){

         $form = new Form(self::FORM_PREFIX);
         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $files = new Eplugin_UserFiles($this->sys());
            $files->deleteAllFiles($this->view()->product[Products_Model_Detail::COLUMN_PRODUCT_ID]);
            if(!$productDetail->deleteProduct($form->getValue(self::FORM_INPUT_ID))){
               throw new UnexpectedValueException($this->_m('Produkt se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage($this->_m('Produkt byl smazán'));
            $this->link()->article()->action()->rmParam()->reload();
         }
      }
   }

   /**
   * Kontroler pro přidání produktu
   */
   public function addProductController(){
      $this->checkWritebleRights();
      // Uživatelské soubory
      $files = new Eplugin_UserFiles($this->sys());
      $files->setIdArticle($this->rights()->getAuth()->getUserId()*(-1));
      $this->view()->EPLfiles = $files;

      $productForm = new Form();
      $productForm->setPrefix(self::FORM_PREFIX);

      $productForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($productForm->checkForm()){
         $productModel = new Products_Model_Detail($this->sys());
         if(!$productModel->saveNewProduct($productForm->getValue(self::FORM_INPUT_LABEL),
               $productForm->getValue(self::FORM_INPUT_TEXT),
               $this->rights()->getAuth()->getUserId())){
            throw new UnexpectedValueException($this->_('Produkt se nepodařilo uložit, chyba při ukládání.'), 1);
         }
         if(isset ($files)){
            $files->renameIdArticle($this->rights()->getAuth()->getUserId()*(-1),
               $productModel->getLastInsertedId());
         }
         $this->infoMsg()->addMessage($this->_('Produkt byl uložen'));
         $this->link()->article()->action()->rmParam()->reload();
      }

      $this->view()->errorItems = $productForm->getErrorItems();
   }

  /**
   * controller pro úpravu produktu
   */
   public function editProductController() {
      $this->checkWritebleRights();

      $productEditForm = new Form(self::FORM_PREFIX);

      $productEditForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      // Uživatelské soubory
      $files = new Eplugin_UserFiles($this->sys());
      $this->view()->EPLfiles = $files;

      //        Pokud byl odeslán formulář
      if($productEditForm->checkForm()){
         $productModel = new Products_Model_Detail($this->sys());
         if(!$productModel->saveEditProduct($productEditForm->getValue(self::FORM_INPUT_LABEL),
               $productEditForm->getValue(self::FORM_INPUT_TEXT), $this->getArticle())){
            throw new UnexpectedValueException($this->_m('Produkt se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage($this->_m('Produkt byl uložen'));
         $this->link()->action()->reload();
      }

      //    Data do šablony
      $this->view()->errorItems = $productEditForm->getErrorItems();
   }
}
?>