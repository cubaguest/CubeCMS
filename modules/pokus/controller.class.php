<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Pokus_Controller extends Controller {
	public function mainController() {
      $form = new Form('pokus_');

      $form->addGroup('hlavni', $this->_('Hlavní skupina'),
         $this->_('Tak tady jsou hlavní volby pro vyplnění formuláře. Všechny volby jsou povinné'));
      $textElementL = new Form_Element_Text('popisek', $this->_('Popisek'));
//      $textElementL->addValidation(new Form_Validator_NotEmpty($this->_('A co popisek voe, ne?'), Locale::getDefaultLang()));
//      $textElementL->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $textElementL->addValidation(new Form_Validator_NotEmpty(_('všechny popisky')));
//      $textElementL->addValidation(new Form_Validator_Length(5, 10));
      $textElementL->setLangs();
//      $textElementL->addValidation(new Form_Validator_Email());
      $form->addElement($textElementL,'hlavni');

//      $file = new Form_Element_File('file', $this->_('Obrázek'));
//      $file->setLangs();
//      $file->addValidation(new Form_Validator_FileMimeType('image/jpeg', 'Nebyl odeslán obrázek'));
//      $file->addValidation(new Form_Validator_FileMimeType('image/jpeg', 'Nebyl odeslán obrázek'));
//      $file->addValidation(new Form_Validator_FileSize(100));
//      $form->addElement($file);

      $textArea = new Form_Element_TextArea('text', $this->_('Text'));
      $textArea->setLangs();
      $form->addElement($textArea);


//      $textElementN = new Form_Element_Text('name', $this->_('Jméno'));
//      $textElementN->setValues('testting');
//      $form->addElement($textElementN);

//      $textElementMail = new Form_Element_Text('mail', $this->_('E-mail'));
//      $textElementMail->addValidation(new Form_Validator_NotEmpty());
//      $textElementMail->addValidation(new Form_Validator_Email());
//      $form->addElement($textElementMail);

//      $textElementSN = new Form_Element_Text('surname', $this->_('Přijmení'));
//      $textElementSN->addValidation(new Form_Validator_NotEmpty());
//      $form->addElement($textElementSN);

//      $form->addGroup('vedlejsi', $this->_('Vedlejší skupina'),
//         $this->_('Tak tady jsou vedlejší volby pro vyplnění formuláře. Tyto volby není nutné vyplňovat'));
//
//      $textElementSel = new Form_Element_Select('city', $this->_('Město'));
//      $textElementSel->setLangs();
//      $textElementSel->setOptions(array('Brno'=>'br', 'Praha'=>'pr', "ValMez"=>'vm', 'Jičín'=>'jn',
//         'Vesnice' => array('Paseky'=>'psk','Bynina'=>'by', 'Poličná'=>'po')));
//      $form->addElement($textElementSel, 'vedlejsi');

//      $textElementRadio = new Form_Element_Radio('city2', $this->_('Město'));
//      $textElementRadio->setOptions(array('Brno'=>'br', 'Praha'=>'pr', "ValMez"=>'vm','Jičín'=>'jn'));
//      $textElementRadio->setValues('vm');
//      $form->addElement($textElementRadio);

//      $textElementCheckBox = new Form_Element_Checkbox('radio', $this->_('Souhlasím s podmínkami'));
//      $textElementCheckBox->addValidation(new Form_Validator_NotEmpty($this->_('Musíte souhlasit s podmínkami')));
//      $form->addElement($textElementCheckBox);

//      $textElementPassword = new Form_Element_Password('passwrd', $this->_('Heslo'));
//      $textElementPassword->setLangs();
//      $textElementPassword->addValidation(new Form_Validator_NotEmpty());
//      $textElementPassword->addValidation(new Form_Validator_Length(5,null));
//      $form->addElement($textElementPassword);

      // druhý element ve skupině
      $textElement2 = new Form_Element_Text('popisek2', $this->_('Popisek2'));
      $textElement2->setLangs();
      $form->addElement($textElement2,'hlavni');


      $form->addElement(new Form_Element_Submit('send', $this->_('Odeslat')));

      if($form->isValid()){
//         var_dump($form->file->getValues());
         $this->infoMsg()->addMessage('odeslano');
         $this->link()->reload();
      }

      $this->view()->form = $form;

//      var_dump($_FILES);
	}

	
}

?>