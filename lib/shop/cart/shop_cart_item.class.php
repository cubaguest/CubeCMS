<?php

/**
 * Třída shop_basket_item
 *
 * @copyright     Copyright (c) 2008-2010 Jakub Matas
 * @version       $Id:  $ VVE 7.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract       Třída položky v nákupním košíku
 */
class Shop_Cart_Item
{

   private $id;
   private $idProduct;
   private $idCombination;
   private $name = null;
   private $note = null;
   private $qty = 1;
   private $qtyProduct = 0;
   private $price = 0;
   private $tax = 0;
   private $weight = 0;
   private $unit = "Ks";
   private $unitSize = 1;
   private $image = null;
   private $url = null;
   private $code = null;

   public function __construct($item)
   {
      if($item instanceof Model_ORM_Record){
         $url = new Url_Link(true);
         $this->id = $item->{Shop_Model_Cart::COLUMN_ID};
         $this
            ->setProductId($item->{Shop_Model_Product::COLUMN_ID})
            ->setCombinationId($item->{Shop_Model_Product_Combinations::COLUMN_ID})
            ->setImage($item->{Shop_Model_Product::COLUMN_IMAGE})
            ->setName($item->{Shop_Model_Product::COLUMN_NAME})
            ->setNote($item->combination_label)
            ->setCode($item->{Shop_Model_Product::COLUMN_CODE})
            ->setQty($item->{Shop_Model_Cart::COLUMN_QTY})
            ->setProductQty($item->{Shop_Model_Product::COLUMN_QUANTITY})
            ->setTax($item->{Shop_Model_Tax::COLUMN_VALUE})
            ->setUnit($item->{Shop_Model_Product::COLUMN_UNIT})
            ->setUnitSize($item->{Shop_Model_Product::COLUMN_UNIT_SIZE})
            ->setPrice($item->{Shop_Model_Product::COLUMN_PRICE})
            ->setImage($item->{Shop_Model_Product::COLUMN_IMAGE})
            ->setUrl($url->clear()->category($item->curlkey.'/'.$item->{Shop_Model_Product::COLUMN_URLKEY}) );
         if($item->combination_codes_json){
            $this->setCode(Shop_Tools::getProductCode($this->code, $item->combination_codes_json));
         }
      }
   }

   public function getId()
   {
      return $this->id;
   }

   public function getName()
   {
      return $this->name;
   }

   public function setName($name)
   {
      $this->name = $name;
      return $this;
   }

   public function getProductId()
   {
      return $this->idProduct;
   }

   public function setProductId($idProduct)
   {
      $this->idProduct = $idProduct;
      return $this;
   }

   public function getCombinationId()
   {
      return $this->idCombination;
   }

   public function setCombinationId($id)
   {
      $this->idCombination = $id;
      return $this;
   }

   public function getNote()
   {
      return $this->note;
   }

   public function setNote($note)
   {
      $this->note = $note;
      return $this;
   }

   public function getPrice($allItems = true, $withTax = true, $withCurrency = false)
   {
      if ($allItems) {
         return round($this->getPrice(false) * ($this->qty == 0 ? $this->unitSize : $this->qty / $this->unitSize), 1);
      }
      if ($withTax) {
         return Shop_Tools::getPrice($this->price, $this->tax, $withCurrency);
      }
      return Shop_Tools::getPrice($this->price, 0, $withCurrency);
   }

   public function setPrice($price)
   {
      $this->price = $price;
      return $this;
   }

   public function getTax()
   {
      return $this->tax;
   }

   public function setTax($tax)
   {
      $this->tax = $tax;
      return $this;
   }

   public function getQty()
   {
      return $this->qty;
   }

   public function setQty($qty)
   {
      $this->qty = $qty;
      return $this;
   }

   public function getProductQty()
   {
      return $this->qtyProduct;
   }

   public function setProductQty($qty)
   {
      $this->qtyProduct = $qty;
      return $this;
   }

   public function getWeight()
   {
      return $this->weight;
   }

   public function setWeight($weight)
   {
      $this->weight = $weight;
      return $this;
   }

   public function getUnit()
   {
      return $this->unit;
   }

   public function setUnit($unit)
   {
      $this->unit = $unit;
      return $this;
   }

   public function getUnitSize()
   {
      return $this->unitSize;
   }

   public function setUnitSize($size)
   {
      $this->unitSize = $size;
      return $this;
   }

   public function getImage()
   {
      return $this->image;
   }

   public function setImage($img)
   {
      $this->image = $img;
      return $this;
   }

   public function getCode()
   {
      return $this->code;
   }

   public function setCode($code)
   {
      $this->code = $code;
      return $this;
   }

   public function getUrl()
   {
      return $this->url;
   }

   public function setUrl($url)
   {
      $this->url = $url;
      return $this;
   }
}

?>
