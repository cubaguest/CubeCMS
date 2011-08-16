<?php
/**
 * Interface pro implementaci platebních metod
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Interface pro implementaci platebních metod v e-shopu
 */
interface Shop_Payment_Interface {
   
   public function __construct(Model_ORM_Record $payment);

   public function renderPaymentInfo();
   
   public function orderCompleteAction(Shop_Basket $basket);
}
?>
