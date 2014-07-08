<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of auth_user
 *
 * @author cuba
 */
class Auth_User {
   
   protected $userId = -1;
   protected $userUserName = VVE_DEFAULT_USER_NAME;
   protected $userMail = null;
   protected $userName = null;
   protected $userSurname = null;
   protected $userNote = null;
   protected $userPhone = null;
   protected $userAddress = null;
   protected $userHasPrivateInfo = true;
   
   /**
    *
    * @var Model_ORM_Record
    */
   protected $lastLogin = false;

   protected $groupName = VVE_DEFAULT_GROUP_NAME;
   protected $groupId = VVE_DEFAULT_ID_GROUP;
   
   protected $isAdmin = false;
   protected $isSiteAdmin = false;
   
   protected $userSites = array();
   
   protected $authenticatorName;
   
   protected $authenticator;
   
   public function __construct(Auth_Provider_Interface $authenticator, Model_ORM_Record $user = null)
   {
      $this->authenticator = $authenticator;
      $this->authenticatorName = strtolower(str_replace('Auth_Provider_', '', get_class($authenticator)));
      
      if($user){
         $this->userId = $user->getPK();
         $this->userUserName = $user->{Model_Users::COLUMN_USERNAME};
         $this->userName = $user->{Model_Users::COLUMN_NAME};
         $this->userSurname = $user->{Model_Users::COLUMN_SURNAME};
         $this->userMail = $user->{Model_Users::COLUMN_MAIL};
         $this->userNote = $user->{Model_Users::COLUMN_NOTE};
         $this->userPhone = $user->{Model_Users::COLUMN_PHONE};
         $this->userAddress = $user->{Model_Users::COLUMN_ADDRESS};
         $this->userHasPrivateInfo = $user->{Model_Users::COLUMN_INFO_IS_PRIVATE};

         $this->groupId = $user->{Model_Groups::COLUMN_ID};
         $this->groupName = $user->group_name;

         $this->isAdmin = (bool)$user->{Model_Groups::COLUMN_IS_ADMIN};

         /* detekce jestli je admin */
         $modelSites = new Model_SitesGroups();
         $sites = $modelSites->joinFK(Model_SitesGroups::COLUMN_ID_SITE)
            ->where(Model_SitesGroups::COLUMN_ID_GROUP.' = :idg', array('idg' => $user->{Model_Users::COLUMN_ID_GROUP}))
            ->records();

         if($sites != false){
            foreach($sites as $site) {
               $this->userSites[$site->{Model_Sites::COLUMN_DOMAIN}] = $site->{Model_Sites::COLUMN_ID};
            }
         }

         // detekce adminu
         if($this->isAdmin AND (
            empty($this->userSites)
               OR (VVE_SUB_SITE_DOMAIN != null AND isset($this->userSites[VVE_SUB_SITE_DOMAIN]))
               OR (VVE_SUB_SITE_DOMAIN == null AND isset($this->userSites['www']) ) )){
            $this->isSiteAdmin = true;
         }
      }
      
   }

   public function getGroupName() {
      return $this->groupName;
   }

   /**
    * Metoda vrací id skupiny ve které je uživatel
    * @return integer -- id skupiny
    */
   public function getGroupId() {
      return $this->groupId;
   }

   /**
    * Metoda vrací id uživatele
    * @return integer -- id uživatele
    */
   public function getUserId() {
      return $this->userId;
   }

   /**
    * Metoda vrací název uživatele
    * @return string -- název uživatele
    */
   public function getUserName() {
      return $this->userUserName;
   }
   
   /**
    * Metoda vrací jméno a přijmení uživatele
    * @return string -- název uživatele
    */
   public function getFullName() {
      return $this->getFirstName().' '.$this->getLastName();
   }
   
   /**
    * Metoda vrací jméno
    * @return string -- název uživatele
    */
   public function getFirstName() {
      return $this->userName;
   }
   
   /**
    * Metoda vrací přijmení
    * @return string -- název uživatele
    */
   public function getLastName() {
      return $this->userSurname;
   }

   /**
    * Metoda vrací mail uživatele
    * @return string -- mail uživatele
    */
   public function getMail() {
      return $this->userMail;
   }
   /**
    * 
    * @return type
    * @deprecated since version number
    */
   public function getUserMail() {
      return $this->getMail();
   }
   
   /**
    * Metoda vrací telefon uživatele
    * @return string -- telefon uživatele
    */
   public function getPhone() {
      return $this->userPhone;
   }
      
   /**
    * Metoda vrací adresu uživatele
    * @return string -- adresa uživatele
    */
   public function getAddress() {
      return $this->userAddress;
   }
   
   /**
    * Metoda vrací poznámku uživatele
    * @return string -- poznámka uživatele
    */
   public function getNote() {
      return $this->userNote;
   }
   
   /**
    * Metoda vrací datum posledního přihlášení uživatele
    * @return string -- poznámka uživatele
    */
   public function getLastLoginDate() {
      if(!$this->lastLogin){
         $this->lastLogin = Model_UsersLogins::getLastLogin($this->getUserId());
      }
      return $this->lastLogin ? new DateTime($this->lastLogin->{Model_UsersLogins::COLUMN_TIME}) : false;
   }
   
   /**
    * Metoda vrací IP posledního přihlášení uživatele
    * @return string -- poznámka uživatele
    */
   public function getLastLoginIP() {
      if(!$this->lastLogin){
         $this->lastLogin = Model_UsersLogins::getLastLogin($this->getUserId());
      }
      return $this->lastLogin ? $this->lastLogin->{Model_UsersLogins::COLUMN_IP_ADDRESS} : false;
   }
   
   
   /**
    * Metoda vrací jestli informace o uživateli jsou privátní
    * @return bool -- true pokud jsou data privátní
    */
   public function hasPrivateInfo() {
      return (bool)$this->userHasPrivateInfo;
   }

   
   /**
    * Metoda vrací jestli je uživatele administrátor pro dané stránky
    * @return bool -- true pokud je administrator
    */
   public function isAdmin() {
      return $this->isSiteAdmin;
   }

   /**
    * Metoda vrací jestli je uživatele administrátor pro některé stránky z domény
    * @return bool -- true pokud je administrator
    */
   public function isAdminGroup() {
      return $this->isAdmin;
   }

   /**
    * Metoda vrací pole s weby, kde je uživatel platný
    * @return array -- pole s doménami
    */
   public function getUserSites() {
      return $this->userSites;
   }
   
   public function getAuthenticatorName()
   {
      return $this->authenticatorName;
   }
   
   /**
    * 
    * @return Auth_Provider_Interface
    */
   public function getAuthenticator()
   {
      return $this->authenticator;
   }
   
   public function __sleep()
   {
      return array(
         'userId', 
         'userUserName', 
         'userMail',
         'userName',
         'userSurname',
         'userNote',
         'userPhone',
         'userAddress',
         'userHasPrivateInfo',
         'groupName',
         'groupId',
         'isAdmin',
         'isSiteAdmin',
         'userSites',
         'authenticatorName',
      );
   }
    
   public function __wakeup()
   {
      $this->authenticator = Auth::getAuthenticator($this->authenticatorName);
   }
}
