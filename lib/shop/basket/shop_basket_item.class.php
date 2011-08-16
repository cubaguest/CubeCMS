<?php

/**
 * Třída shop_basket_item
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 7.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída položky v nákupním košíku
 */
class Shop_Basket_Item {
   
   private $id;
   
   private $name = null;
   
   private $note = null;
   
   private $qty = 1;
   
   private $price = 0;
   
   private $tax = 0;
   
   private $weight = 0;
   
   private $unit = "Ks";
   
   private $unitSize = 1;

   private $image = null;
   
   private $url = null;
   
   private $code = null;
   
   public function __construct($id)
   {
      $this->id = $id;
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
   
   public function getNote()
   {
      return $this->note;
   }
   
   public function setNote($note)
   {
      $this->note = $note;
      return $this;
   }
   
   public function getPrice($allItems = true, $withTax = true)
   {
      if($allItems){
         return round($this->getPrice(false)* ($this->qty == 0 ? $this->unitSize : $this->qty / $this->unitSize),1);
      }
      if($withTax){
         return round($this->price * ( $this->tax == 0 ? 1 : $this->tax/100+1 ), 1);
      }
      return round($this->price,1);
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
