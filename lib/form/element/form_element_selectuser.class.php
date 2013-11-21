<?php
/**
 * Třída pro obsluhu prvku typu SELECT
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu TEXT. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_SelectUser extends Form_Element_Select {

   protected function init() {
      parent::init();
   }
   
   public function loadUsers($params = array())
   {
      $params += array(
          'groupId' => false,
          'admins' => true,
          'onlyThisWeb' => true,
      );
      
      $modelUsers = new Model_Users();
      $modelUsers->where('1 = 1', array());
      if($params['onlyThisWeb']){
         $modelUsers->usersForThisWeb($params['admins']);
      }
      if($params['groupId']){
         $modelUsers->where(' AND '.Model_Users::COLUMN_GROUP_ID.' = :idg ', array('idg' => $params['groupId']), true);
      }
      
      $users = $modelUsers->records(PDO::FETCH_OBJ);
      $usersFormated = array();
      if($params['groupId']){
         foreach ($users as $user) {
            if($user->{Model_Users::COLUMN_MAIL} != null){
               $usersFormated[$this->getUserName($user)] = $user->{Model_Users::COLUMN_ID}; 
            }
         }
      } else {
         foreach ($users as $user) {
            $grpKey = $this->tr('Skupina').': '.$user->{Model_Groups::COLUMN_LABEL};
            if(!isset($usersFormated[$grpKey])){
               $usersFormated[$grpKey] = array();
            }

            if($user->{Model_Users::COLUMN_MAIL} != null){
               $usersFormated[$grpKey][$this->getUserName($user)] = $user->{Model_Users::COLUMN_ID};
                   
            }
         }
      }
      $this->setOptions($usersFormated, false, false);
   }
   
   private function getUserName($user )
   {
      return $user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME}
                 .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.' <'.$user->{Model_Users::COLUMN_MAIL}.'>';
   }
}
