<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class DwfilesController extends Controller {
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'dwfiles_';
	const FORM_BUTTON_SEND = 'send';
	const FORM_FILE_LABEL = 'label';
	const FORM_FILE = 'file';

   /**
    * Prefix pro soubor
    */
   const FILE_DIR_PREFIX = 'dirprefix';

   /**
    * Link pro stažení
    */
   const DOWNLOAD_FILE_link = 'download_file';

	/**
	 * Kontroler pro zobrazení textu
	 */
	public function mainController() {
//		Kontrola práv
		$this->checkReadableRights();
//		Model pro načtení souborů
		$fileM = new FilesDetailModel();
      $dwFiles = $fileM->getDwFiles();
//		pokud má uživatel právo zápisu vytvoříme odkaz pro editaci
		if($this->getRights()->isWritable()){
			$this->container()->addLink('LINK_TO_ADD_FILE', $this->getLink()->action($this->getAction()->addFile()));
		}

      // doplnění odkazu pro stažení
      foreach ($dwFiles as $key => $file) {
         $dwFiles[$key][self::DOWNLOAD_FILE_link] = Links::getLinkToDwFile($this->getModule()
            ->getDir()->getDataDir(false), $file[FilesDetailModel::COLUMN_FILE_NAME]);
      }

      $this->container()->addData('DIR', $this->getModule()->getDir()->getDataDir(false));
      $this->container()->addData('DWFILES', $dwFiles);
	}

	/**
	 * Kontroler pro editaci textu
	 */
	public function addfileController() {
		$this->checkWritebleRights();

      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX);

      $form->crSubmit(self::FORM_BUTTON_SEND)
      ->crTextArea(self::FORM_FILE_LABEL, false, true)
      ->crInputFile(self::FORM_FILE, true);

 //        Pokud byl odeslán formulář
      if($form->checkForm()){
         $filesM = new FilesDetailModel();

         $file = new File($form->getValue(self::FORM_FILE));

         if($file->copy($this->getModule()->getDir()->getDataDir()) AND
            $filesM->saveNewFile($file->getName(), $form->getValue(self::FORM_FILE_LABEL))){
               $this->infoMsg()->addMessage(_('Soubor byl uložen'));
               $this->getLink()->action()->reload();
         } else {
            new CoreException(_('Soubor se nepodařilo uložit, chyba při ukládání.'), 1);
         }
      }
//    Data do šablony
      $this->container()->addData('FILE_DATA', $form->getValues());
      $this->container()->addData('ERROR_ITEMS', $form->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
	}
}

?>