<?php
/**
 * Třída pro autorizaci.
 * Třída obsluhuje přihlášení/odhlášení uživatele a práci s vlastnostmi (jméno, email,
 * id, skupinu, atd.) přihlášeného uživatele.
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id: auth.class.php 3152 2013-08-01 12:32:20Z jakub $ VVE3.9.4 $Revision: 3152 $
 * @author        $Author: jakub $ $Date: 2013-08-01 14:32:20 +0200 (Čt, 01 srp 2013) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2013-08-01 14:32:20 +0200 (Čt, 01 srp 2013) $
 * @abstract      Třída pro obsluhu autorizace uživatele
 * @todo          Dodělat načítání z modelu a převést taknázvy sloupců do modelu
 */

abstract class Auth_Provider extends TrObject implements Auth_Provider_Interface {

   public function __construct($defaultIdGroup = 1, $params = array()){
      
   }
   
   /**
    * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
    * @return boolean -- true pokud se uživatele podařilo přihlásit
    */
   public function authenticate() {
      return false;
   }

   public function changePassword($password)
   {
      return false;
   }
   
   public function logout() 
   {
      return false;
   }
   
   public function operationIsAllowed($operation) {
      return false;
   }
   
   public function isCalled() 
   {
      // kontrola odhlášení
      return false;
   }
   
   public function isPermanentLogin()
   {
      return false;
   }
   
   /**
    * Metoda pro generování nového hesla
    * @param $userName
    */
   public function restorePassword()
   {
      return false;
   }
   
   protected function getName()
   {
      return strtolower(str_replace('Auth_Provider_', '', get_class($this)));
   }
   
   public function getLoginContent()
   {
      return null;
   }
   
   public function getLoginForm()
   {
      return null;
   }
}
