<?php
/**
 * Třída pro obsluhu přenesených souborů.
 * Třída poskytuje základní metody pro práci s uploadovanými soubory, 
 * jejich kontrolu, zjišťování mime typu a ukládání do filesystému.
 * 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: uploadfiles.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu uploadovaných souborů
 */

class UploadFiles {
	/**
	 * Název proměné s originálním názvem souboru
	 * @var string
	 */
	const POST_FILES_ERROR 			= 'error';
	const POST_FILES_ORIGINAL_NAME	= 'name';
	const POST_FILES_SIZE 			= 'size';
	const POST_FILES_TYPE 			= 'type';
	const POST_FILES_TMP_NAME		= 'tmp_name';
	
	/**
	 * Pole s typy zip souborů
	 * @var array 
	 */
	private $zipExtensionsArray = array("zip" => 'application/zip',
										"zip2" => 'application/x-zip-compressed',
										"zip3" => 'application/x-zip');
	
	/**
	 * Objekt s chybovými zprávami
	 * @var Messages
	 */
	private $errors = null;
	
	/**
	 * Indikace, pokud byla tozeznána chby při uploadu souboru
	 * @var boolean
	 */
	private $uploadError = false;
	
	/**
	 * Proměná obsahuje, jestli je povolen prázdný soubor
	 * @var boolean
	 */
	private $canEmpty = false;
	
	/**
	 * Proměná obsahuje, jestli byl soubor nahrán
	 * @var boolean
	 */
	private $fileUploaded = false;
	
	/**
	 * Proměná obsahuje jestli nebyl nahrán žádný soubor
	 * @var boolean
	 */
	private $noFileUpload = true;
	
	/**
	 * Název původního souboru
	 * @var string
	 */
	private $fileOriginalName = null;
	
	/**
	 * Název tmp souboru
	 * @var string
	 */
	private $fileTmpName = null;
	
	/**
	 * název nového souboru
	 * @var string
	 */
	private $fileNewName = null;
	
	/**
	 * pole s informacemi o souboru
	 * @var Files
	 */
	private $file = null;
	
	/**
	 * Konstruktor třídy
	 *
	 * @param Messages -- objekt pro přístup ke správám (option. pokud je nul je použito CoreException)
	 * @param boolean -- jestli může být přenesen prázdný soubor
	 */
	function __construct(Messages $errors = null, $canEmpty = false){
		$this->canEmpty = $canEmpty;
		$this->errors = $errors;
	}
	
	/**
	 * Metoda přenese zadaný soubor
	 * 
	 * @param string -- název $_POST se souborem
	 * @param string -- adresář do kterého se má soubor zapsat
	 * 
	 * @return boolean -- true pokud byl soubor v pořádku nahrán
	 */
	public function upload($postName, $dest = null) {
		$error = false;
		if (is_uploaded_file($_FILES[$postName][self::POST_FILES_TMP_NAME])) {
			$this->fileUploaded = true;
			$this->noFileUpload = false;
			
			$this->file = $_FILES[$postName];
			
			$this->fileOriginalName = $_FILES[$postName][self::POST_FILES_ORIGINAL_NAME];
			
		} else {
			switch($_FILES[$postName][self::POST_FILES_ERROR]){
				case 0: //no error; possible file attack!
					$this->uploadError = $error = true;
					$this->addError('Problém s nahráním souboru');
					break;
				case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
					$this->uploadError = $error = true;
					$this->addError('Soubor je příliš velký');
					break;
				case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$this->uploadError = $error = true;
					$this->addError('Soubor je příliš velký');
					break;
				case 3: //uploaded file was only partially uploaded
					$this->addError('Soubor byl nahrán jen částečně');
					$this->uploadError = $error = true;
					break;
				case 4: //no file was uploaded
					if(!$this->canEmpty){
						$this->addError('Soubor nebyl vybrán');
						$this->uploadError = $error = true;
					}
					break;
				default: //a default error, just in case!  :)
					$this->uploadError = $error = true;
					$this->addError('Problém s nahráním souboru');
					break;
			}
		}
		return !$error;
	}
	
	/**
	 * Metoda vrací jestli byl soubor nahrán
	 * @return boolean -- true pokud byl soubor nahrán
	 */
	public function isUploaded() {
		return $this->fileUploaded;
	}
	
	/**
	 * Metoda vrací jestli nebyl nahrán žádný soubor
	 * @return boolean -- true pokud byl soubor nahrán
	 */
	public function isUploadError() {
		return $this->uploadError;
	}

	/**
	 * Metoda překopíruje zadaný soubor do zadaného adresáře
	 * //TODO not implemented
	 * 
	 */
	public function copy($dstDir) {
		$files = new Files();
	}
	
	/**
	 * Privátní metoda přiřadí chbovou hlášku
	 * 
	 * @param string -- text hlášky
	 * @param integer -- id chybové hlášky
	 */
	private function addError($text, $idMessage = 0) {
		if($this->errors != null){
			$this->errors->addMessage(_($text));
		} else {
			new CoreException(_($text), $idMessage);
		}
	}
	
	/**
	 * Metoda vrací název nahraného souboru
	 * 
	 * @return string -- nahraný soubor
	 */
	public function getTmpName() {
		return $this->file[self::POST_FILES_TMP_NAME];
	}
	
	/**
	 * Metoda vrací originální název souboru
	 * 
	 * @return string -- originální název souboru
	 */
	public function getOriginalName() {
		return $this->file[self::POST_FILES_ORIGINAL_NAME];
	}
	
	/**
	 * Metoda zjišťuje, zdali je uploadovaný soubor zip soubor
	 * @param string -- soubor, který se zjišťuje
	 * 
	 */
	public function isZipFile() {
		if(in_array($this->getMimeType(), $this->zipExtensionsArray)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda vrací mime typ souboru
	 * //TODO nutná portace na PECL rozšíření o informací o souboru
	 * 
	 * @return string -- mime typ souboru
	 */
	public function getMimeType() {
		return $this->file[self::POST_FILES_TYPE];
	}
}
?>