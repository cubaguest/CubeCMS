<?php

/**
 * Třída shop_cart_item
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
abstract class Shop_Product_Controller extends Controller {

   const DIR_IMAGES = 'products';

   protected function init()
   {
      $this->category()->getModule()->setDataDir('shop');
      parent::init();
   }

   protected function createAddToCartForm()
   {
      $formAdd = new Form('product_add_');
      $formAdd->setProtected(false);
      
      $eQuantity = new Form_Element_Text('qty', $this->tr('Množství'));
      $eQuantity->addValidation(new Form_Validator_NotEmpty($this->tr('Musí být vyplněno pole s množstvím zboží')));
      $eQuantity->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $formAdd->addElement($eQuantity);
      
      $eProductId = new Form_Element_Hidden('productId');
      $eProductId->addValidation(new Form_Validator_NotEmpty($this->tr('Nebylo přeneseno ID zboží. Zkuste obnovit stránku')));
      $eProductId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $formAdd->addElement($eProductId);
      
      $eAddToCart = new Form_Element_Submit('add', $this->tr('Koupit'));
      $formAdd->addElement($eAddToCart);
      
      if($formAdd->isSend()){
         // kontrola velikosti položky
         $modelProduct = new Shop_Model_Product();
         try {
            $product = $modelProduct->record($formAdd->productId->getValues());
            if($product == false){
               $eProductId->isValid(false);
               throw new UnexpectedValueException('Nebylo předáno správné ID zboží');
            }
            if($eQuantity->getValues() < $product->{Shop_Model_Product::COLUMN_UNIT_SIZE}){
               $eQuantity->isValid(false);
               throw new UnexpectedValueException('Množství nemůže být měnší než nomnální hodnota');
            }
            
            if($eQuantity->getValues() % $product->{Shop_Model_Product::COLUMN_UNIT_SIZE} != 0){
               $eQuantity->isValid(false);
               throw new UnexpectedValueException('Množství musí být v násobcích nominální hodnoty');
            }
            
         } catch (UnexpectedValueException $exc) {
            $this->errMsg()->addMessage($exc->getMessage());
         }
      }

      if($formAdd->isValid()){
         $this->addToCart($formAdd->productId->getValues(), $formAdd->qty->getValues());
         $this->infoMsg()->addMessage($this->tr('Položka byla přidána do košíku'));
//         $this->link()->reload();
      }
      
      $this->view()->formAddToCart = $formAdd;
   }
   
   public static function getSortTypes()
   {
      $tr = new Translator();
      return array(
         'n' => array(
            'name' => $tr->tr('Názvu - vzestupně'),
            'column' => Shop_Model_Product::COLUMN_NAME,
            'sort' => Model_ORM::ORDER_ASC
            ),
         'nd' => array(
            'name' => $tr->tr('Názvu - sestupně'),
            'column' => Shop_Model_Product::COLUMN_NAME,
            'sort' => Model_ORM::ORDER_DESC
            ),
         'p' => array(
            'name' => $tr->tr('Ceny - vzestupně'),
            'column' => Shop_Model_Product::COLUMN_PRICE,
            'sort' => Model_ORM::ORDER_ASC
            ),
         'pd' => array(
            'name' => $tr->tr('Ceny - sestupně'),
            'column' => Shop_Model_Product::COLUMN_PRICE,
            'sort' => Model_ORM::ORDER_DESC
            ),
      );
   }


   protected function addToCart($idProduct, $qty, $attributes = array())
   {
      $cart = new Shop_Basket();
      $cart->addItem($idProduct, $qty);
   }

   protected function loadProducts($productsOnPage = 0, $sort = 'p')
   {
      $model = new Shop_Model_Product();
      $model->setSelectAllLangs(false);
      $model->joinFK(Shop_Model_Product::COLUMN_ID_TAX);
      
      $model->where(Shop_Model_Product::COLUMN_ACTIVE.' = 1 '
            .'AND '.Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc '
            .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL ',
            array('idc' => $this->category()->getId())
               );

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
      $this->view()->products = $model->records();
   }
   
   /**
    * Metoda načte produk podle url klíče
    */
   protected function loadProduct($urlKey = null)
   {
      if($urlKey == null){
         $urlKey = $this->getRequest('urlkey');
      }
      
      $model = new Shop_Model_Product();
      $model->setSelectAllLangs(false);
      $model->joinFK(Shop_Model_Product::COLUMN_ID_TAX);
      
      $model->where(Shop_Model_Product::COLUMN_ACTIVE.' = 1 '
            .'AND '.Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc '
            .'AND '.Shop_Model_Product::COLUMN_URLKEY.' = :ukey ',
            array('idc' => $this->category()->getId(), 'ukey' => $urlKey)
               );
      
      $this->view()->product = $model->record();
   }

   /**
    * Metoda vytvoří formulář produktu
    * @param Model_ORM_Record $product
    * @return Form 
    */
   protected function createForm(Model_ORM_Record $product)
   {
      $form = new Form('product_', true);
      
      $fGrpInfo = $form->addGroup('info', $this->tr('Základní informace'));
      
      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->setLangs();
      $eName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eName, $fGrpInfo);
      
      $eCode = new Form_Element_Text('code', $this->tr('Kód zboží'));
      $form->addElement($eCode, $fGrpInfo);
      
      $eWeight = new Form_Element_Text('weight', $this->tr('Hmotnost'));
      $eWeight->setSubLabel($this->tr('Váha v Kg'));
      $form->addElement($eWeight, $fGrpInfo);
      
      $eActive = new Form_Element_Checkbox('active', $this->tr('Zapnuto'));
      $eActive->setValues(true);
      $form->addElement($eActive, $fGrpInfo);
      
      $eIsNewDate = new Form_Element_Text('isNewDate', $this->tr('Novinka do'));
      $eIsNewDate->addValidation(new Form_Validator_Date());
      $eIsNewDate->addFilter(new Form_Filter_DateTimeObj());
      $eIsNewDate->setValues(vve_date("%x"));
      $eIsNewDate->setSubLabel($this->tr('Zboží bude označeno jako novinka do zadaného data.'));
      $form->addElement($eIsNewDate, $fGrpInfo);
      
      $eQuantity = new Form_Element_Text('quantity', $this->tr('Položek na skladě'));
      $eQuantity->addValidation(new Form_Validator_NotEmpty());
      $eQuantity->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $eQuantity->setSubLabel($this->tr('Počáteční počet položek na skladě. -1 pro "položky bez omezení množství". 0 pro "není skladem".'));
      $eQuantity->setValues(-1);
      $form->addElement($eQuantity, $fGrpInfo);
      
      $eImage = new Form_Element_File('image', $this->tr('Obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $eImage->setUploadDir(self::getImagesDir());
      $form->addElement($eImage, $fGrpInfo);
      
      if($product instanceof Model_ORM_Record AND $product->{Shop_Model_Product::COLUMN_IMAGE} != null){
         $eImageDel = new Form_Element_Checkbox('imageDel', $this->tr('Smazat obrázek'));
         $eImageDel->setSubLabel(sprintf($this->tr('Uložen obrázek: %s<br />%s')
            ,$product->{Shop_Model_Product::COLUMN_IMAGE}
            ,'<img src="'.$this->module()->getDataDir(true).self::DIR_IMAGES.'/small/'.$product->{Shop_Model_Product::COLUMN_IMAGE}.'" height="50" />'
            ));
         $form->addElement($eImageDel, $fGrpInfo);
      }
      
      $fGrpPrice = $form->addGroup('price', $this->tr('Ceny'));
      
      $ePrice = new Form_Element_Text('price', $this->tr('Cena bez daně'));
      $ePrice->addValidation(new Form_Validator_NotEmpty());
//      $ePrice->addValidation(new Form_Validator_IsNumber(null,Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($ePrice, $fGrpPrice);
      
      $eTax = new Form_Element_Select('tax', $this->tr('Daň'));
      
      //načtení daní
      $modelTax = new Shop_Model_Tax();
      $taxes = $modelTax->records();
      $this->view()->taxes = $taxes;
      foreach ($taxes as $tax) {
         $eTax->setOptions(array($tax->{Shop_Model_Tax::COLUMN_NAME} => $tax->{Shop_Model_Tax::COLUMN_ID}), true);
      }
      $form->addElement($eTax, $fGrpPrice);
      
      $ePriceWTax = new Form_Element_Text('pricewtax', $this->tr('Cena s daní'));
//      $ePriceWTax->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($ePriceWTax, $fGrpPrice);
      
      $eUnitSize = new Form_Element_Text('unitSize', $this->tr('Velikost jednotky'));
      $eUnitSize->addValidation(new Form_Validator_NotEmpty());
      $eUnitSize->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $eUnitSize->setValues(1);
      $eUnitSize->setSubLabel($this->tr('Velikost jednotky, v jaké se zboží prodává. Např: 1, 5, 100'));
      $form->addElement($eUnitSize, $fGrpPrice);
      
      $eUnit = new Form_Element_Text('unit', $this->tr('Jednotka'));
      $eUnit->addValidation(new Form_Validator_NotEmpty());
      $eUnit->setValues('Ks');
      $eUnit->setSubLabel($this->tr('Jednotka, v jaké se zboží prodává. Např: Ks(kusy), Kg(Kilogramy), g(gramy), m(metry),...'));
      $form->addElement($eUnit, $fGrpPrice);
      
      $fGrpLabels = $form->addGroup('labels', $this->tr('Popis zboží'));
      
      $eTextShort = new Form_Element_TextArea('textShort', $this->tr('Krátký popis'));
      $eTextShort->setLangs();
      $eTextShort->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eTextShort, $fGrpLabels);
      
      $eText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $eText->setLangs();
      $eText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eText, $fGrpLabels);
      
      
      $fGrpSeo = $form->addGroup('seo', $this->tr('SEO optimalizace'));
      
      $eUrlKey = new Form_Element_Text('urlkey', $this->tr('URL adresa'));
      $eUrlKey->setLangs();
      $eUrlKey->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($eUrlKey, $fGrpSeo);
      
      $eKeyWords = new Form_Element_Text('keywords', $this->tr('Klíčová slova'));
      $eKeyWords->setLangs();
      $form->addElement($eKeyWords, $fGrpSeo);
      
      
//      $eEditAttributes = new Form_Element_Checkbox('editAttr', $this->tr('Upravit atributy'));
//      $eEditAttributes->setValues(false);
//      $form->addElement($eEditAttributes);
      
      $eSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($eSubmit);
      
      // pokud je předán produkt, doplníme hodnoty
      if(!$product->isNew()){
         $form->name->setValues($product->{Shop_Model_Product::COLUMN_NAME});
         $form->code->setValues($product->{Shop_Model_Product::COLUMN_CODE});
         $form->weight->setValues($product->{Shop_Model_Product::COLUMN_WEIGHT});
         $form->active->setValues($product->{Shop_Model_Product::COLUMN_ACTIVE});
         $form->quantity->setValues($product->{Shop_Model_Product::COLUMN_QUANTITY});
         $form->price->setValues($product->{Shop_Model_Product::COLUMN_PRICE});
         $form->tax->setValues($product->{Shop_Model_Product::COLUMN_ID_TAX});
//         $form->pricewtax->setValues($product->{Shop_Model_Product::COLUMN_});
         $form->unitSize->setValues($product->{Shop_Model_Product::COLUMN_UNIT_SIZE});
         $form->unit->setValues($product->{Shop_Model_Product::COLUMN_UNIT});
         $form->text->setValues($product->{Shop_Model_Product::COLUMN_TEXT});
         $form->textShort->setValues($product->{Shop_Model_Product::COLUMN_TEXT_SHORT});
         $form->urlkey->setValues($product->{Shop_Model_Product::COLUMN_URLKEY});
         $form->keywords->setValues($product->{Shop_Model_Product::COLUMN_KEYWORDS});
         if($product->{Shop_Model_Product::COLUMN_IS_NEW_TO_DATE} != '0000-00-00' AND 
            $product->{Shop_Model_Product::COLUMN_IS_NEW_TO_DATE} != null){
            $form->isNewDate->setValues(vve_date("%x", new DateTime($product->{Shop_Model_Product::COLUMN_IS_NEW_TO_DATE})));
         }
      }
      
      return $form;
   }
 
   /**
    * Meoda pro úpravu produktu
    * @param $product Model_ORM_Record/urlkey/null
    */
   public function editProduct($product = null)
   {
      $model = new Shop_Model_Product();
      if($product instanceof Model_ORM_Record){
         $product = $model->newRecord();
      } else if(is_string($product)){
         $model->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
            ->where(Shop_Model_Product::COLUMN_ACTIVE.' = 1 '
               .'AND '.Shop_Model_Product::COLUMN_URLKEY.' = :ukey ', array('ukey' => $product)
         );
         $product = $model->record();
      } else {
         $product = $model->newRecord();
      }
      
      if($product == false){
         return;
      }
      
      $form = $this->createForm($product);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->editCancelCallback(!$product->isNew());
      }
      
      if($form->isValid()){
         // mazání obrázku
         $imgInfo = $form->image->getValues();
         if(($form->haveElement('imageDel') && $form->imageDel->getValues() == true)
            || ($imgInfo != null && $imgInfo['name'] != $product->{Shop_Model_Product::COLUMN_IMAGE}) ){
            if(is_file(self::getImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})){
               @unlink(self::getImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE});
            }
            if(is_file(self::getImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})){
               @unlink(self::getImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE});
            }
            $product->{Shop_Model_Product::COLUMN_IMAGE} = null;
         }
         
         // uložení nového obrázku
         if ($form->image->getValues() != null) {
            $image = $form->image->createFileObject('Filesystem_File_Image');
            $image->saveAs(self::getImagesDir().'small', VVE_IMAGE_THUMB_W, VVE_IMAGE_THUMB_H, (bool)VVE_IMAGE_THUMB_CROP);
            $image->resampleImage(VVE_DEFAULT_PHOTO_W, VVE_DEFAULT_PHOTO_H, false);
            $image->save();
            $product->{Shop_Model_Product::COLUMN_IMAGE} = $image->getName();
         }
         
         // uložení dat
         $product->{Shop_Model_Product::COLUMN_ID_CATEGORY} = $this->category()->getId();
         
         $product->{Shop_Model_Product::COLUMN_CODE} = $form->code->getValues();
         $product->{Shop_Model_Product::COLUMN_NAME} = $form->name->getValues();
         $product->{Shop_Model_Product::COLUMN_WEIGHT} = $form->weight->getValues();
         $product->{Shop_Model_Product::COLUMN_ACTIVE} = $form->active->getValues();
         $product->{Shop_Model_Product::COLUMN_QUANTITY} = $form->quantity->getValues();
         $product->{Shop_Model_Product::COLUMN_PRICE} = $form->price->getValues();
         $product->{Shop_Model_Product::COLUMN_ID_TAX} = $form->tax->getValues();
         $product->{Shop_Model_Product::COLUMN_UNIT_SIZE} = $form->unitSize->getValues();
         $product->{Shop_Model_Product::COLUMN_UNIT} = $form->unit->getValues();
         $product->{Shop_Model_Product::COLUMN_TEXT} = $form->text->getValues();
         $product->{Shop_Model_Product::COLUMN_TEXT_SHORT} = $form->textShort->getValues();
         $product->{Shop_Model_Product::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues());
         $product->{Shop_Model_Product::COLUMN_URLKEY} =
            $this->createUrlKeys($form->name->getValues(), $form->urlkey->getValues(), $product->{Shop_Model_Product::COLUMN_URLKEY});
         $product->{Shop_Model_Product::COLUMN_KEYWORDS} = $form->keywords->getValues();
         $product->{Shop_Model_Product::COLUMN_IS_NEW_TO_DATE} = $form->isNewDate->getValues();
         $model->save($product);
         $this->editCompleteCallback($product);
      }
      
      $this->view()->form = $form;
      $this->view()->product = $product;
   }
   
   public function deleteProduct($productId = null)
   {
      $formDelete = new Form('product_delete_', true);
      
      $eId = new Form_Element_Hidden('id');
      if($productId != null){
         $eId->setValues($productId);
      } else if($this->view()->product != null){
         $eId->setValues($this->view()->product->{Shop_Model_Product::COLUMN_ID});
      }
      
      $formDelete->addElement($eId);
      
      $eDel = new Form_Element_Submit('del', $this->tr('Smazat'));
      $formDelete->addElement($eDel);
      
      if($formDelete->isValid()){
         
      }
      $this->view()->formDelete = $formDelete;
   }


   protected function editCompleteCallback(Model_ORM_Record $product)
   {
      $this->infoMsg()->addMessage($this->tr('Zboží bylo uloženo'));
//      if($isEdit->isNew()){
      Debug::log($product->{Shop_Model_Product::COLUMN_URLKEY});
//      
      $this->link()->route('detail', array('urlkey' => (string)$product->{Shop_Model_Product::COLUMN_URLKEY}))->reload();
//      } else {
//         $this->link()->route()->reload();
//      }
   }
   
   protected function editCancelCallback($isEdit = true)
   {
      $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
      if($isEdit){
         $this->link()->route('detail')->reload();
      } else {
         $this->link()->route()->reload();
      }
   }
   
   protected function createUrlKeys($names, $urlkeys, $oldKeys)
   {
      foreach ($names as $lang => $value) {
         if($value != null && $urlkeys[$lang] == null){
            $urlkeys[$lang] = vve_cr_url_key($value);
         }
      }
      // vytvoření unikátních klíčů
      $model = new Shop_Model_Product();
      foreach ($urlkeys as $lang => $key) {
         if($key == null){ continue; }
         $testKey = $key;
         $addIndex = 2;
         do {
            $keyOk = true;
            $count = $model->where('('.$lang.')'.Shop_Model_Product::COLUMN_URLKEY.' = :uk AND ('.$lang.')'.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL', 
               array('uk' => $testKey))->count();
            if( ($count == 0) ||
                ($count == 1 && $oldKeys[$lang] == $testKey ) ){
               $keyOk = true;
            } else {
               $testKey = $key.'_'.$addIndex;
               $addIndex++;
            }
         } while ($keyOk == false);
         $urlkeys[$lang] = $testKey;
      }
      return $urlkeys;
   }
   
   public static function getImagesDir($url = false)
   {
      if($url){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.'/shop/'.self::DIR_IMAGES.'/';
      } else {
         return AppCore::getAppDataDir().'shop'.DIRECTORY_SEPARATOR
            .self::DIR_IMAGES.DIRECTORY_SEPARATOR;
      }
      
   }
}
?>
