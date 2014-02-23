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

class Auth_Provider_Google extends Auth_Provider_OpenID implements Auth_Provider_Interface {
   protected $identity = 'https://www.google.com/accounts/o8/id';
   
   public function getAuthUrl()
   {
      $this->checkOpenIDObj();
      $this->openID->identity = $this->identity;
      $this->openID->required = $this->required;
      $link = new Url_Link();
      $this->openID->returnUrl = (string) $link->clear()->param('auth', 'google');
      return $this->openID->authUrl();
   }
   
   public function isCalled()
   {
      return (isset($_GET['auth']) && $_GET['auth'] == 'google');
   }
}
