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
   const FORM_USERNAME = 'login_username';
   const FORM_PASSWORD = 'login_password';
   const FORM_SUBMIT = 'login_submit';
   
   public function __construct($defaultIdGroup = 1, $params = array()){
      
   }
   
   /**
    * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
    * @return boolean -- true pokud se uživatele podařilo přihlásit
    */
   public function authenticate() {
      $tr = new Translator();
      if (($_POST[self::FORM_USERNAME] == "") and ($_POST[self::FORM_PASSWORD] == "")){
         AppCore::getUserErrors()->addMessage($tr->tr("Byly zadány prázdné údaje"));
         
      } else {
         $user = Model_Users::getUsersByUsername($_POST[self::FORM_USERNAME]);
         if (!$user){
            AppCore::getUserErrors()->addMessage($tr->tr("Nepodařilo se přihlásit. Zřejmě váš účet neexistuje."));
            return false;
            
         } else if($user->{Model_Users::COLUMN_BLOCKED} == 1) {
            AppCore::getUserErrors()->addMessage($tr->tr("Nepodařilo se přihlásit. Zřejmě váš účet neexistuje."));
            return false;
            
         } else {
            if (Auth::cryptPassword(htmlentities($_POST[self::FORM_PASSWORD],ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD}
               OR (
                   $user->{Model_Users::COLUMN_PASSWORD_RESTORE} != null
                     AND Auth::cryptPassword(htmlentities($_POST[self::FORM_PASSWORD],ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD_RESTORE})
                     ){
                     
               // pokud je použito obnovné heslo uožíme jej
               if(Auth::cryptPassword(htmlentities($_POST[self::FORM_PASSWORD],ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD_RESTORE}){
                  $user->{Model_Users::COLUMN_PASSWORD} = $user->{Model_Users::COLUMN_PASSWORD_RESTORE};
                  $user->{Model_Users::COLUMN_PASSWORD_RESTORE} = null;
                  $model = new Model_Users();
                  $model->save($user);
                  unset ($model);
                  AppCore::getInfoMessages()->addMessage($tr->tr("Nové heslo bylo nastaveno."));
                  Log::msg($tr->tr('Uživateli bylo obnoveno nové heslo'), null, self::$userName);
               }
               // uložení přihlášení

               $modelUserLogins = new Model_UsersLogins();
               $newLogin = $modelUserLogins->newRecord();
               $newLogin->{Model_UsersLogins::COLUMN_ID_USER} = $user->getPK();
               $newLogin->{Model_UsersLogins::COLUMN_IP_ADDRESS} = $_SERVER['REMOTE_ADDR'];
               $newLogin->{Model_UsersLogins::COLUMN_BROWSER} = $_SERVER['HTTP_USER_AGENT'];
               $modelUserLogins->save($newLogin);

               $userObj = new Auth_User($this, $user);
               Log::msg($tr->tr('Uživatel byl přihlášen'), null, $userObj->getUserName());
               return $userObj;
            } else {
               AppCore::getUserErrors()->addMessage($tr->tr("Bylo zadáno špatné heslo."));
               return false;
            }
         }
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
      return (isset($_POST[self::FORM_SUBMIT]) OR isset ($_POST[self::FORM_SUBMIT.'_x']));
   }
   
   /**
    * Metoda pro generování nového hesla
    * @param $userName
    */
   public function restorePassword()
   {
   }
}
