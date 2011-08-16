<?php
/**
 * Třída implementující platbu pomocí paypal
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída implementující platbu pomocí paypal
 */
abstract class Shop_Payment implements Shop_Payment_Interface {
   
   /**
    * Informace o platbě
    * @var Model_ORM_Record 
    */
   protected $record = null;


   public function __construct(Model_ORM_Record $payment)
   {
      $this->record = $payment;
   }

   public function renderPaymentInfo()
   {
      
   }
   
   public function orderCompleteAction(Shop_Basket $basket)
   {
      
   }
}
?>
