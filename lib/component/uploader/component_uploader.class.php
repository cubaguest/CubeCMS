<?php

/**
 * Třída pro upload souborů na server
 * Třída slouží pro nahrávání souborů na server (kromě IE je podporováno multiple
 * a progressbar) implementace AJAX uploadu od Andrew Valums
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas Js Copyright (c) 2010 Andrew Valums
 * @version    	$Id: $ VVE 7.3.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro nahrávání souborů
 * @see           http://valums.com/ajax-upload/
 */
class Component_Uploader extends Component {
   const CONFIG_ALLOW_EXT = 'allowedExtensions';
   const CONFIG_SIZE_LIMIT = 'sizeLimit';
   const CONFIG_SAVE_PATH = 'path';
   const CONFIG_OVERVRITE = 'overvrite';
   const CONFIG_PARAM_NAME = 'param';
   const CONFIG_ = '';


   /**
    * Pole s konfiguračními hodnotami
    * @var array
    */
   protected $config = array(self::CONFIG_ALLOW_EXT => array(),
      self::CONFIG_SIZE_LIMIT => VVE_MAX_UPLOAD_SIZE,
      self::CONFIG_SAVE_PATH => null,
      self::CONFIG_OVERVRITE => true,
      self::CONFIG_PARAM_NAME => 'qqfile',
      'tpl_file' => 'upload_button.phtml'
   );
   /**
    * Zpracovávaný soubor
    * @var <type>
    */
   private $file;

   public function __construct($runOnly = false)
   {
      parent::__construct($runOnly);
      $this->checkServerSettings();
   }

   private function checkServerSettings()
   {
      if (VVE_MAX_UPLOAD_SIZE < $this->getConfig(self::CONFIG_SIZE_LIMIT)) {
         $size = max(1, $this->getConfig(self::CONFIG_SIZE_LIMIT) / 1024 / 1024) . 'M';
         $sizeMax = max(1, VVE_MAX_UPLOAD_SIZE / 1024 / 1024) . 'M';
         throw new UnexpectedValueException(sprintf($this->tr('Nastavená maximání velikost souboru překračuje limit %s pro upload, max %s'), $size, $sizeMax));
      }
   }

   public function  setConfig($name, $value)
   {
      parent::setConfig($name, $value);
      if($name == self::CONFIG_SIZE_LIMIT){
         $this->checkServerSettings();
      }
   }

   /**
    * Returns array('success'=>true) or array('error'=>'error message')
    */
   public function handleFile()
   {
      $this->checkServerSettings();

      if (isset($_GET[$this->getConfig(self::CONFIG_PARAM_NAME)])) {
         $this->file = new Component_Uploader_Handler_XHR($this->getConfig(self::CONFIG_PARAM_NAME));
      } elseif (isset($_FILES[$this->getConfig(self::CONFIG_PARAM_NAME)])) {
         $this->file = new Component_Uploader_Handler_Form($this->getConfig(self::CONFIG_PARAM_NAME));
      } else {
         $this->file = false;
      }

      if (!is_writable($this->getConfig(self::CONFIG_SAVE_PATH))) {
         AppCore::getUserErrors()->addMessage($this->tr('Chyba nahrávání. Adresáře nemá práva pro zápis'));
         return array('error' => "Server error. Upload directory isn't writable.");
      }

      if (!$this->file) {
         return array('error' => 'No files were uploaded.');
      }

      $size = $this->file->getSize();

      if ($size == 0) {
         return array('error' => 'File is empty');
      }

      if ($size > $this->getConfig(self::CONFIG_SIZE_LIMIT)) {
         return array('error' => sprintf('Soubor je příliš velký %s, max %s', vve_create_size_str($size), vve_create_size_str($this->getConfig(self::CONFIG_SIZE_LIMIT))));
      }

      $pathinfo = pathinfo($this->file->getName());
      $filename = $pathinfo['filename'];
      //$filename = md5(uniqid());
      $ext = $pathinfo['extension'];

      $allowedExt = $this->getConfig(self::CONFIG_ALLOW_EXT);
      if (!empty ($allowedExt) && is_array($allowedExt) && !in_array(strtolower($ext), $allowedExt)) {
         $these = implode(', ', $this->getConfig(self::CONFIG_ALLOW_EXT));
         return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
      }

      if (!$this->getConfig(self::CONFIG_OVERVRITE)) {
         /// don't overwrite previous files that were uploaded
         while (file_exists($this->getConfig(self::CONFIG_SAVE_PATH) . $filename . '.' . $ext)) {
            $filename .= rand(10, 99);
         }
      }

      if ($this->file->save($this->getConfig(self::CONFIG_SAVE_PATH) . $filename . '.' . $ext)) {
         AppCore::getInfoMessages()->addMessage($this->tr('Soubory byly nahrány'));
         return array('success' => true);
      } else {
         return array('error' => 'Could not save uploaded file.' .
            'The upload was cancelled, or server error encountered');
      }
   }

   /**
    * Vygeneruje a odešle výstup a ukončí script
    */
   public function flush()
   {

   }

   /**
    * Metoda pro výpis komponenty
    */
   public function mainView()
   {
      
   }

}
?>
