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

   protected function createAddToCartForm($idProduct = null, $useCombinations = false)
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

      if($useCombinations && $idProduct != null && ($variantsTMP = Shop_Model_ProductVariants::getVariants($idProduct)) ){
         $selects = array();
         foreach ($variantsTMP as $variant) {
            $selectName = 'attribute_'.$variant->{Shop_Model_AttributesGroups::COLUMN_ID};
            if(!$formAdd->haveElement($selectName)){
               $elemVariantSelect = new Form_Element_Select($selectName, (string)$variant->{Shop_Model_AttributesGroups::COLUMN_NAME} );
               $formAdd->addElement($elemVariantSelect);
            }
            $variantName = (string)$variant->{Shop_Model_Attributes::COLUMN_NAME};
//            $variant->{Shop_Model_ProductVariants::COLUMN_PRICE_ADD} >= 0 ?
//               $variantName .= ' +'.Shop_Tools::getPrice($variant->{Shop_Model_ProductVariants::COLUMN_PRICE_ADD})
//               : $variantName .= Shop_Tools::getPrice($variant->{Shop_Model_ProductVariants::COLUMN_PRICE_ADD});

            $formAdd->$selectName->setOptions(array( $variantName => $variant->{Shop_Model_ProductVariants::COLUMN_ID} ), true);

            if($variant->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT}){
               $formAdd->$selectName->setValues($variant->{Shop_Model_ProductVariants::COLUMN_ID});
            }
            $selects[$variant->{Shop_Model_AttributesGroups::COLUMN_ID}] = $selectName;
         }
         $this->view()->productVariantsSelects = $selects;
      }

      $eAddToCart = new Form_Element_Submit('add', $this->tr('Do košíku'));
      $formAdd->addElement($eAddToCart);

      $combination = false;
      $product = false;
      if($formAdd->isSend()){
         $selectedVariants = array();
         $modelProduct = new Shop_Model_Product();
         if($useCombinations && $this->view()->productVariantsSelects){

            foreach($this->view()->productVariantsSelects as $name) {
               $selectedVariants[] = $formAdd->$name->getValues();
            }
            $this->view()->selectedVariants = $selectedVariants;
         }
         // kontrola velikosti položky
         try {
            $product = $modelProduct->record($formAdd->productId->getValues());
            if($product == false){
               $eProductId->isValid(false);
               throw new UnexpectedValueException('Nebylo předáno správné ID zboží');
            }
            if($eQuantity->getValues() < $product->{Shop_Model_Product::COLUMN_UNIT_SIZE}){
               $eQuantity->isValid(false);
               throw new UnexpectedValueException('Množství nemůže být měnší než nominální hodnota');
            }
            
            if($eQuantity->getValues() % $product->{Shop_Model_Product::COLUMN_UNIT_SIZE} != 0){
               $eQuantity->isValid(false);
               throw new UnexpectedValueException('Množství musí být v násobcích nominální hodnoty');
            }
            // kontrola množství
            if($useCombinations) {
               $combination = Shop_Model_ProductCombinations::getCombinationByVariants($product->getPK(), $selectedVariants);
            } else {
               $combination = Shop_Model_ProductCombinations::getDefaultCombination($product->getPK());
            }
            if($product->{Shop_Model_Product::COLUMN_STOCK} && !VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK){
               // kontrola skladu
               $productQty = ($combination ? $combination->{Shop_Model_ProductCombinations::COLUMN_QTY} : $product->{Shop_Model_Product::COLUMN_QUANTITY});
               if($productQty <= 0){
                  $eQuantity->isValid(false);
                  if($combination){
                     throw new UnexpectedValueException('Produkt v této variantě není skladem a nelze jej tedy zakoupit. Zkuste vybrat jinou variantu');
                  } else {
                     throw new UnexpectedValueException('Produkt není skladem. Nelze jej zakoupit');
                  }
               }
               // kontrola množství ve skladu
               else if ( $eQuantity->getValues() > $productQty ){
                  $eQuantity->isValid(false);
                  throw new UnexpectedValueException( sprintf('Produkt není skladem v požadovaném množství. Skladem je %s %s',
                     $productQty, $product->{Shop_Model_Product::COLUMN_UNIT}));
               }
            }

         } catch (UnexpectedValueException $exc) {
            $this->errMsg()->addMessage($exc->getMessage());
         }
      }

      if($formAdd->isValid()){
         // pokud má produkt kombinace
         $idProduct = $product->getPK();

         if(Shop_Model_ProductCombinations::productHasCombination($idProduct)){
            if($useCombinations && $this->view()->productVariantsSelects){
               // jsou kombinace a varianty
               $selectedVariants = array();
               foreach($this->view()->productVariantsSelects as $name) {
                  $selectedVariants[] = $formAdd->$name->getValues();
               }
               $combination = Shop_Model_ProductCombinations::getCombinationByVariants($idProduct, $selectedVariants);
            } else {
               // výběr kombinací není zadán, bere se v potaz výchozí
               $combination = Shop_Model_ProductCombinations::getDefaultCombination($idProduct);
            }
         }
         $combination
            ? $this->addToCart(
               $formAdd->productId->getValues(), $formAdd->qty->getValues(),
               $combination->{Shop_Model_ProductCombinations::COLUMN_ID}
            )
            : $this->addToCart($formAdd->productId->getValues(), $formAdd->qty->getValues());

         $this->infoMsg()->addMessage($this->tr('Položka byla přidána do košíku'));
         $this->link()->reload();
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

   protected function addToCart($idProduct, $qty, $idCombination = 0, $label = null)
   {
      $cart = new Shop_Cart();
      $cart->addItem($idProduct, $qty, $idCombination, $label);
   }

   protected function loadProducts($productsOnPage = 0, $sort = 'p', $idCategory = 0, $joinCategory = false)
   {
      $model = new Shop_Model_Product();
      $model->setSelectAllLangs(false);
      $model
         ->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
         ->join(Shop_Model_Product::COLUMN_ID, 'Shop_Model_ProductCombinations', Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT);

      if($joinCategory){
         $model->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY);
      }

      if($idCategory != 0){
         $model->where( ($this->category()->getRights()->isWritable() ? null : Shop_Model_Product::COLUMN_ACTIVE.' = 1 AND ' ).
               Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc '
               .'AND ('.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' IS NULL )'
               .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL ',
            array('idc' => $this->category()->getId())
         );
      } else {
         $model->where( ($this->category()->getRights()->isWritable() ? null : Shop_Model_Product::COLUMN_ACTIVE.' = 1 AND ')
               .'('.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' IS NULL )'
               .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL ',
            array()
         );
      }

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
      $model->groupBy(Shop_Model_Product::COLUMN_ID);
      $this->view()->scrollComp = $scrollComponent;
      $this->view()->products = $model->records();
   }
   
   /**
    * Metoda načte produk podle url klíče
    */
   protected function loadCategoryProduct($urlKey = null, $combinationId = 0)
   {
      if($urlKey == null){
         $urlKey = $this->getRequest('urlkey');
      }
      
      $model = new Shop_Model_Product();

      $combBind = array();
      if($combinationId == 0){
         $combWhereString = 'AND ( '.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' = 1'
            .' OR '.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' IS NULL)';
      } else {
         $combWhereString = 'AND '.Shop_Model_ProductCombinations::COLUMN_ID.' = :idcomb ';
         $combBind['idcomb'] = $combinationId;
      }

      $model->setSelectAllLangs(false)
         ->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
         ->join(
            Shop_Model_Product::COLUMN_ID,
            'Shop_Model_ProductCombinations',
            Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT
         )
         ->where( ($this->category()->getRights()->isWritable() ? null :Shop_Model_Product::COLUMN_ACTIVE.' = 1 AND ')
            .Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc '
            .'AND '.Shop_Model_Product::COLUMN_URLKEY.' = :ukey '
            . $combWhereString,
            array_merge( array('idc' => $this->category()->getId(), 'ukey' => $urlKey), $combBind )
      );
      
      $product = $model->record();
      // pokud má produkt kombinace, načteme je
      if(isset($product->{Shop_Model_ProductCombinations::COLUMN_ID})
         && $product->{Shop_Model_ProductCombinations::COLUMN_ID} != null){
         $modelCombinations = new Shop_Model_ProductCombinations();
         // load default combination
         $this->view()->productCombinations
            = Shop_Model_ProductCombinations::getCombinations($product->getPK());
      }
      return $product;
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

      $eCat = new Form_Element_Select('cat', $this->tr('Kategorie'));
      $cats = Model_Category::getCategoryListByModule('shopproductgeneral');
      foreach ($cats as $c) {
        $eCat->setOptions(array((string)$c->{Model_Category::COLUMN_NAME} => $c->getPK()), true);
      }
      $eCat->setValues($this->category()->getId());
      $form->addElement($eCat, $fGrpInfo);

      $eWeight = new Form_Element_Text('weight', $this->tr('Hmotnost'));
      $eWeight->setSubLabel($this->tr('Váha v Kg'));
      $eWeight->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_FLOAT));
      $form->addElement($eWeight, $fGrpInfo);
      
      $eActive = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $eActive->setValues(true);
      $form->addElement($eActive, $fGrpInfo);
      
      $eIsNewDate = new Form_Element_Text('isNewDate', $this->tr('Novinka do'));
      $eIsNewDate->addValidation(new Form_Validator_Date());
      $eIsNewDate->addFilter(new Form_Filter_DateTimeObj());
      $eIsNewDate->setValues(vve_date("%x"));
      $eIsNewDate->setSubLabel($this->tr('Zboží bude označeno jako novinka do zadaného data.'));
      $form->addElement($eIsNewDate, $fGrpInfo);

      $eStock = new Form_Element_Checkbox('stock', $this->tr('Sklad zapnut'));
      $eStock->setValues(true);
      $form->addElement($eStock, $fGrpInfo);

      $eQuantity = new Form_Element_Text('quantity', $this->tr('Položek na skladě'));
      $eQuantity->addValidation(new Form_Validator_NotEmpty());
      $eQuantity->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $eQuantity->setValues(0);
      $form->addElement($eQuantity, $fGrpInfo);

      $eImage = new Form_Element_File('image', $this->tr('Obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
      $eImage->setUploadDir(Shop_Tools::getProductImagesDir());
      $form->addElement($eImage, $fGrpInfo);
      
      if($product instanceof Model_ORM_Record AND $product->{Shop_Model_Product::COLUMN_IMAGE} != null){
         $eImageDel = new Form_Element_Checkbox('imageDel', $this->tr('Smazat obrázek'));
         $eImageDel->setSubLabel(sprintf($this->tr('Uložen obrázek: %s<br />%s')
            ,$product->{Shop_Model_Product::COLUMN_IMAGE}
            ,'<img src="'.$this->module()->getDataDir(true).self::DIR_IMAGES.'/small/'.$product->{Shop_Model_Product::COLUMN_IMAGE}.'" height="50" />'
            ));
         $form->addElement($eImageDel, $fGrpInfo);
      }
      
      $ePersonalPickupOnly = new Form_Element_Checkbox('personalPickupOnly', $this->tr('Pouze osobní odběr'));
      $ePersonalPickupOnly->setSubLabel($this->tr('Zboží lze odebírat pouze osobně.'));
      $ePersonalPickupOnly->setValues(false);
      $form->addElement($ePersonalPickupOnly, $fGrpInfo);
      
      $eNeedPickupDate = new Form_Element_Checkbox('pickupDate', $this->tr('Povinné datum odběru'));
      $eNeedPickupDate->setSubLabel($this->tr('Nakupující musí zadat datum odběru zboží.'));
      $eNeedPickupDate->setValues(false);
      $form->addElement($eNeedPickupDate, $fGrpInfo);
      
      $fGrpPrice = $form->addGroup('price', $this->tr('Ceny'));
      
      $ePrice = new Form_Element_Text('price', $this->tr('Cena bez daně'));
      $ePrice->addValidation(new Form_Validator_NotEmpty());
      $ePrice->addValidation(new Form_Validator_IsNumber(null,Form_Validator_IsNumber::TYPE_FLOAT));
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
//      $eUrlKey->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
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
         $form->cat->setValues($product->{Shop_Model_Product::COLUMN_ID_CATEGORY});
         $form->weight->setValues($product->{Shop_Model_Product::COLUMN_WEIGHT});
         $form->active->setValues($product->{Shop_Model_Product::COLUMN_ACTIVE});
         $form->quantity->setValues($product->{Shop_Model_Product::COLUMN_QUANTITY});
         $form->stock->setValues($product->{Shop_Model_Product::COLUMN_STOCK});
         $form->personalPickupOnly->setValues($product->{Shop_Model_Product::COLUMN_PERSONAL_PICKUP_ONLY});
         $form->pickupDate->setValues($product->{Shop_Model_Product::COLUMN_PICKUP_DATE});
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
            ->where(Shop_Model_Product::COLUMN_URLKEY.' = :ukey ', array('ukey' => $product)
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
            if(is_file(Shop_Tools::getProductImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})){
               @unlink(Shop_Tools::getProductImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE});
            }
            if(is_file(Shop_Tools::getProductImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})){
               @unlink(Shop_Tools::getProductImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE});
            }
            $product->{Shop_Model_Product::COLUMN_IMAGE} = null;
         }
         
         // uložení nového obrázku
         if ($form->image->getValues() != null) {
            $image = $form->image->createFileObject('Filesystem_File_Image');
            $image->saveAs(Shop_Tools::getProductImagesDir().'small', VVE_IMAGE_THUMB_W, VVE_IMAGE_THUMB_H, (bool)VVE_IMAGE_THUMB_CROP);
            $image->resampleImage(VVE_DEFAULT_PHOTO_W, VVE_DEFAULT_PHOTO_H, false);
            $image->save();
            $product->{Shop_Model_Product::COLUMN_IMAGE} = $image->getName();
         }
         
         // uložení dat
         $product->{Shop_Model_Product::COLUMN_ID_CATEGORY} = $form->cat->getValues();
         
         $product->{Shop_Model_Product::COLUMN_CODE} = $form->code->getValues();
         $product->{Shop_Model_Product::COLUMN_NAME} = $form->name->getValues();
         $product->{Shop_Model_Product::COLUMN_WEIGHT} = $form->weight->getValues();
         $product->{Shop_Model_Product::COLUMN_ACTIVE} = $form->active->getValues();
         $product->{Shop_Model_Product::COLUMN_PERSONAL_PICKUP_ONLY} = $form->personalPickupOnly->getValues();
         $product->{Shop_Model_Product::COLUMN_PICKUP_DATE} = $form->pickupDate->getValues();
         $product->{Shop_Model_Product::COLUMN_QUANTITY} = $form->quantity->getValues();
         $product->{Shop_Model_Product::COLUMN_STOCK} = $form->stock->getValues();
         $product->{Shop_Model_Product::COLUMN_PRICE} = $form->price->getValues();
         $product->{Shop_Model_Product::COLUMN_ID_TAX} = $form->tax->getValues();
         $product->{Shop_Model_Product::COLUMN_UNIT_SIZE} = $form->unitSize->getValues();
         $product->{Shop_Model_Product::COLUMN_UNIT} = $form->unit->getValues();
         $product->{Shop_Model_Product::COLUMN_TEXT} = $form->text->getValues();
         $product->{Shop_Model_Product::COLUMN_TEXT_SHORT} = $form->textShort->getValues();
         $product->{Shop_Model_Product::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues());
         $product->{Shop_Model_Product::COLUMN_URLKEY} = $form->urlkey->getValues();
         $product->{Shop_Model_Product::COLUMN_KEYWORDS} = $form->keywords->getValues();
         $product->{Shop_Model_Product::COLUMN_IS_NEW_TO_DATE} = $form->isNewDate->getValues();
         $model->save($product);
         $this->editCompleteCallback($product);
      }
      
      $this->view()->form = $form;
      $this->view()->product = $product;
   }
   
   /**
    * Meoda pro úpravu produktu
    * @param $product Model_ORM_Record/urlkey
    */
   public function editProductVariants($product)
   {
      $model = new Shop_Model_Product();
      $modelProductVariants = new Shop_Model_ProductVariants();
      if($product instanceof Model_ORM_Record){
//         $product = $model->newRecord();
      } else if(is_string($product)){
         $model->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
            ->where(Shop_Model_Product::COLUMN_URLKEY.' = :ukey ', array('ukey' => $product)
         );
         $product = $model->record();
      } else {
         $product = $model->newRecord();
      }
      $this->view()->product = $product;

      if($product == false){
         return;
      }

      // delete variant
      if($this->getRequestParam('dc', false)){
         // smazání kombinací a propojneí s atributy
         $variant = $modelProductVariants->record($this->getRequestParam('dc', 0));

         if($variant){
            $modelCombinationHasVariant = new Shop_Model_ProductCombinationHasVariant();
            $modelCombinations = new Shop_Model_ProductCombinations();

//            Model_ORM::lockModels( array($modelCombinationHasVariant, $modelProductVariants, $modelCombinations) );
            try {
               // kombinace k dané variantě
               $combinations = $modelCombinationHasVariant
                  ->joinFK(Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION)
                  ->where(Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT." = :idp "
                     ."AND ".Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT.' = :idv',
                  array(
                     'idp' => $variant->{Shop_Model_ProductVariants::COLUMN_ID_PRODUCT},
                     'idv' => $variant->{Shop_Model_ProductVariants::COLUMN_ID}))
                  ->records(PDO::FETCH_OBJ);
//
//               $modelCombinationHasVariant2 = new Shop_Model_ProductCombinationHasVariant();
               foreach ($combinations as $c) {
//                  $modelCombinationHasVariant2->where(Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION." = :idc",
//                     array('idc' => $c->{Shop_Model_ProductCombinations::COLUMN_ID}))->delete();
//
                  $modelCombinations->delete($c->{Shop_Model_ProductCombinations::COLUMN_ID});
               }

               // pokud se jedná o poslední variantu z dané skupiny, smažou se všechny vytvořené kombinace,
               // protože zůstávají duplicity


               // pokud je výchozí, nastaví se jiná varianta jako výchozí
               if($variant->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT}){
                  $modelProductVariants = new Shop_Model_ProductVariants();
                  $otherVariants = $modelProductVariants
                     ->joinFK(Shop_Model_ProductVariants::COLUMN_ID_ATTR)
                     ->where(
                        Shop_Model_ProductVariants::COLUMN_ID_PRODUCT.' = :idp AND '.Shop_Model_Attributes::COLUMN_ID_GROUP." = :idatg AND "
                           .Shop_Model_ProductVariants::COLUMN_ID." != :idv",
                     array(
                        'idp' => $product->{Shop_Model_Product::COLUMN_ID},
                        'idatg' => $variant->{Shop_Model_Attributes::COLUMN_ID_GROUP},
                        'idv' => $variant->{Shop_Model_ProductVariants::COLUMN_ID}
                     ))
                     ->records();

                  if($otherVariants && count($otherVariants) > 0){
                     $vNewDefault = $otherVariants[0];
                     $vNewDefault->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT} = true;
                     $modelProductVariants->save($vNewDefault);
                     Shop_Model_ProductCombinations::generateDefaultCombination($product->getPK());
                  }
               }


               // smazání varianty
               $modelProductVariants->delete($variant);
            } catch (PDOException $e) {
               new CoreErrors($e);
            }
//            Model_ORM::unLockTables();
         }
         $this->infoMsg()->addMessage($this->tr('Varianta byla smazána'));
         $this->link()->rmParam('dc')->redirect();
      }

      // update combination QTY
      $formComb = new Form('pr_comb_qty_');

      $elemQty = new Form_Element_Text('qty');
      $elemQty->setMultiple();
      $formComb->addElement($elemQty);

      $elemQty = new Form_Element_Text('qty');
      $elemQty->setMultiple();
      $formComb->addElement($elemQty);

      $elemPrice = new Form_Element_Text('price');
      $elemPrice->setMultiple();
      $formComb->addElement($elemPrice);

      $elemSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $formComb->addElement($elemSave);

      if($formComb->isValid()){
         $qtys = $formComb->qty->getValues();
         $prices = $formComb->price->getValues();

         $modelCombination = new Shop_Model_ProductCombinations();
         foreach ($qtys as $id => $qty) {
            if(!is_numeric($qty)){
               continue;
            }
            $modelCombination->where(Shop_Model_ProductCombinations::COLUMN_ID." = :idc", array('idc' => (int)$id))
               ->update(array(
                  Shop_Model_ProductCombinations::COLUMN_QTY => (int)$qty,
                  Shop_Model_ProductCombinations::COLUMN_PRICE => (int)$prices[$id],
               ));
         }
         Shop_Model_ProductCombinations::updateProductQty($product->getPK());
         $this->infoMsg()->addMessage($this->tr('Množství bylo aktualizováno'));
         $this->link()->redirect();
      }

      $this->view()->formComb = $formComb;

      // načtení variant produktu
      $modelProductVariants = new Shop_Model_ProductVariants();
      $productVariants = $modelProductVariants
         ->join(Shop_Model_ProductVariants::COLUMN_ID_ATTR, array( 'prattr' => "Shop_Model_Attributes"), Shop_Model_Attributes::COLUMN_ID)
         ->join( array('prattr' => Shop_Model_Attributes::COLUMN_ID_GROUP), "Shop_Model_AttributesGroups", Shop_Model_AttributesGroups::COLUMN_ID)
         ->join(Shop_Model_ProductVariants::COLUMN_ID,
               "Shop_Model_ProductCombinationHasVariant",
               Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT,
               array(Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION))
         ->groupBy(Shop_Model_ProductVariants::COLUMN_ID)
         ->where(Shop_Model_ProductVariants::COLUMN_ID_PRODUCT." = :idp", array('idp' => $product->{Shop_Model_Product::COLUMN_ID}))
         ->order(array('prattr.'.Shop_Model_Attributes::COLUMN_ID_GROUP => Model_ORM::ORDER_ASC, Shop_Model_Attributes::COLUMN_NAME => Model_ORM::ORDER_ASC))
         ->records();

      $this->view()->productVarinats = $productVariants;

      // načtení výchozích hodnot a výběr kategorií
      $defaults = array();
      $variantsGroups = array();
      foreach ($productVariants as $var) {
         if($var->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT}){
            $defaults[] = $var->{Shop_Model_ProductVariants::COLUMN_ID};
         }
         $variantsGroups[$var->{Shop_Model_Attributes::COLUMN_ID_GROUP}] = $var;
      }
      $this->view()->variantsGroups = $variantsGroups;


      // načtení všech variant
      $modelVariants = new Shop_Model_Attributes();
      $modelVariantsGrps = new Shop_Model_AttributesGroups();
      $variants = $modelVariants
         ->joinFK(Shop_Model_Attributes::COLUMN_ID_GROUP)
         ->order(array( Shop_Model_AttributesGroups::COLUMN_NAME, Shop_Model_Attributes::COLUMN_NAME))
         ->records();

      // form přidání atributů
      $formAddVariant = new Form('product_add_variant_');
      $elemSelect = new Form_Element_Select('ids');
      $elemSelect->setMultiple(true);
      $elemSelect->addValidation(new Form_Validator_NotEmpty($this->tr('Nebyla vybrána žádná varianta.')));
      $vTmp = array();
      foreach($variants as $v ) {
         $vGName = (string)$v->{Shop_Model_AttributesGroups::COLUMN_NAME};
         if(!isset($vTmp[$vGName]) ){
            $vTmp[$vGName] = array();
         }
         $vTmp[$vGName][(string)$v->{Shop_Model_Attributes::COLUMN_NAME}] = $v->{Shop_Model_Attributes::COLUMN_ID};
      }
      $elemSelect->setOptions($vTmp);
      $formAddVariant->addElement($elemSelect);

      $elemSaveAdd = new Form_Element_Submit('save', $this->tr('Přidat'));
      $formAddVariant->addElement($elemSaveAdd);

      if($formAddVariant->isValid()){
         $ids = $formAddVariant->ids->getValues();


         $modelProductVariants = new Shop_Model_ProductVariants();
         $modelAttributes = new Shop_Model_Attributes();
//         Model_ORM::lockModels(array($modelProductVariants, $modelAttributes));

         foreach($ids as $id) {
            $alreadyExist = (bool)$modelProductVariants
               ->where(Shop_Model_ProductVariants::COLUMN_ID_PRODUCT." = :idp AND "
                  .Shop_Model_ProductVariants::COLUMN_ID_ATTR." = :ida",
                  array('idp' => $product->{Shop_Model_Product::COLUMN_ID}, 'ida' => $id))
               ->count();
            // nepřidávat pokud existuje
            if($alreadyExist){
               continue;
            }

            $rec = $modelProductVariants->newRecord();
            $rec->{Shop_Model_ProductVariants::COLUMN_ID_ATTR} = $id;
            $rec->{Shop_Model_ProductVariants::COLUMN_ID_PRODUCT} = $product->{Shop_Model_Product::COLUMN_ID};
            // @todo kontorla pokud je z dané skupiny první, nastavit jako výchozí

            try {
               $attr = $modelAttributes->record($id);

               $isSomeInGroup = (bool)$modelProductVariants
                  ->joinFK(Shop_Model_ProductVariants::COLUMN_ID_ATTR)
                  ->where(Shop_Model_ProductVariants::COLUMN_ID_PRODUCT." = :idp AND "
                     .Shop_Model_Attributes::COLUMN_ID_GROUP." = :idg",
                  array('idp' => $product->{Shop_Model_Product::COLUMN_ID}, 'idg' => $attr->{Shop_Model_Attributes::COLUMN_ID_GROUP}))
                  ->count();

               $rec->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT} = $isSomeInGroup ? false : true;
               $rec->save();
            } catch (Exception $e) {
               new CoreErrors($e);
            }
         }
//         Model_ORM::unLockTables();

         $this->infoMsg()->addMessage($this->tr('Varianty byly přidány'));
         $this->link()->reload();
      }
      $this->view()->formAddVariant = $formAddVariant;

      // form úpravy kódu
      $formProductCode = new Form('product_code_');

      $elemCode = new Form_Element_Text('code', $this->tr('Kód produktu'));
      $elemCode->addValidation(new Form_Validator_NotEmpty());
      $elemCode->setValues($product->{Shop_Model_Product::COLUMN_CODE});
      $formProductCode->addElement($elemCode);

      $elemSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $formProductCode->addElement($elemSave);

      if($formProductCode->isValid()){
         // save code
         $product->{Shop_Model_Product::COLUMN_CODE} = $formProductCode->code->getValues();
         $product->save();

         $this->infoMsg()->addMessage($this->tr('Kód byl uložen'));
         $this->link()->redirect();
         // regenerate combinations
      }

      $this->view()->formProductCode = $formProductCode;

      // form uložení varianty
      $formEditVariants = new Form('product_variants_');

      $elemPrice = new Form_Element_Text('price', $this->tr('Cena'));
      $elemPrice->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $elemPrice->setMultiple();
      $formEditVariants->addElement($elemPrice);

      $elemCode = new Form_Element_Text('code', $this->tr('Kód'));
      $elemCode->setMultiple();
      $formEditVariants->addElement($elemCode);

      $elemDefault = new Form_Element_Radio('default', $this->tr('Výchozí'));
      $elemDefault->setMultiple();
      $elemDefault->setOptions(array(null));
      $elemDefault->setValues($defaults);
      $elemDefault->setCheckOptions(false);
      $formEditVariants->addElement($elemDefault);

      $elemWeight = new Form_Element_Text('weight', $this->tr('Váha'));
      $elemWeight->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_FLOAT));
      $elemWeight->setMultiple();
      $formEditVariants->addElement($elemWeight);

      $elemGenPrices = new Form_Element_Checkbox('generatePrice', $this->tr('Upravit ceny kombinací'));
      $formEditVariants->addElement($elemGenPrices);

      $eSave = new Form_Element_Submit('save', $this->tr('Vytvořit kombinace'));
      $formEditVariants->addElement($eSave);

      if($formEditVariants->isValid()){
         // save data
         $prices = $formEditVariants->price->getValues();
         $codes = $formEditVariants->code->getValues();
         $weights = $formEditVariants->weight->getValues();
         $defaults = $formEditVariants->default->getValues();

         $variantForComb = array();
         $varCurGrp = null;
         $key = 0;
         $defaultVariants = array();
         foreach ($productVariants as $variant) {
            $variant->{Shop_Model_ProductVariants::COLUMN_PRICE_ADD} = $prices[$variant->{Shop_Model_ProductVariants::COLUMN_ID}];
            $variant->{Shop_Model_ProductVariants::COLUMN_CODE_ADD} =
               isset($codes[$variant->{Shop_Model_ProductVariants::COLUMN_ID}]) ?
               $codes[$variant->{Shop_Model_ProductVariants::COLUMN_ID}] : null;
            $variant->{Shop_Model_ProductVariants::COLUMN_WEIGHT_ADD} = $weights[$variant->{Shop_Model_ProductVariants::COLUMN_ID}];

            $variant->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT} = false;
            if(in_array($variant->{Shop_Model_ProductVariants::COLUMN_ID}, $defaults)){
               $variant->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT} = true;
            }

            if($variant->{Shop_Model_ProductVariants::COLUMN_IS_DEFAULT}){
               $defaultVariants[] = $variant->{Shop_Model_ProductVariants::COLUMN_ID};
            }

            $variant->save();

            if($variant->{Shop_Model_Attributes::COLUMN_ID_GROUP} != $varCurGrp ){
               $varCurGrp != null ? $key++ : null;
               $varCurGrp = $variant->{Shop_Model_Attributes::COLUMN_ID_GROUP};
            }
            if(!isset($variantForComb[$key])){
               $variantForComb[$key] = array();
            }
            $variantForComb[$key][] = $variant->getPK();

         }
         // generování kombinací
         Shop_Model_ProductCombinations::generateCombinations(
            $variantForComb,
            $defaultVariants,
            $product->getPK(),
            $product->{Shop_Model_Product::COLUMN_QUANTITY},
            $formEditVariants->generatePrice->getValues()
         );
         Shop_Model_ProductCombinations::generateDefaultCombination($product->getPK());

         $this->infoMsg()->addMessage($this->tr('Kombinace byly generovány'));
         $this->link()->redirect();
      }

      $this->view()->formEditVariants = $formEditVariants;

      // get combinations -- move to model
      $modelCombinations = new Shop_Model_ProductCombinations();
      $modelCombinations->prepareForProductCombinations($product->getPK());

      if($this->getRequestParam('pord', false)){
         $modelCombinations->order(array('price' => $this->getRequestParam('pord', 'a') == 'a' ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC));
      }
      if($this->getRequestParam('qord', false)){
         $modelCombinations->order(array(Shop_Model_ProductCombinations::COLUMN_QTY => $this->getRequestParam('qord', 'a') == 'a' ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC));
      }
      if($this->getRequestParam('word', false)){
         $modelCombinations->order(array('weight' => $this->getRequestParam('word', 'a') == 'a' ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC));
      }
      $productCombinations = $modelCombinations->records(PDO::FETCH_OBJ);
      $this->view()->productCombinations = $productCombinations;
   }

   public function processDeleteProduct(Model_ORM_Record $product)
   {
      if(!$this->rights()->isWritable()){ return; }
      $formDelete = new Form('product_delete_', true);
      
      $eId = new Form_Element_Hidden('id');
      $eId->setValues($product->{Shop_Model_Product::COLUMN_ID});

      $formDelete->addElement($eId);
      
      $eDel = new Form_Element_Submit('del', $this->tr('Smazat'));
      $formDelete->addElement($eDel);
      
      if($formDelete->isValid()){
         $model = new Shop_Model_Product();
         $product = $model->record($formDelete->id->getValues());
         
         // image
         if($product->{Shop_Model_Product::COLUMN_IMAGE} != null){
            if(is_file(Shop_Tools::getProductImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})
              && is_writable(Shop_Tools::getProductImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})){
                  @unlink(Shop_Tools::getProductImagesDir().'small'.DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE});
            }
            if(is_file(Shop_Tools::getProductImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})
               && is_writable(Shop_Tools::getProductImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE})){
                  @unlink(Shop_Tools::getProductImagesDir().DIRECTORY_SEPARATOR.$product->{Shop_Model_Product::COLUMN_IMAGE});
            }
         }
         $model->delete($product);

         // @todo smazat kombinace, varinaty a propojení

         $this->infoMsg()->addMessage(sprintf($this->tr('Produkt %s byl smazán'), $product->{Shop_Model_Product::COLUMN_NAME} ));
         $this->link()->route()->reload();
      }
      
      $this->view()->formDelete = $formDelete;
   }

   public function processDuplicateProduct(Model_ORM_Record $product)
   {
      if(!$this->rights()->isWritable()){ return; }
      $formDuplicate = new Form('product_duplicate_', true);

      $eId = new Form_Element_Hidden('id');
      $eId->setValues($product->getPK());

      $formDuplicate->addElement($eId);

      $eDel = new Form_Element_Submit('submit', $this->tr('Duplikovat zboží'));
      $formDuplicate->addElement($eDel);

      if($formDuplicate->isValid()){
         $model = new Shop_Model_Product();
         // duplikace produktu
//         $product = $model->record($formDuplicate->id->getValues());
         $origId = $product->getPK();
         $product->setNew();
         $product->{Shop_Model_Product::COLUMN_ACTIVE} = false;
         $product->{Shop_Model_Product::COLUMN_NAME} = $this->tr('Kopie')." ".$product->{Shop_Model_Product::COLUMN_NAME};
         $product->save();

         // duplikace varinat
         $modelVariants = new Shop_Model_ProductVariants();
         $variants = $modelVariants->where(Shop_Model_ProductVariants::COLUMN_ID_PRODUCT." = :idp", array('idp' => $origId))->records();

         $createdCombinations = array(); // pole s ID starých a nových kombinací 'oldid' => 'newid'
         if($variants){
            foreach ($variants as $variant){
               // duplikace varianty
               $oldVariantId = $variant->getPK();
               $variant->setNew();
               $variant->{Shop_Model_ProductVariants::COLUMN_ID_PRODUCT} = $product->getPK();
               $variant->save();

               // načtou se propojení s kombinacemi
               $modelCombVariant = new Shop_Model_ProductCombinationHasVariant();
               $combinationsHasVariants = $modelCombVariant
                  ->where(Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT." = :idv", array('idv' => $oldVariantId))
                  ->records();
               // projdou se propojení
               foreach ($combinationsHasVariants as $combinationHasVariant) {
                  // pokud nová kombinace ještě neexistuje tak se vytvoří
                  $oldComId = $combinationHasVariant->{Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION};

                  if(!isset($createdCombinations[$oldComId])){
                     $modelCombination = new Shop_Model_ProductCombinations();
                     $combination = $modelCombination->record($oldComId);
                     $combination->setNew();
                     $combination->{Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT} = $product->getPK();
                     $combination->save();
                     // uložíme že kombinace je vytvořena
                     $createdCombinations[$oldComId] = $combination->getPK();
                  }

                  // uložení propojení s novou kombinací
                  $combVar = $modelCombVariant->newRecord();
                  $combVar->{Shop_Model_ProductCombinationHasVariant::COLUMN_ID_COMBINATION} = $createdCombinations[$oldComId];
                  $combVar->{Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT} = $variant->getPK();
                  $combVar->save();
               }
            }
         }

         // duplikace obrázku
         $img = new File($product->{Shop_Model_Product::COLUMN_IMAGE}, self::getImagesDir());
         if($img->exist()){
            $img = $img->copy(self::getImagesDir(), true);
            // kopie miniatury
            $thumb = new File($product->{Shop_Model_Product::COLUMN_IMAGE}, self::getImagesDir()."small".DIRECTORY_SEPARATOR);
            $thumb->copy(self::getImagesDir()."small".DIRECTORY_SEPARATOR, true);
            // uložení nového názvu obrázku
            $product->{Shop_Model_Product::COLUMN_IMAGE} = $img->getName();
            $product->save();
         }

         $this->infoMsg()->addMessage($this->tr('Produkt byl duplikován'));
         $this->link()->route('detail', array('urlkey' => $product->{Shop_Model_Product::COLUMN_URLKEY}))->redirect();
      }

      $this->view()->formDuplicate = $formDuplicate;
   }

   public function processChangeProductState(Model_ORM_Record $product)
   {
      $form = new Form('product_state_');

      $elemState = new Form_Element_Hidden('state');
      $elemState->setValues($product->{Shop_Model_Product::COLUMN_ACTIVE} ? 0 : 1);
      $form->addElement($elemState);

      $elemChange = new Form_Element_Submit('change', $product->{Shop_Model_Product::COLUMN_ACTIVE} ? $this->tr('Deaktivovat') : $this->tr('Aktivovat'));
      $form->addElement($elemChange);

      if($form->isValid()){
         $product->{Shop_Model_Product::COLUMN_ACTIVE} = (bool)$form->state->getValues();
         $product->save();

         $product->{Shop_Model_Product::COLUMN_ACTIVE}
            ? $this->infoMsg()->addMessage($this->tr('Produkt by aktivován'))
            : $this->infoMsg()->addMessage($this->tr('Produkt by deaktivován'));
         $this->link()->redirect();
      }

      $this->view()->formState = $form;
   }

   protected function editCompleteCallback(Model_ORM_Record $product)
   {
      $this->infoMsg()->addMessage($this->tr('Zboží bylo uloženo'));
//      if($product->isNew()){
      $pr = (new Shop_Model_Product())
         ->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY, array('curlkey' => Model_Category::COLUMN_URLKEY))
         ->record($product->getPK());

      $this->link()
         ->category($pr->curlkey)
         ->route('detail', array('urlkey' => (string)$product->{Shop_Model_Product::COLUMN_URLKEY}))
         ->redirect();
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

   public static function getImagesDir($url = false)
   {
      return Shop_Tools::getProductImagesDir($url);
   }

}
