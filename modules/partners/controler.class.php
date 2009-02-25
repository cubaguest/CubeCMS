<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class PartnersController extends Controller {
	/**
	 * Názvy formulářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'partner_';
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_DELETE = 'delete';
	const FORM_NAME = 'name';
	const FORM_LABEL = 'label';
	const FORM_URL = 'url';
	const FORM_LOGO_FILE = 'logo_file';
	const FORM_ID = 'id';
	const FORM_DELETE_IMAGE = 'delete_image';

   /**
    * Parametry pro velikost obrázku
    */
   const PARAM_LOGO_WIDTH = 'logowidth';
   const PARAM_LOGO_HEIGHT = 'logoheight';

	/**
	 * Velikosti obrázku loga společnosti
	 * @var int
	 */
   const LOGO_IMAGE_WIDTH = 150;
   const LOGO_IMAGE_HEIGHT = 150;
//	const LOGO_BIG_IMAGE_WIDTH = 120;
//	const LOGO_BIG_IMAGE_HEIGHT = 80;

   /**
    * Názvy prvků s odkazy
    */
   const LINK_TO_EDIT = 'linkEdit';

	/**
	 * Adresář s velkými logy
	 * @var string
	 */
//	const LOGO_BIG_DIR = 'big';

   /**
    * Pole s typy adres, které jsou validní
    * @var array
    */
   private $validProtocols = array('http', 'https', 'ftp');


	public function mainController() {
		$this->checkReadableRights();

//      načtení sponzorů
      $partnersModel = new PartnersListModel();

      $partners = $partnersModel->getPartners();
      
      if($this->getRights()->isWritable()){
         // smazání sponzora
         $this->deletePartner();
         // doplnění odkazů pro editaci
         foreach ($partners as $key => $partner) {
            $partners[$key][self::LINK_TO_EDIT] = $this->getLink()->article($partner[PartnerDetailModel::COLUMN_NAME],
               $partner[PartnerDetailModel::COLUMN_ID_PARTNER])->action($this->getAction()->editPartner());
         }

         // Odkaz pro přidání sponzora
         $this->container()->addLink('LINK_TO_ADD', $this->getLink()->action($this->getAction()->addPartner()));
      }

      $this->container()->addData('LOGO_DIR', $this->getModule()->getDir()->getDataDir());
      $this->container()->addData('PARTNERS_ARRAY', $partners);
	}
	
	/**
	 * Kontroler pro obsluhu přidání sponzora
	 */
	public function addpartnerController(){
		$this->checkWritebleRights();

      $addForm = new Form(self::FORM_PREFIX);

      $addForm->crInputText(self::FORM_NAME, true)
      ->crTextArea(self::FORM_LABEL, false, true, Form::CODE_HTMLDECODE)
      ->crInputText(self::FORM_URL, false, false, Form::VALIDATE_URL)
      ->crInputFile(self::FORM_LOGO_FILE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //Kontrola formuláře
      if($addForm->checkForm()){
         $fileError = false;
         $partnerModel = new PartnerDetailModel();

         $logoFile = null;
         $logoType = null;
         $logoWidth = 0;
         $logoHeight = 0;
         // Kontrola jestli bylo uloženo logo
         if($addForm->getValue(self::FORM_LOGO_FILE) != null){
            // KOntrola jestli nebyl uložen obrázek
            $imageFile = new ImageFile($addForm->getValue(self::FORM_LOGO_FILE));
            $flashFile = new FlashFile($addForm->getValue(self::FORM_LOGO_FILE));
            if($imageFile->isImage(false)){
               $this->getModule()->getParam(self::PARAM_LOGO_WIDTH) == null ?
               $newLogoWidth = self::LOGO_IMAGE_WIDTH :
               $newLogoWidth = $this->getModule()->getParam(self::PARAM_LOGO_WIDTH);

               $this->getModule()->getParam(self::PARAM_LOGO_HEIGHT) == null ?
               $newLogoHeight = self::LOGO_IMAGE_HEIGHT :
               $newLogoHeight = $this->getModule()->getParam(self::PARAM_LOGO_HEIGHT);

               $imageFile->saveImage($this->getModule()->getDir()->getDataDir(),
                  $newLogoWidth, $newLogoHeight);
               $logoFile = $imageFile->getName();
               $logoType = PartnerDetailModel::LOGO_IMAGE_TYPE;
               $logoWidth = self::LOGO_IMAGE_WIDTH;
               $logoHeight = self::LOGO_IMAGE_HEIGHT;
            } else if($flashFile->isFlash(false)){
               $flashFile->copy($this->getModule()->getDir()->getDataDir());
               $logoFile = $flashFile->getName();
               $logoType = PartnerDetailModel::LOGO_FLASH_TYPE;
               $logoWidth = $flashFile->getWidth();
               $logoHeight = $flashFile->getHeight();
            } else {
               $fileError = true;
               $this->errMsg()->addMessage(_('Nebyl zadán korektní typ souboru'));
            }
         }

         // dogenerování url adresy
         $url = $this->validateUrl($addForm->getValue(self::FORM_URL));

//         Uložení do modelu
         if(!$fileError AND $partnerModel->saveNewPartner($addForm->getValue(self::FORM_NAME),
               $addForm->getValue(self::FORM_LABEL), $url,
               $logoFile, $logoType, $logoWidth, $logoHeight)){
            $this->infoMsg()->addMessage(_('Partner byl uložen'));
            $this->getLink()->action()->reload();
         } else {
            new CoreException(_('Chyba při ukládání partnera'),1);
         }
      }

      $this->container()->addData('PARTNER_DATA', $addForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $addForm->getErrorItems());
	}
	
	/**
	 * Controler pro úpravu spozora
	 */
   public function editpartnerController() {
      $this->checkWritebleRights();

      $partnerDetailModel = new PartnerDetailModel();

      $editForm = new Form(self::FORM_PREFIX);

      $editForm->crInputText(self::FORM_NAME, true)
      ->crTextArea(self::FORM_LABEL, false, true, Form::CODE_HTMLDECODE)
      ->crInputText(self::FORM_URL, false, false, Form::VALIDATE_URL)
      ->crInputFile(self::FORM_LOGO_FILE)
      ->crInputHidden(self::FORM_ID, true)
      ->crInputCheckboxn(self::FORM_DELETE_IMAGE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      // Naplnění dat
      $partnerDetailModel->getPartnerDetailAllLangs($this->getArticle()->getArticle());
      // Nastavení hodnot prvků
      $editForm->setValue(self::FORM_NAME, $partnerDetailModel->getName());
      $editForm->setValue(self::FORM_LABEL, $partnerDetailModel->getLabelsLangs());
      $editForm->setValue(self::FORM_URL, $partnerDetailModel->getUrl());
      $editForm->setValue(self::FORM_ID, $partnerDetailModel->getId());

      $lname = $partnerDetailModel->getLabelsLangs();
      $this->container()->addData('PARTNER_NAME', $partnerDetailModel->getName());

      $this->container()->addData('PARTNER_LOGO_FILE', $partnerDetailModel->getFileImage());

      //Kontrola formuláře
      if($editForm->checkForm()){
         $fileOk = true;
         $partnerModel = new PartnerDetailModel();

         $logoFile = null;
         $logoType = null;
         $logoWidth = 0;
         $logoHeight = 0;

         // Smazání původního obrázkzku
         if($partnerDetailModel->getFileImage() != null AND ($editForm->getValue(self::FORM_DELETE_IMAGE)
               OR $editForm->getValue(self::FORM_LOGO_FILE)) != null){
            $deleteFile = new File($partnerDetailModel->getFileImage(),
               $this->getModule()->getDir()->getDataDir());
            $deleteFile->remove();
            unset ($deleteFile);
            // vymaz z modelu
            $partnerDetailModel->saveEditPartnerFile($editForm->getValue(self::FORM_ID));
         }

         // Kontrola jestli bylo uloženo logo
         if($editForm->getValue(self::FORM_LOGO_FILE) != null){
            // Kontrola jestli nebyl uložen obrázek
            $imageFile = new ImageFile($editForm->getValue(self::FORM_LOGO_FILE));
            $flashFile = new FlashFile($editForm->getValue(self::FORM_LOGO_FILE));
            if($imageFile->isImage(false)){
               $this->getModule()->getParam(self::PARAM_LOGO_WIDTH) == null ?
               $newLogoWidth = self::LOGO_IMAGE_WIDTH :
               $newLogoWidth = $this->getModule()->getParam(self::PARAM_LOGO_WIDTH);

               $this->getModule()->getParam(self::PARAM_LOGO_HEIGHT) == null ?
               $newLogoHeight = self::LOGO_IMAGE_HEIGHT :
               $newLogoHeight = $this->getModule()->getParam(self::PARAM_LOGO_HEIGHT);

               $imageFile->saveImage($this->getModule()->getDir()->getDataDir(),
                  $newLogoWidth, $newLogoHeight);
               // uložení do modelu
               $fileOk = $partnerDetailModel->saveEditPartnerFile($editForm->getValue(self::FORM_ID),
                  $imageFile->getName(), PartnerDetailModel::LOGO_IMAGE_TYPE,
                  self::LOGO_IMAGE_WIDTH, self::LOGO_IMAGE_HEIGHT);
            } else if($flashFile->isFlash(false)){
               $flashFile->copy($this->getModule()->getDir()->getDataDir());
               // uložení do modelu
               $fileOk = $partnerDetailModel->saveEditPartnerFile($editForm->getValue(self::FORM_ID),
                  $flashFile->getName(), PartnerDetailModel::LOGO_FLASH_TYPE,
                  $flashFile->getWidth(), $flashFile->getHeight());
            } else {
               $fileOk = false;
               $this->errMsg()->addMessage(_('Nebyl zadán korektní typ souboru'));
            }

         }

         // dogenerování url adresy
         $url = $this->validateUrl($editForm->getValue(self::FORM_URL));

         //         Uložení do modelu
         if($fileOk AND $partnerModel->saveEditPartner($editForm->getValue(self::FORM_NAME),
               $editForm->getValue(self::FORM_LABEL), $editForm->getValue(self::FORM_ID),
               $url)){
            $this->infoMsg()->addMessage(_('Partner byl uložen'));
            $this->getLink()->action()->article()->reload();
         } else {
            new CoreException(_('Chyba při ukládání partnera'),1);
         }
      }

      $this->container()->addData('PARTNER_DATA', $editForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $editForm->getErrorItems());

      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }
	
	/**
	 * Metoda vymaže zadaného sponzora
	 */
	private function deletePartner() {
      $deleteForm = new Form(self::FORM_PREFIX);
      
      $deleteForm->crSubmit(self::FORM_BUTTON_DELETE)
      ->crInputHidden(self::FORM_ID, true, 'is_numeric');

      if($deleteForm->checkForm()){
         $partnerModel = new PartnerDetailModel();

         // smazání loga a partnera
         $file = new File($partnerModel->getFileImage($deleteForm->getValue(self::FORM_ID)),
            $this->getModule()->getDir()->getDataDir());
         if($file->remove() AND $partnerModel->deletePartner($deleteForm->getValue(self::FORM_ID))){
            $this->infoMsg()->addMessage(_('Partner byl smazán'));
            $this->getLink()->reload();
         } else {
            new CoreException(_('Partnera se nepodařilo smazat'));
         }
      }
   }


   private function validateUrl($url) {
      $isValidUrl = false;
      foreach ($this->validProtocols as $protocol) {
         if(substr($url, 0, strlen($protocol)) == $protocol){
            $isValidUrl = true;
            break;
         }
      }

      if(!$isValidUrl){
         $url = $this->validProtocols[0].'://'.$url;
      }

      return $url;
   }
}

?>