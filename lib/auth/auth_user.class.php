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
         $this->userNote = $user->{Model_Users::COLUMN_MAIL};

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
      return $this->userName.' '.$this->userSurname;
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
   public function getUserMail() {
      return $this->userMail;
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
