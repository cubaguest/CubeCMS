<?php
class Template_Stream_Tpl {

   private $position;
   private $varname;
   public $context;

   public function stream_open($path, $mode, $options, &$opened_path)
   {
      $url = parse_url($path);
      var_dump($path, $mode, $options, $opened_path, $url);
      flush();
      return true;
   }

//   public function stream_read($count) {
//      return 0;
//   }
//   public function stream_write($data){
//      return false;
//   }
//
//   public function stream_tell() {
//      return 0;
//   }
//   
//   public function stream_eof() {
//      return true;
//   }
//   
//   public function stream_stat() {
//      return array();
//   }
//   
//   public function stream_seek($offset, $whence) {
//      return null;
//   }
}
