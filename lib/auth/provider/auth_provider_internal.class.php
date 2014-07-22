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

class Auth_Provider_Internal extends Auth_Provider implements Auth_Provider_Interface {
   const FORM_USERNAME = 'username';
   const FORM_PASSWORD = 'password';
   const FORM_SUBMIT = 'login';
   const FORM_PERMANENT = 'permanent';
   
   /**
    *
    * @var Form
    */
   protected $form = false;

   public function __construct($defaultIdGroup = 1, $params = array()){
   }
   
   /**
    * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
    * @return boolean -- true pokud se uživatele podařilo přihlásit
    */
   public function authenticate() {
      $form = $this->getLoginForm();

      if($form->isSend() && $form->isValid()){
         $user = Model_Users::getUsersByUsername($form->{self::FORM_USERNAME}->getValues());
         if (!$user){
            $form->{self::FORM_USERNAME}->setError($this->tr('Nepodařilo se přihlásit. Zřejmě váš účet neexistuje.'));
            return false;
         } else if($user->{Model_Users::COLUMN_BLOCKED} == 1) {
            $form->{self::FORM_USERNAME}->setError($this->tr('Nepodařilo se přihlásit. Váš účet je bloková.'));
            return false;
         }
         $cryptedPassord = Auth::cryptPassword(htmlentities($form->{self::FORM_PASSWORD}->getValues(),ENT_QUOTES));
         if ( $cryptedPassord != $user->{Model_Users::COLUMN_PASSWORD}
            AND $cryptedPassord != $user->{Model_Users::COLUMN_PASSWORD_RESTORE}
            ){
            $form->{self::FORM_PASSWORD}->setError($this->tr('Bylo zadáno špatné heslo.'));
            return false;
         }
      }
         
      if($form->isValid()){
         // pokud je použito obnovné heslo uožíme jej
         if(Auth::cryptPassword(htmlentities($form->{self::FORM_PASSWORD}->getValues(),ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD_RESTORE}){
            $user->{Model_Users::COLUMN_PASSWORD} = $user->{Model_Users::COLUMN_PASSWORD_RESTORE};
            $user->{Model_Users::COLUMN_PASSWORD_RESTORE} = null;
            $model = new Model_Users();
            $model->save($user);
            unset ($model);
            AppCore::getInfoMessages()->addMessage($tr->tr("Nové heslo bylo nastaveno."));
            Log::msg($tr->tr('Uživateli bylo obnoveno nové heslo'), null, $user->{Model_Users::COLUMN_SURNAME});
         }
         // uložení přihlášení

         $modelUserLogins = new Model_UsersLogins();
         $newLogin = $modelUserLogins->newRecord();
         $newLogin->{Model_UsersLogins::COLUMN_ID_USER} = $user->getPK();
         $newLogin->{Model_UsersLogins::COLUMN_IP_ADDRESS} = $_SERVER['REMOTE_ADDR'];
         $newLogin->{Model_UsersLogins::COLUMN_BROWSER} = $_SERVER['HTTP_USER_AGENT'];
         $modelUserLogins->save($newLogin);

         $userObj = new Auth_User($this, $user);
         Log::msg($this->tr('Uživatel byl přihlášen'), null, $userObj->getUserName());
         return $userObj;
      }
      return false;
   }

   public function changePassword($password)
   {
      
   }
   
   public function logout() 
   {
      
   }
   
   public function operationIsAllowed($operation) {
      return true; // má implementovány všechny
   }
   
   public function isCalled() 
   {
      // kontrola odhlášení
      return $this->getLoginForm()->isSend();
   }
   
   public function isPermanentLogin()
   {
      return $this->getLoginForm()->{self::FORM_PERMANENT}->getValues();
   }
   
   /**
    * Metoda pro generování nového hesla
    * @param $userName
    */
   public function restorePassword()
   {
   }
   
   /**
    * Vrátí obsah pro přihlášení
    * @return string
    */
   public function getLoginContent()
   {
      return (string)$this->getLoginForm();
   }
   
   /**
    * Metoda vytvoří formulář přihlášení
    * @return \Form
    */
   public function getLoginForm()
   {
      if(!$this->form){
         $this->form = new Form('login_');
         $this->form->setAction(new Url());

         $eUserName = new Form_Element_Text(self::FORM_USERNAME, $this->tr('Jméno / e-mail'));
         $eUserName->addValidation(new Form_Validator_NotEmpty($this->tr('Nebylo vyplěnno uživatelksé jméno')));
         $this->form->addElement($eUserName);

         $ePassword = new Form_Element_Password(self::FORM_PASSWORD, $this->tr('Heslo'));
         $ePassword->addValidation(new Form_Validator_NotEmpty($this->tr('Nebylo vyplěnno heslo')));
         $this->form->addElement($ePassword);

         $ePermanent = new Form_Element_Checkbox(self::FORM_PERMANENT, $this->tr('aktivovat trvalé přihlášení'));
         $this->form->addElement($ePermanent);

         $eSubmit = new Form_Element_Submit(self::FORM_SUBMIT, $this->tr('Přihlásit'));
         $this->form->addElement($eSubmit);

         $this->form->isSend();
      
      }
      return $this->form;
   }
}
