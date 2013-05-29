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
      ob_start();
      $this->printMsg($this->tr('AutoRun spuštěn'), null, true);
      $this->printMsg();
      $this->model = new Model_AutoRun();
      // parsování času
      $time = new DateTime();
      $minute = $time->format("i");
      $hour = $time->format("H");
      $day = $time->format("j");
      $dayInWeek = $time->format("N");
      $month = $time->format("n");
      
      // for testing
//       $minute = '00';
//       $hour = '01';
//       $day = '2';
//       $dayInWeek = '7';
//       $month = '1';
      
      // testy, který časový úsek se provádí
      // hodinový vždy
      $this->runTasks(Model_AutoRun::PERIOD_HOURLY);
      file_put_contents(AppCore::getAppCacheDir()."autorun.log", date("d.m.Y G:i").' - Proveden autorun hourly.'."\n", FILE_APPEND);   
      
      if($hour == "01"){ // denní v 1 ráno
         $this->runTasks(Model_AutoRun::PERIOD_DAILY);
         file_put_contents(AppCore::getAppCacheDir()."autorun.log", date("d.m.Y G:i").' - Proveden autorun daily.'."\n", FILE_APPEND);   
      }
      if($dayInWeek == "7" && $hour == "02"){ // týdenní v neděli ve 2 ráno
         $this->runTasks(Model_AutoRun::PERIOD_WEEKLY);
         file_put_contents(AppCore::getAppCacheDir()."autorun.log", date("d.m.Y G:i").' - Proveden autorun weekly.'."\n", FILE_APPEND);   
      }
      if($day == "2" && $hour == "03"){ // měsíční prvního ve 3 ráno
         $this->runTasks(Model_AutoRun::PERIOD_MONTHLY);
         file_put_contents(AppCore::getAppCacheDir()."autorun.log", date("d.m.Y G:i").' - Proveden autorun monthly.'."\n", FILE_APPEND);   
      }
      if($day == "1" && $month == "1" && $hour == "03"){ // roční 1 měsíc a 1 den 3 ráno
         $this->runTasks(Model_AutoRun::PERIOD_YEARLY);
         file_put_contents(AppCore::getAppCacheDir()."autorun.log", date("d.m.Y G:i").' - Proveden autorun yearly.'."\n", FILE_APPEND);   
      }
      
      if(VVE_SUB_SITE_DOMAIN == null){
         $this->runSubdomainTasks();
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
                     $this->printMsg($this->tr('Modul spuštěn'), 'green');
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
                     $this->printMsg($this->tr('Chyba při načítání stránky'), 'red');
                  } else {
                     $this->printMsg($this->tr('Stránka načtena'), 'green');
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
   
   private function runSubdomainTasks() 
   {
      $this->printMsg($this->tr('Spouštění subdomén'), 'green', true );
      
      $modelSites = new Model_Sites();
      $sites = $modelSites->where(Model_Sites::COLUMN_IS_MAIN." = 0", array())->records();

      if($sites){
         foreach ($sites as $site) {
            $domain = $site->{Model_Sites::COLUMN_DOMAIN}.'.'.Url_Request::getDomain();
            $this->printMsg(sprintf($this->tr('Spouštím úlohy v pro subdoménu "%s"'),$domain ), 'coral', true );
         
            $ch = curl_init();
            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, 'http://'.$domain.'/autorun.php');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // grab URL and pass it to the browser
            $result = curl_exec($ch);
            if($result === false){
               $this->printMsg($this->tr('Chyba při načítání stránky'), 'red');
            } else {
               $this->printMsgSub($result, 'margin-left: 30px;');
            }
            // close cURL resource, and free up system resources
            curl_close($ch);
         }
      }
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
   
   private function printMsgSub($msg = null, $style = null){
      if($msg == null){
         $msg = str_repeat('=', 50);
      }
      $msg = '<p style="'.$style.'">'.$msg.'</p>';
      echo $msg ."<br />";
   }
}
?>
