<?php
class Template_Stream_Tpl {

   private $position;
   private $varname;
   public $context;

   protected $vars = array(
   );
   
   public function stream_open($path, $mode, $options, &$opened_path)
   {
      echo 'Soubor: '.$path."<br />";
      $url = parse_url($path);
//      var_dump($path, $mode, $options, $opened_path, $url);
//      flush();
      return true;
   }

   public function stream_read($count) {
      extract($this->vars);
      include AppCore::getAppLibDir().'templates'.DIRECTORY_SEPARATOR."test.phtml";
      echo '================================<br /><br /><br /><br />';
      return false;
   }

   public function stream_eof() {
      return true;
   }
   
   public function stream_stat() {
      return array();
   }

}
