<?php
class Forum_Install extends Install_Module {
   
   public function install() 
   {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
   
   protected function moduleUpdate($major, $minor) 
   {
      if($major == 1 && $minor == 9){
         // přepočítání zpráv na pozice a první zanoření
         $model = new Forum_Model_Messages();
         $messages = $model
//         ->joinFK(Forum_Model_Messages::COLUMN_ID_TOPIC)
            ->order(array(
               Forum_Model_Messages::COLUMN_ID_TOPIC => Model_ORM::ORDER_ASC, 
               Forum_Model_Messages::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC))
            ->records();
      
         $curTopic = null;
         $curPos = 1;
         foreach ($messages as $msg){
            if($msg->{Forum_Model_Messages::COLUMN_ID_TOPIC} != $curTopic){
               // reset counter if diferent topic
               $curTopic = $msg->{Forum_Model_Messages::COLUMN_ID_TOPIC};
               $curPos = 1;
            }
            // update db
            $msg->{Forum_Model_Messages::COLUMN_ORDER} = $curPos;
            $model->save($msg);
            $curPos++;
         }
         
      }
      
   }
}

?>
