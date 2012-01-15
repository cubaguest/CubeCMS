<?php
/**
 * Třída tvorbu excel souborů
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.7 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu excel souborů
 */

// load PHPExcel core
require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                      .'nonvve'.DIRECTORY_SEPARATOR."phpexcel".DIRECTORY_SEPARATOR."PHPExcel.php";

class File_Excel extends File {
	CONST EXPORT_EXCEL_5 = "Excel5";
	CONST EXPORT_EXCEL_2007 = "Excel2007";
	CONST EXPORT_CSV = "CSV";
	CONST EXPORT_PDF = "PDF";
	CONST EXPORT_HTML = "HTML";
   
   
   /**
    * Soubor execelu
    * @var type 
    */
   private $excelFile = null;

   private $exportType = self::EXPORT_EXCEL_5;

   public function __construct($name = null, $path = null)
   {
      parent::__construct($name, $path);
   }

	/**
	 * Metoda vrací obsah souboru
	 * @return PHPExcel -- objek dokumentu
	 */
	public function getData() 
   {
      if(!$this->exist()){
         $this->excelFile = new PHPExcel();
      } else {
         $this->excelFile = PHPExcel_IOFactory::load((string)$this);
      }
		return $this->excelFile;
	}

   /**
    * Metoda nastaví obsah
    * @param PHPExcel $data -- obsah
    */
   public function setData($data)
   {
      if($data instanceof PHPExcel){
         $this->excelFile = $data;
      } else {
         throw new UnexpectedValueException($this->tr('Do souboru excelu nebyl předán platný obsah'));
      }
      return $this;
   }
   
   /**
    * metoda nastaví typ výstupu (excel 5, excel 2007, csv, pdf, html)
    * @param const $type -- konstanty tídy pro export
    * @return File_Excel 
    */
   public function setType($type = self::EXPORT_EXCEL_5)
   {
      $this->exportType = $type;
      return $this;
   }

   /**
    * Metoda uloží daný excel do souoru
    */
   public function save()
   {
      switch ($this->exportType) {
         case self::EXPORT_EXCEL_2007:
            $hCntType = self::$mimeTypes['xlsx'];
            break;
         case self::EXPORT_HTML:
            $hCntType = self::$mimeTypes['html'];
            break;
         case self::EXPORT_PDF:
            $hCntType = self::$mimeTypes['pdf'];
            break;
         case self::EXPORT_CSV:
            $hCntType = self::$mimeTypes['csv'];
            break;
         case self::EXPORT_EXCEL_5:
         default:
            $hCntType = self::$mimeTypes['xls'];
            break;
      }
      $objWriter = PHPExcel_IOFactory::createWriter($this->excelFile, $this->exportType);
      $objWriter->save((string)$this);
      $this->excelFile->disconnectWorksheets();
   }
   
   /**
    * metoda odešle soubor ke klientovi a ukončí běh
    */
   public function send()
   {
      $hCntType = self::$mimeTypes['other'];
      ob_end_clean();
      switch ($this->exportType) {
         case self::EXPORT_EXCEL_2007:
            $hCntType = self::$mimeTypes['xlsx'];
            break;
         case self::EXPORT_HTML:
            $hCntType = self::$mimeTypes['html'];
            break;
         case self::EXPORT_PDF:
            $hCntType = self::$mimeTypes['pdf'];
            break;
         case self::EXPORT_CSV:
            $hCntType = self::$mimeTypes['csv'];
            break;
         case self::EXPORT_EXCEL_5:
         default:
            $hCntType = self::$mimeTypes['xls'];
            break;
      }
      
      $objWriter = PHPExcel_IOFactory::createWriter($this->excelFile, $this->exportType);
              
      header('Content-Description: File Transfer');
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header('Content-Type: '.$hCntType);
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      header('Content-Disposition: attachment;filename="'.$this->getName().'"');
      
      ob_end_clean();
      $objWriter->save('php://output');
      $this->excelFile->disconnectWorksheets();
      exit;
   }
}
?>