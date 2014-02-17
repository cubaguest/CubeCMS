<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class FaceEdit_Controller extends Controller {
   public function mainController() 
   {
      $this->checkControllRights();

      // check writable
      if(!is_writable(Face::getCurrent()->getDir())){
         throw new ForbiddenAccessException($this->tr('Aplikace nemá přístup k zápisu do adresáře se soubory vzhledu.'));
      }
      
      // load files
      // html, js, css, php zvláště
      $css = array();
      $php = array();
      $phtml = array();
      $js = array();
      
      $dir_iterator = new RecursiveDirectoryIterator(Face::getCurrent()->getDir());
      $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
      foreach($iterator as $file) {
         if(!$file->isDir()){
            $filename = str_replace(Face::getCurrent()->getDir(), "", $file->getPath().DIRECTORY_SEPARATOR.$file->getBasename());
            switch ($file->getExtension()) {
               case 'php':
                  $php[] = $filename;
                  break;
               case 'css':
               case 'less':
                  $css[] = $filename;
                  break;
               case 'js':
                  $js[] = $filename;
                  break;
               case 'phtml':
                  $phtml[] = $filename;
                  break;
            }
         }
      }
      
      
      $this->view()->filesPHP = $php;
      $this->view()->filesJS = $js;
      $this->view()->filesCSS = $css;
      $this->view()->filesPHTML = $phtml;
   }
   
   public function editFileController()
   {
      $this->checkControllRights();
      $file = Face::getCurrent()->getDir().$this->getRequestParam('file');
      $this->view()->fileContent = null;
      $this->view()->fileType = null;
      $this->view()->fileName = null;
      $this->view()->file = $this->getRequestParam('file');
      if(is_file($file)){
         $this->view()->fileName = pathinfo($file, PATHINFO_BASENAME);
         $this->view()->fileContent = file_get_contents($file);
         $this->view()->fileType = pathinfo($file, PATHINFO_EXTENSION);
      }
   }
   
   public function saveFileController()
   {
      $this->checkControllRights();
      if($this->getRequestParam('file') == null){
         throw new UnexpectedValueException($this->tr('Nebyl předán název souboru'));
      }
      $file = preg_replace("/\.\.\/?/", "", $this->getRequestParam('file'));
      $file = Face::getCurrent()->getDir().$file;
      // pokud existuje, přepiš jej
      if(is_file($file)){
         file_put_contents($file, $this->getRequestParam('content'));
         $this->infoMsg()->addMessage($this->tr('Soubor byl uložen'));
      } 
      // pokud neexistuje, vytvoř nový
      else {
         file_put_contents($file, $this->getRequestParam('content'));
         $this->infoMsg()->addMessage($this->tr('Soubor byl vytvořen'));
      }
   }
}
