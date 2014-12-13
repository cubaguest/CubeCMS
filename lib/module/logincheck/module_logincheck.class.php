<?php

/**
 * Třída Core Modulu pro kontrolu přihlášení z externího webu
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ VVE 8.2.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu automatického spoštěče
 */
class Module_LoginCheck extends Module_Core {
   
   public function runController() {
      if(isset($_GET['getsid']) && ( isset($_GET['back']) || isset($_SERVER['HTTP_REFERER']) )){
         $backUrl = isset($_GET['back']) ? $_GET['back'] : $_SERVER['HTTP_REFERER'];
         $urlBack = new Url($backUrl);
         $urlBack->param('sessionid', session_id())
                  ->param('hidesid', 1);
         
         if(Auth::isLogin()){
            // redirect zpět pokud je uživatel přihlášen
            $urlBack->redirect();
            die;
         } else {
            // redirect na login modul
            $tmp = Url_Link::getCategoryLinkByModule('login');
            $link = reset($tmp);
            
            $link->param('redirect', (string)$urlBack)
                ->redirect();
            
         }
      }
      
      Template_Output::setOutputType('json');
      Template_Output::sendHeaders();
      echo json_encode(array(
          'login' => Auth::isLogin(),
          'sid' => session_id(),
          ));
      die;
   }

   public function runView() {
      
   }
   
}
