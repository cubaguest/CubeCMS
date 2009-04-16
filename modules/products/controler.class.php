<?php
class ProductsController extends Controller {
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
      //		Kontrola práv
      $this->checkReadableRights();

      //		Vytvoření modelu
      $productsModel = new ProductsListModel();
      //		Scrolovátka
      $scroll = new ScrollEplugin();
      $scroll->setCountRecordsOnPage($this->getModule()->getParam(self::PARAM_NUM_PRODUCTS_ON_PAGE, 10));
      $scroll->setCountAllRecords($productsModel->getCountProducts());

      //		Vybrání článků
      $productsArray = $productsModel->getSelectedListProducts($scroll->getStartRecord(), $scroll->getCountRecords());

      //		Přidání linku pro editaci a jestli se dá editovat
      if(!empty ($productsArray)){
         foreach ($productsArray as $key => $product) {
            //			Link pro zobrazení
            $productsArray[$key][self::PRODUCT_SHOW_LINK] = $this->getLink()
            ->article($product[ProductDetailModel::COLUMN_PRODUCT_LABEL],
               $product[ProductDetailModel::COLUMN_PRODUCT_ID]);
         }
      }

      //		Přenos do viewru
      $this->container()->addEplugin('scroll',$scroll);

      //		Link pro přidání
      if($this->getRights()->isWritable()){
         $this->container()->addLink('LINK_ADD_PRODUCT',$this->getLink()
            ->action($this->getAction()->addProduct()));
      }
      // předání dat
      $this->container()->addData('PRODUCT_LIST_ARRAY', $productsArray);
      $this->container()->addData('IMAGES_PATH', $this->getModule()->getDir()->getDataDir(true));
   }

   public function showController(){
      $articleDetail = new ProductDetailModel();
      $articleArr = $articleDetail->getArticleDetailSelLang($this->getArticle()->getArticle());

      //      obsluha Mazání novinky
      if(($this->getRights()->isWritable() AND $articleDetail->getIdUser()
            == $this->getRights()->getAuth()->getUserId()) OR
         $this->getRights()->isControll()){
         
      }

      $this->container()->addData('ARTICLE', $articleArr);
      $this->container()->addData('ARTICLE_LABEL', $articleArr[ProductDetailModel::COLUMN_PRODUCT_LABEL]);

      if($this->getRights()->isControll() OR
         ($this->getRights()->isWritable() AND $this->getModule()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == false) OR
         ($this->getRights()->isWritable() AND $this->getModule()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == true AND
            $articleDetail->getIdUser() == $this->getRights()->getAuth()->getUserId())){

         $form = new Form(self::FORM_PREFIX);
         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $files = new UserFilesEplugin($this->getRights());
            $files->deleteAllFiles($articleArr[ProductDetailModel::COLUMN_PRODUCT_ID]);
            $images = new UserImagesEplugin($this->getRights());
            $images->deleteAllImages($articleArr[ProductDetailModel::COLUMN_PRODUCT_ID]);
            if(!$articleDetail->deleteArticle($form->getValue(self::FORM_INPUT_ID))){
               throw new UnexpectedValueException(_m('Článek se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage(_m('Článek byl smazán'));
            $this->getLink()->article()->action()->rmParam()->reload();
         }

         $this->container()->addLink('EDIT_LINK', $this->getLink()->action($this->getAction()->editArticle()));
         $this->container()->addData('EDITABLE', true);
      }

      if($this->getRights()->isWritable()){
         $this->container()->addLink('ADD_LINK',$this->getLink()->action($this->getAction()->addArticle())->article());
         $this->container()->addData('WRITABLE', true);
      }
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

   /**
   * Kontroler pro přidání produktu
   */
   public function addproductController(){
      $this->checkWritebleRights();
      if($this->getModule()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new UserFilesEplugin($this->getRights());
         $files->setIdArticle($this->getRights()->getAuth()->getUserId()*(-1));
         $this->container()->addEplugin('files', $files);
      }

      $productForm = new Form();
      $productForm->setPrefix(self::FORM_PREFIX);

      $productForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crInputFile(self::FORM_INPUT_IMAGE, true)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($productForm->checkForm()){
         $file = $productForm->getValue(self::FORM_INPUT_IMAGE);
         $image = new ImageFile($file);
         if($image->isImage()){
            try {
               $image->saveImage($this->getModule()->getDir()->getDataDir());


               $productDetail = new ProductDetailModel();
               if(!$productDetail->saveNewProduct($productForm->getValue(self::FORM_INPUT_LABEL),
                     $productForm->getValue(self::FORM_INPUT_TEXT),
                     $image->getName(),
                     $this->getRights()->getAuth()->getUserId())){
                  throw new UnexpectedValueException(_m('Produkt se nepodařilo uložit, chyba při ukládání.'), 1);
               }
               if(isset ($files)){
                  $files->renameIdArticle($this->getRights()->getAuth()->getUserId()*(-1),
                     $productDetail->getLastInsertedId());
               }
               $this->infoMsg()->addMessage(_m('Produkt byl uložen'));
               $this->getLink()->article()->action()->rmParam()->reload();
            } catch (Exception $e) {
               new CoreErrors($e);
            }

         }
      }

      $this->container()->addData('PRODUCT_DATA', $productForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $productForm->getErrorItems());
      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * controller pro úpravu novinky
   */
   public function editArticleController() {
      $this->checkWritebleRights();

      $ardicleEditForm = new Form(self::FORM_PREFIX);

      $ardicleEditForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //      Načtení hodnot prvků
      $articleModel = new ProductDetailModel();
      $articleModel->getArticleDetailAllLangs($this->getArticle());
      //      Nastavení hodnot prvků
      $ardicleEditForm->setValue(self::FORM_INPUT_LABEL, $articleModel->getLabelsLangs());
      $ardicleEditForm->setValue(self::FORM_INPUT_TEXT, $articleModel->getTextsLangs());
      $label = $articleModel->getLabelsLangs();
      
      $this->container()->addData('ARTICLE_NAME', $label[Locale::getLang()]);

      if($this->getModule()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new UserFilesEplugin($this->getRights());
         $files->setIdArticle($articleModel->getId());
         $this->container()->addEplugin('files', $files);
      }

      //        Pokud byl odeslán formulář
      if($ardicleEditForm->checkForm()){
         if(!$articleModel->saveEditArticle($ardicleEditForm->getValue(self::FORM_INPUT_LABEL),
               $ardicleEditForm->getValue(self::FORM_INPUT_TEXT), $this->getArticle())){
            throw new UnexpectedValueException(_m('Článek se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage(_m('Článek byl uložen'));
         $this->getLink()->action()->reload();
      }

      //    Data do šablony
      $this->container()->addData('ARTICLE_DATA', $ardicleEditForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $ardicleEditForm->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }
}
?>