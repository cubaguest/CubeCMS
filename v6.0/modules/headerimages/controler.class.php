<?php
/* SVN FILE: $Id$ */
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$: controller.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu akcí a kontrolerů modulu
 * 
 * @author $Author$
 * @copyright $Copyright$
 * @version $Revision$
 * @lastrevision $Date$
 * @modifiedby $LastChangedBy$
 * @lastmodified $LastChangedDate$
 */

class HeaderimagesController extends Controller {
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'headerimage_';
	const FORM_FILE = 'file';
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_DELETE = 'delete';
	
	/**
	 * Kontroler pro zobrazení textu
	 */
	public function mainController() {
//		Kontrola práv
		$this->checkReadableRights();

//		Model pro načtení textu
		$model = new HeaderimageDetailModel();
		if($model->isImage()){
			$this->container()->addData('IMAGE', $model->getImageName());
		}

//		pokud má uživatel právo zápisu vytvoříme odkaz pro editaci
		if($this->getRights()->isWritable()){
//			je přidáván obrázek
			if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
				
				$uploadFile = new UploadFiles($this->errMsg());
				$uploadFile->upload(self::FORM_PREFIX.self::FORM_FILE);

				if($uploadFile->isUploaded()){
					$image = new Images($this->errMsg(), $uploadFile->getTmpName());
					if($image->isImage()){
						$saved = true;
						$image->setImageName($model->getImageName(false));
						$saved = $image->saveImage($this->getModule()->getDir()->getDataDir(), null, null, null, IMAGETYPE_JPEG);
					}

				}
				if($saved){
					$this->infoMsg()->addMessage(_('Obrázek byl uložen'));
					$this->getLink()->reload();
				} else {
					$this->errMsg()->addMessage(_('Obrázek se nepodařilo uložit'));
				}
			
			}
			
//			Maže se obrázek
			if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_DELETE])){
				$files = new Files();
				if($files->deleteFile($this->getModule()->getDir()->getDataDir(), $model->getImageName(false))){
					$this->infoMsg()->addMessage(_('Obrázek byl smazán'));
					$this->getLink()->reload();
				} else {
					$this->errMsg()->addMessage(_('Obrázek se nepodařilo smazat'));
				}
			}
		}
	}

}

?>