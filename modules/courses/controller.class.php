<?php
class Courses_Controller extends Controller {
   const PAY_TYPE_ORGANISATION = 'organisation';
   const PAY_TYPE_PRIVATE = 'private';

   const DEFAULT_IMG_WIDTH = 300;
   const DEFAULT_IMG_HEIGHT = 225;

   const DATA_DIR = 'courses';

   protected function init() {
      parent::init();
      $this->category()->getModule()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      $this->checkReadableRights();

      $model = new Courses_Model_Courses();
      $this->view()->courses = $model->getCoursesFromDate(new DateTime());

   }

   public function listAllCoursesController() {
      $this->checkReadableRights();
      $model = new Courses_Model_Courses();
      $this->view()->courses = $model->getCourses();
   }

   public function archiveController() {
      $this->checkReadableRights();
      $m = new Articles_Model_List();
      $articlesAll = $m->getListAll($this->category()->getId());
      $articles = array();

      while ($row = $articlesAll->fetch()){
         $date = new DateTime($row->{Articles_Model_Detail::COLUMN_ADD_TIME});
         $year = $date->format("Y");
         if(!isset ($articles[$year])){
            $articles[$year] = array();
         }
         array_push($articles[$year], $row);
      }

      $this->view()->articles = $articles;
      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);
   }

   public function showCourseController() {
      $this->checkReadableRights();

      $courseModel = new Courses_Model_Courses();
      $this->view()->course = $courseModel->getCourse($this->getRequest('urlkey'));
      if($this->view()->course == false)
         return false;

      $regModel = new Courses_Model_Registrations();
      // registrace
      $this->view()->countReg = $regModel->getCountRegistrations($this->view()->course->{Courses_Model_Courses::COLUMN_ID});

      $this->view()->freeSeats = $this->view()->course->{Courses_Model_Courses::COLUMN_SEATS}
         - $this->view()->course->{Courses_Model_Courses::COLUMN_SEATS_BLOCKED} - $this->view()->countReg;

      // lektoři
      $this->view()->lecturers = $courseModel->getLecturers($this->view()->course->{Courses_Model_Courses::COLUMN_ID});

      // pokud je volno přidáme registraci
      if($this->view()->freeSeats > 0) {
         $this->courseRegistration();
      }

      if($this->category()->getRights()->isWritable()){
         $formDelete = new Form('course_delete_');
         
         $eId = new Form_Element_Hidden('id');
         $eId->setValues($this->view()->course->{Courses_Model_Courses::COLUMN_ID});
         $formDelete->addElement($eId);

         $eDelete = new Form_Element_SubmitImage('delete', $this->_('Smazat kurz'));
         $formDelete->addElement($eDelete);

         if($formDelete->isValid()){
            $courseModel->deleteCourse($formDelete->id->getValues());

            $this->infoMsg()->addMessage($this->_('Kurz byl smazán'));
            $this->link()->route()->reload();

         }
         $this->view()->formDelete = $formDelete;
      }

   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addCourseController() {
      $this->checkWritebleRights();
      $addForm = $this->createCourseForm();

      if($addForm->isValid()) {
         $model = new Courses_Model_Courses();

         // uložení nového obrázku
         $imgName = null;
         if($addForm->image->getValues()){
            $image = $addForm->image->createFileObject('Filesystem_File_Image');

            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMG_WIDTH),
                    $this->category()->getParam('imgh', self::DEFAULT_IMG_HEIGHT),false);
            $image->save();
            $imgName = $image->getName();
         }

         $urlkeys = $addForm->urlkey->getValues();
         $names = $addForm->name->getValues();

         $idC = $model->saveCourse($addForm->name->getValues(), $addForm->textShort->getValues(),
                 $addForm->text->getValues(), $addForm->urlkey->getValues(),
                 $addForm->dateStart->getValues(), $addForm->dateStop->getValues(),
                 $addForm->price->getValues(), $addForm->hourseLen->getValues(), $addForm->place->getValues(),
                 $addForm->seats->getValues(), $addForm->seatsBlocked->getValues(),
                 $addForm->isNew->getValues(), $addForm->allowReg->getValues(),
                 $imgName, $addForm->lecturers->getValues());

         $newCours = $model->getCourseById($idC);

         $this->infoMsg()->addMessage($this->_('Kurz byl uložen'));
         $this->link()->route('detail',array('urlkey' => $newCours->{Courses_Model_Courses::COLUMN_URLKEY}))->reload();
      }

      $this->view()->form = $addForm;
      $this->view()->edit = false;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editCourseController() {
      $this->checkWritebleRights();

      $editForm = $this->createCourseForm();

      // načtení dat
      $model = new Courses_Model_Courses();
      $course = $model->getCourse($this->getRequest('urlkey'));

      $editForm->name->setValues($course->{Courses_Model_Courses::COLUMN_NAME});
      $editForm->textShort->setValues($course->{Courses_Model_Courses::COLUMN_TEXT_SHORT});
      $editForm->text->setValues($course->{Courses_Model_Courses::COLUMN_TEXT});
      $dateS = new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_START});
      $editForm->dateStart->setValues(strftime("%x",$dateS->format("U")));
      if($course->{Courses_Model_Courses::COLUMN_DATE_STOP} != null){
         $dateS = new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_STOP});
         $editForm->dateStop->setValues(strftime("%x",$dateS->format("U")));
      }
      $editForm->price->setValues($course->{Courses_Model_Courses::COLUMN_PRICE});
      $editForm->hourseLen->setValues($course->{Courses_Model_Courses::COLUMN_HOURS_LEN});
      $editForm->seats->setValues($course->{Courses_Model_Courses::COLUMN_SEATS});
      $editForm->seatsBlocked->setValues($course->{Courses_Model_Courses::COLUMN_SEATS_BLOCKED});
      $editForm->place->setValues($course->{Courses_Model_Courses::COLUMN_PLACE});

      // načtení lektorů
      $lecturers = $model->getLecturers($course->{Courses_Model_Courses::COLUMN_ID});
      $idl = array();
      foreach ($lecturers as $l)
         array_push ($idl, $l->{Courses_Model_Courses::COLUMN_L_H_C_ID_LECTURER});

      $editForm->lecturers->setValues($idl);
      $editForm->urlkey->setValues($course->{Courses_Model_Courses::COLUMN_URLKEY});
      $editForm->allowReg->setValues($course->{Courses_Model_Courses::COLUMN_ALLOW_REG});
      $editForm->isNew->setValues($course->{Courses_Model_Courses::COLUMN_IS_NEW});

      // element pro smazání obrázku

      // doplnění id
      $iIdElem = new Form_Element_Hidden('id');
      $iIdElem->addValidation(new Form_Validator_IsNumber());
      $iIdElem->setValues($course->{Courses_Model_Courses::COLUMN_ID});
      $editForm->addElement($iIdElem);

      // přidání elementu pro odstranění obrázku
      $this->view()->courseImage = $course->{Courses_Model_Courses::COLUMN_IMAGE};
      if($this->view()->courseImage != null){
         $eDelteImg = new Form_Element_Checkbox('deleteImg', $this->_('Smazat uložený obrázek'));
         $editForm->addElement($eDelteImg,'other');
      }

      if($editForm->isValid()) {
         // smazání obrázku
         $imgName = $course->{Courses_Model_Courses::COLUMN_IMAGE};
         if($imgName != null AND ($editForm->image->getValues() != null OR $editForm->deleteImg->getValues() == true)){
            $file = new Filesystem_File($course->{Courses_Model_Courses::COLUMN_IMAGE}, AppCore::getAppDataDir().self::DATA_DIR.DIRECTORY_SEPARATOR);
            $file->delete();
            $imgName = null;
         }

         // uložení nového obrázku
         if($editForm->image->getValues()){
            $image = $editForm->image->createFileObject('Filesystem_File_Image');
            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMG_WIDTH),
                    $this->category()->getParam('imgh', self::DEFAULT_IMG_HEIGHT),false);
            $image->save();
            $imgName = $image->getName();
         }

         // generování url klíče
         $urlkeys = $editForm->urlkey->getValues();
         $names = $editForm->name->getValues();

         $model->saveCourse($editForm->name->getValues(), $editForm->textShort->getValues(),
                 $editForm->text->getValues(), $editForm->urlkey->getValues(),
                 $editForm->dateStart->getValues(), $editForm->dateStop->getValues(),
                 $editForm->price->getValues(), $editForm->hourseLen->getValues(), $editForm->place->getValues(),
                 $editForm->seats->getValues(), $editForm->seatsBlocked->getValues(),
                 $editForm->isNew->getValues(), $editForm->allowReg->getValues(),
                 $imgName, $editForm->lecturers->getValues(), $course->{Courses_Model_Courses::COLUMN_ID});

         $newCours = $model->getCourseById($course->{Courses_Model_Courses::COLUMN_ID});

         $this->infoMsg()->addMessage($this->_('Kurz byl uložen'));
         $this->link()->route('detailCourse',array('urlkey' => $newCours->{Courses_Model_Courses::COLUMN_URLKEY}))->reload();

         // generování url klíče
//         $urlkeys = $editForm->urlkey->getValues();
//         $names = $editForm->name->getValues();
//         $urlkeys = $this->createUrlKey($urlkeys, $names);
//
//         $id = $this->saveArticle($names, $urlkeys, $editForm, $course);
//         // nahrání nové verze článku (kvůli url klíči)
//         $newCours = $model->getCourseById($id);
//
//         $this->link()->route('detail',array('urlkey' => $newCours->{Courses_Model_Courses::COLUMN_URLKEY}))->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->edit = true;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createCourseForm() {
      $form = new Form('course_');

      $fGrpTexts = $form->addGroup('texts', $this->_('Texty'));

      $iName = new Form_Element_Text('name', $this->_('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, $fGrpTexts);

      $iTextShort = new Form_Element_TextArea('textShort', $this->_('Úvodní text'));
      $iTextShort->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iTextShort, $fGrpTexts);

      $iText = new Form_Element_TextArea('text', $this->_('Text'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, $fGrpTexts);

      $fGrpParams = $form->addGroup('params', $this->_('Parametry'));

      $eDateStart = new Form_Element_Text('dateStart', $this->_('Datum začátku'));
      $eDateStart->addValidation(new Form_Validator_NotEmpty());
      $eDateStart->addValidation(new Form_Validator_Date());
      $eDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateStart, $fGrpParams);

      $eDateStart = new Form_Element_Text('dateStop', $this->_('Datum konce'));
      $eDateStart->addValidation(new Form_Validator_Date());
      $eDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateStart, $fGrpParams);

      $ePrice = new Form_Element_Text('price', $this->_('Cena (Kč)'));
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice, $fGrpParams);

      $eHoursLeng = new Form_Element_Text('hourseLen', $this->_('Délka (hodin)'));
      $eHoursLeng->addValidation(new Form_Validator_IsNumber());
      $form->addElement($eHoursLeng, $fGrpParams);

      $eSeats = new Form_Element_Text('seats', $this->_('Počet míst'));
      $eSeats->addValidation(new Form_Validator_IsNumber());
      $form->addElement($eSeats, $fGrpParams);

      $eSeatsBlocked = new Form_Element_Text('seatsBlocked', $this->_('Počet blokovaných míst'));
      $eSeatsBlocked->addValidation(new Form_Validator_IsNumber());
      $eSeatsBlocked->setValues(0);
      $form->addElement($eSeatsBlocked, $fGrpParams);

      $ePlace = new Form_Element_Text('place', $this->_('Místo konání'));
      $ePlace->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($ePlace, $fGrpParams);

      $eLecturers = new Form_Element_Select('lecturers', $this->_('Lektoři'));
      $eLecturers->setMultiple(true);

      $modelLecturers = new Lecturers_Model();
      $l = $modelLecturers->getList();
      foreach ($l as $lecturer) {
         $eLecturers->setOptions(array($lecturer->{Lecturers_Model::COLUMN_DEGREE}
         .' '.$lecturer->{Lecturers_Model::COLUMN_NAME}
         .' '.$lecturer->{Lecturers_Model::COLUMN_SURNAME} => $lecturer->{Lecturers_Model::COLUMN_ID}), true);
      }
      $eLecturers->addValidation(new Form_Validator_NotEmpty($this->_('Musí být vybrán alespoň jeden lektor')));
      $form->addElement($eLecturers, $fGrpParams);

      $fGrpOther = $form->addGroup('other', $this->_('Ostatní'));

      $iUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $iUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky z názvu kurzu'));
      $form->addElement($iUrlKey, $fGrpOther);

      $eAllowReg = new Form_Element_Checkbox('allowReg', $this->_('Povolit registraci'));
      $eAllowReg->setValues(true);
      $form->addElement($eAllowReg, $fGrpOther);

      $eIsNew = new Form_Element_Checkbox('isNew', $this->_('Označit kurz jako Nový'));
      $form->addElement($eIsNew, $fGrpOther);

      $eImage = new Form_Element_File('image', $this->_('Obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
      $eImage->setUploadDir($this->category()->getModule()->getDataDir());
      $form->addElement($eImage, $fGrpOther);


      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }

   private function courseRegistration() {
      $idc = $this->view()->course->{Courses_Model_Courses::COLUMN_ID};

      $regForm = new Form('course_reg_');
      $basicGrp = $regForm->addGroup('basic', $this->_('Základní informace'));

      $eName = new Form_Element_Text('name', $this->_('Jméno'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eName, $basicGrp);

      $eSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $eSurName->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eSurName, $basicGrp);

      $eDegree = new Form_Element_Text('degree', $this->_('Titul'));
      $regForm->addElement($eDegree, $basicGrp);

      $eGrade = new Form_Element_Text('grade', $this->_('Pracovní zařazení'));
      $eGrade->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eGrade, $basicGrp);
      
      $ePracticeLenght = new Form_Element_Text('practiceLength', $this->_('Délka praxe'));
      $ePracticeLenght->addValidation(new Form_Validator_NotEmpty());
      $ePracticeLenght->addValidation(new Form_Validator_IsNumber());
      $regForm->addElement($ePracticeLenght, $basicGrp);
      
      $ePhone = new Form_Element_Text('phone', $this->_('Telefon'));
      $ePhone->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($ePhone, $basicGrp);
      
      $eMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $eMail->addValidation(new Form_Validator_NotEmpty());
      $eMail->addValidation(new Form_Validator_Email());
      $regForm->addElement($eMail, $basicGrp);

      $eRegNewsLetter = new Form_Element_Checkbox('regNewsletter', $this->_('Registrovat k odběru novinek e-mailem'));
      $eRegNewsLetter->setValues(true);
      $regForm->addElement($eRegNewsLetter, $basicGrp);

      $eNote = new Form_Element_TextArea('note', $this->_('Poznámka'));
      $regForm->addElement($eNote, $basicGrp);

      $payGroup = $regForm->addGroup('pay', $this->_('Fakturační údaje'));

      $ePay = new Form_Element_Radio('payType', $this->_('Kurz hrazen'));
      $payTypes = array($this->_('Organizací') => self::PAY_TYPE_ORGANISATION,
          $this->_('Soukromně') => self::PAY_TYPE_PRIVATE);
      $ePay->setOptions($payTypes);
      $regForm->addElement($ePay, $payGroup);

      $eOrgName = new Form_Element_Text('orgName', $this->_('Název'));
      $regForm->addElement($eOrgName, $payGroup);

      $eOrgAddress = new Form_Element_TextArea('orgAddress', $this->_('Adresa'));
      $regForm->addElement($eOrgAddress, $payGroup);

      $eOrgICO = new Form_Element_Text('orgICO', $this->_('IČO'));
      $regForm->addElement($eOrgICO, $payGroup);

      $eOrgPhone = new Form_Element_Text('orgPhone', $this->_('Telefon'));
      $regForm->addElement($eOrgPhone, $payGroup);

      $ePrivateAddress = new Form_Element_TextArea('privateAddress', $this->_('Adresa'));
      $regForm->addElement($ePrivateAddress, $payGroup);

      $eSend = new Form_Element_Submit('send', $this->_('Odeslat'));
      $regForm->addElement($eSend);

      if($regForm->isSend()){
         if($regForm->payType->getValues() == 'organisation'){
            $regForm->orgName->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgAddress->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgICO->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgPhone->addValidation(new Form_Validator_NotEmpty());
         } else {
            $regForm->privateAddress->addValidation(new Form_Validator_NotEmpty());
         }
      }

      if($regForm->isValid()){
         $model = new Courses_Model_Registrations();

         $model->saveRegistration($idc, $regForm->name->getValues(), $regForm->surname->getValues(),
                 $regForm->degree->getValues(), $regForm->grade->getValues(), 
                 $regForm->practiceLength->getValues(), $regForm->phone->getValues(),
                 $regForm->mail->getValues(), $regForm->note->getValues(),
                 $regForm->payType->getValues(), $regForm->orgName->getValues(),
                 $regForm->orgAddress->getValues(), $regForm->orgICO->getValues(),
                 $regForm->orgPhone->getValues(), $regForm->privateAddress->getValues());

         // pokud je registrace k newsletteru, přidáme jej
         if($regForm->regNewsletter->getValues() == true){
            $modelNewsLetter = new NewsLetter_Model_Mails();
            $modelNewsLetter->saveMail($regForm->mail->getValues(), $_SERVER['REMOTE_ADDR']);
         }

         // odeslání emailu  s registrací na registrovaného a admina

         $this->infoMsg()->addMessage($this->_('Registace byla uložena. Na Váš e-mail byly odeslány detail rezervace'));
//         $this->link()->reload();
      }

      $this->view()->formReg = $regForm;
   }

   /**
    * Kontroller pro seznam registrovaných ke kurzu
    */
   public function registrationsCourseController() {
      $this->checkWritebleRights();

      $modelC = new Courses_Model_Courses();
      $this->view()->course = $modelC->getCourse($this->getRequest('urlkey'));

      if($this->view()->course == false) return false;

      $modelReg = new Courses_Model_Registrations();

      $formCancelReg = new Form('cancel_reg_');

      $eId = new Form_Element_Hidden('id');
      $eId->addValidation(new Form_Validator_IsNumber());
      $formCancelReg->addElement($eId);

      $eSubmit = new Form_Element_SubmitImage('submit');
      $formCancelReg->addElement($eSubmit);

      if($formCancelReg->isValid()){
         $reg = $modelReg->getRegistration($formCancelReg->id->getValues());


         $modelReg->cancelRegistration($formCancelReg->id->getValues());

         $this->infoMsg()->addMessage(sprintf($this->_('Registrace na jméno %s,%s byla zrušena'),
                 $reg->{Courses_Model_Registrations::COLUMN_SURNAME}, 
                 $reg->{Courses_Model_Registrations::COLUMN_NAME} ));
                 
         $this->link()->reload();
      }
      $this->view()->formCancel = $formCancelReg;

      $this->view()->registrations = $modelReg->getRegistrations($this->view()->course->{Courses_Model_Courses::COLUMN_ID});

   }



   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
   }

   /**
    * Kontroler pro seznam uložených míst
    */
   public function placesListController() {
      $model = new Courses_Model_Places();
      $searched = $this->getRequestParam('term', null);
      $this->view()->places = $model->getPlaces($searched);
   }


   /**
    * Metoda pro nastavení modulu
    */
//   public static function settingsController(&$settings,Form &$form) {
//   }
}
?>