<?php
/** 
 * Třída Komponenty přístup k sociálním sítím
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ VVE 7.9 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty přístup k sociálním sítím
 */

class Component_SocialNetwork_Publisher extends Component_SocialNetwork {
   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) 
   {
      parent::__construct(true); // nemá žádný vystup přes url adresy
   }
   
   public function publishPost($postParams)
   {
      $postParams = array_merge(array(
                'name' => null,
                'message' => null,
                'link' => null,
                'caption' => null,
                'description' => null,
                'picture' => null,
                'source' => null,
                'icon' => null,
                ), $postParams);
      // nechat nully a pak je odtranit pomocí foreach a převést na string
      
      // bublish to facebook
      if($this->isValidNetwork('facebook')){
         try {
            $fcb = new Component_SocialNetwork_Facebook();
            $fcb->fcb()->setAccessToken(VVE_FCB_ACCESS_TOKEN);
                  
            $fcb->fcb()->api('/me/feed','post',  $this->cleanParams($postParams));
         } catch (FacebookApiException $e) {
            new CoreErrors($e);
         }
      }
      
   }
   
   /**
    * Metoda vyčistí parametry
    * @param array $params
    * @return array 
    */
   private function cleanParams($params)
   {
      foreach ($params as $key => $param) {
         if($param == null){
            unset($params[$key]);
         } else {
            $params[$key] = (string)$param;
         }
      }
      return $params;
   }
}
?>
