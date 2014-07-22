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

interface Auth_Provider_Interface {
   const OPERATION_CHANGE_PASSWORD = 1;
   const OPERATION_CHANGE_USER = 2;


   public function __construct($defaultIdGroup = 1, $params = array());
   
   /**
    * Provede autentizace uživatele
    * @return false|User vrací false nebo objekt uživatele
    */
   public function authenticate();
   
   /**
    * Provede autentizace uživatele
    * @return false|User vrací false nebo objekt uživatele
    */
   public function changePassword($password);

   /**
    * @return string password
    */
   public function restorePassword();
   
   public function logout();
   
   public function isCalled();
 
   public function isPermanentLogin();
   
   /**
    * vrací, které operace jsou povoleny
    */
   public function operationIsAllowed($operation);
   
   /**
    * Metoda vrací obsah pro přihlášení
    * @return string html obsah pro přihlášení
    */
   public function getLoginContent();
   
   /**
    * Metoda vrací formulář pro přihlášení
    * @return Form|null formulář pro přihlášení
    */
   public function getLoginForm();
}
