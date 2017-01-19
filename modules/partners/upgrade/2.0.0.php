<?php
// vytvoříme skupiny podle kategorií

$mPGroups = new Partners_Model_Groups();
$mPartners = new Partners_Model();

$all = $mPartners->records();

$storedCatsToGroup = array();

if($all){
   foreach ($all as $partner) {
      $idcByPartner = (int)$partner->{Partners_Model::COLUMN_ID_GROUP};
      if(!isset($storedCatsToGroup[$idcByPartner])){
         // vytvoř skupinu podle dané kateogrie
         $grp = $mPGroups->getNewRecord();
         $grp->{Partners_Model_Groups::COLUMN_NAME} = array(Locales::getDefaultLang() => 'Výchozí skupina');
         $grp->{Partners_Model_Groups::COLUMN_ID_CATEGORY} = $idcByPartner;
         $grp->save();
         
         $storedCatsToGroup[$idcByPartner] = $grp->getPK();
      } 
      // update skupiny partnera na novou hodnotu
      $partner->{Partners_Model::COLUMN_ID_GROUP} = $storedCatsToGroup[$idcByPartner];
      $partner->save();
   }
}


//var_dump($all);

//var_dump(CoreErrors::getErrors());
//die;

//$grps