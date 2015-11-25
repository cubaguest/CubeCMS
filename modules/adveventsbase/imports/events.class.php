<?php
abstract class AdveventsBase_Imports_Events {
   /**
    *
    * @var Controller
    */
   protected $controler = null;
   
   /**
    *
    * @var array
    */
   protected $params = array();
   
   public function __construct(Controller $ctrl, $params)
   {
      $this->controler = $ctrl;
      $this->params = $params;
   }
   
   public function process()
   {
   }

   
   /**
    * Stáhne obrázek do zadaného adresáře a vrátí jej jako objekt
    * @param string $imageUrl
    * @param string $targetDir
    * @return \File
    */
   protected function downloadImage($imageUrl, $targetDir)
   {
      
      $file = new File(AppCore::getAppCacheDir() . '/advevents/' . basename($imageUrl));
      $ch = curl_init($imageUrl);
      $fp = fopen((string) $file, 'wb');
      curl_setopt($ch, CURLOPT_FILE, $fp);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      curl_close($ch);
      fclose($fp);

      return $file;
   }
}
