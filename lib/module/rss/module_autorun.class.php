<?php

/**
 * Třída Core Modulu pro automatické spouštění
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu automatického spoštěče
 */
class Module_AutoRun extends Module_Core {
   
   /**
    * 
    * @var Model_AutoRun
    */
   protected $model = null;
   
   public function runController() {
      $this->printMsg($this->tr('AutoRun spuštěn'), null, true);
      $this->printMsg();
      // parsování času
      $time = new DateTime();
      $minute = $time->format("i");
      $hour = $time->format("H");
      $day = $time->format("j");
      $dayInWeek = $time->format("N");
      $month = $time->format("n");
      
      // for testing
      $minute = '00';
      $hour = '01';
      $day = '2';
      $dayInWeek = '7';
      $month = '1';
      
      // testy, který časový úsek se provádí
      
      $this->model = new Model_AutoRun();
      
      // hodinový vždy
      $this->runTasks(Model_AutoRun::PERIOD_HOURLY);
      
      if($hour == "01"){ // denní v 1 ráno
         $this->runTasks(Model_AutoRun::PERIOD_DAILY);
      }
      
      if($dayInWeek == "7" && $hour == "02"){ // týdenní v neděli ve 2 ráno
         $this->runTasks(Model_AutoRun::PERIOD_WEEKLY);
      }
      if($day == "2" && $hour == "03"){ // měsíční prvního ve 3 ráno
         $this->runTasks(Model_AutoRun::PERIOD_MONTHLY);
      }
      if($day == "1" && $month == "1" && $hour == "03"){ // roční 1 měsíc a 2 den 3 ráno
         $this->runTasks(Model_AutoRun::PERIOD_YEARLY);
      }
         
   }

   public function runView() {
      
   }
   
   private function runTasks($type) 
   {
      $this->printMsg(sprintf($this->tr('Spouštím úlohy v sekci %s'),$type ), 'coral', true );
      
      $tasks = $this->model->where(Model_AutoRun::COLUMN_PERIOD." = :period", array('period' => $type))->records(PDO::FETCH_OBJ);
      
      if(!empty($tasks)){
         foreach ($tasks as $task) {
            try {
               
               if($task->{Model_AutoRun::COLUMN_URL} == null){
                  $className = ucfirst($task->{Model_AutoRun::COLUMN_MODULE_NAME}."_Controller");
                  $method = 'AutoRun'.ucfirst($type);
                  $this->printMsg(sprintf($this->tr('Modul: %s, metoda: %s'),
                        $task->{Model_AutoRun::COLUMN_MODULE_NAME},
                        $className."::".$method), 'blue' );
                  if(method_exists($className, $method)){
                     call_user_func(array($className, $method));
                     $this->printMsg($this->tr('Modul spuštěn'), 'Green');
                  } else {
                     $this->printMsg($this->tr('Chybí metoda: ').$className.'::'.$method, 'red');
                  }
               } else {
                  $this->printMsg(sprintf($this->tr('Url: %s'),$task->{Model_AutoRun::COLUMN_URL}), 'blue' );
                  // create a new cURL resource
                  $ch = curl_init();
                  // set URL and other appropriate options
                  curl_setopt($ch, CURLOPT_URL, $task->{Model_AutoRun::COLUMN_URL});
                  curl_setopt($ch, CURLOPT_HEADER, 0);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  // grab URL and pass it to the browser
                  $result = curl_exec($ch);
                  if($result === false){
                     $this->printMsg($this->tr('Chyba při načítání stránky'), 'Red');
                  } else {
                     $this->printMsg($this->tr('Stránka načtena'), 'Green');
                  }
                  // close cURL resource, and free up system resources
                  curl_close($ch);
               }
               
            } catch (Exception $e) {
               echo $e->getTraceAsString(); 
           }
         }
      }
      
      $this->printMsg(sprintf($this->tr('Kompletní provedení sekce %s'),$type ), 'green', true );
      $this->printMsg();
   }
   
   private function printMsg($msg = null, $color = null, $strong = false){
      if($msg == null){
         $msg = str_repeat('=', 50);
         $color = 'silver';
      }
      if($strong){
         $msg = '<strong>'.$msg.'</strong>';
      }
      if($color != null){
         $msg = '<span style="color: '.$color.'">'.$msg.'</span>';
      }
      echo $msg ."<br />";
   }
}
?>
