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
include_once AppCore::getAppLibDir() . CUBECMS_LIB_DIR . DIRECTORY_SEPARATOR . 'nonvve' . DIRECTORY_SEPARATOR . 'openid' . DIRECTORY_SEPARATOR . 'openid.php';

class Auth_Provider_OpenID extends Auth_Provider implements Auth_Provider_Interface {

   const FORM_ID_NAME = 'auth_openid';
   const FORM_SUBMIT_NAME = 'auth_openid_submit';


   protected $identity = false;
   protected $required = array(
       'namePerson/first',
       'namePerson/last',
       'contact/email',
   );
   protected $openID;
   protected $defaultGroupID = 0;

   public function __construct($defaultIdGroup = 1, $params = array())
   {
      if (isset($params['identity'])) {
         $this->identity = $params['identity'];
      }
      $this->defaultGroupID = $defaultIdGroup;
   }

   /**
    * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
    * @return boolean -- true pokud se uživatele podařilo přihlásit
    */
   public function authenticate()
   {
      if (!$this->identity) {
         throw new InvalidArgumentException('Nebyla předáná adresa pro ověření');
      }

      $this->checkOpenIDObj();
      if ($this->openID->mode == 'cancel') {
         AppCore::getUserErrors()->addMessage($this->tr('Vaše přihlášení bylo zrušeno'));
      } else if ($this->openID->validate()) {
         $data = $this->openID->getAttributes();
         if( !isset($data['namePerson/first']) || !isset($data['namePerson/first'])){
            AppCore::getUserErrors()->addMessage($this->tr('Chyba při přihlašování. Nedostatek údajů. Pravděpodobně nepodporovaná služba OpenID'));
            return false;
         }
         $username = strtolower($data['namePerson/first'] . $data['namePerson/last']);
         
         if (isset($data['contact/email'])) {
            $username = (string) $data['contact/email'];
         }

         // get user from db by email as username
         $model = new Model_Users();
         $user = Model_Users::getUsersByUsernameAndAuth($username, $this->openID->identity);
         
         if ($user && $user->{Model_Users::COLUMN_BLOCKED} == 1) {
            AppCore::getUserErrors()->addMessage($this->tr('tento účet je blokována. Kontaktujte webmastera.'));
            return false;
         }
         if(!$user) { // pokud není uživatel registrován, uložit jej
            $model = new Model_Users();
            $user = $model->newRecord();
            $user->{Model_Users::COLUMN_GROUP_ID} = $this->defaultGroupID;
            $user->{Model_Users::COLUMN_EXTERNAL_AUTH_ID} = (string)$this->openID->identity;
            $user->{Model_Users::COLUMN_AUTHENTICATOR} = $this->getName();
         } 
         $user->{Model_Users::COLUMN_MAIL} = $data['contact/email'];
         $user->{Model_Users::COLUMN_USERNAME} = $username;
         $user->{Model_Users::COLUMN_NAME} = $data['namePerson/first'];
         $user->{Model_Users::COLUMN_SURNAME} = $data['namePerson/last'];
         $user->save();
         // create auth user obj
         $userObj = new Auth_User($this, $user);

         return $userObj;
      } else {
         AppCore::getUserErrors()->addMessage($this->tr('Vaše přihlášení se nezdařilo. Zkuste přihlášení opakovat.'));
      }
      return false;
   }

   public function isCalled()
   {
      if(isset($_POST[self::FORM_SUBMIT_NAME])){
         if (!isset($_POST[self::FORM_ID_NAME]) || $_POST[self::FORM_ID_NAME] == ""){
            AppCore::getUserErrors()->addMessage($this->tr('Nebylo vplněno OpenID'));
            return false;
         }
         $this->identity = $_POST[self::FORM_ID_NAME];
         header('Location: '.$this->getAuthUrl());
         die;
      }
      if(isset($_GET['auth']) && $_GET['auth'] == 'openid'){
         $this->identity = isset($_GET['openid_identifier']) ? urlencode($_GET['openid_identifier']) : $this->identity;
         return true;
      }
      return false;
   }

   public function getAuthUrl()
   {
      $this->checkOpenIDObj();
      $this->openID->identity = $this->identity;
      $this->openID->required = $this->required;
      $link = new Url();
      $this->openID->returnUrl = (string) $link->param('auth', 'openid')->param('openid_identifier', $this->identity);
      return $this->openID->authUrl();
   }

   protected function checkOpenIDObj()
   {
      if (!$this->openID) {
         $this->openID = new LightOpenID($_SERVER['SERVER_NAME']);
      }
   }

}
