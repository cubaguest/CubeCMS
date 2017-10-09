<?php

class Courses_Controller extends Controller {

   const PAY_TYPE_ORGANISATION = 'organisation';
   const PAY_TYPE_PRIVATE = 'private';
   
   const TYPE_COURSE = 'kurz';
   const TYPE_CONFERENTION = 'konf';
   const TYPE_SEMINARE = 'semin';
   
   const PARAM_SEND_ADMIN_NOTIF_DEFAULT = true;
   const PARAM_SEND_ADMIN_NOTIF = 'admin_notif';
   const PARAM_ADMINS = 'admins';
   const PARAM_OTHER_RECIPIENS = 'other_recipients';
   const PARAM_NEWSLETTER_MAIL_GRP = 'newsletter_mail_grp';
   const PARAM_CONTACT_CAT = 'cat_c';
   
   const DEFAULT_IMG_WIDTH = 400;
   const DEFAULT_IMG_HEIGHT = 300;
   const DATA_DIR = 'courses';

   protected function init()
   {
      parent::init();
      $this->category()->getModule()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController()
   {
      $this->checkReadableRights();

      $model = new Courses_Model();
      $this->view()->courses = $model->getCoursesFromDate(new DateTime(), !$this->category()->getRights()->isWritable());
   }

   public function listAllCoursesController()
   {
      $this->checkReadableRights();
      $model = new Courses_Model();
      $this->view()->courses = $model->getCourses(!$this->category()->getRights()->isWritable());
   }

   public function showCourseController($urlkey)
   {
      $this->checkReadableRights();

      $courseModel = new Courses_Model();
      $this->view()->course = $courseModel->getCourse($urlkey);
      if ($this->view()->course == false) {
         return false;
      }

      $regModel = new Courses_Model_Registrations();
      // registrace
      $this->view()->countReg = $regModel->getCountRegistrations($this->view()->course->{Courses_Model::COLUMN_ID});

      $this->view()->freeSeats = $this->view()->course->{Courses_Model::COLUMN_SEATS} - $this->view()->course->{Courses_Model::COLUMN_SEATS_BLOCKED} - $this->view()->countReg;

      // lektoři
      $this->view()->lecturers = $this->view()->course->getLecturers();

      // pokud je volno přidáme registraci
      if ($this->view()->course->{Courses_Model::COLUMN_ALLOW_REG}
              AND $this->view()->freeSeats > 0
              AND strtotime($this->view()->course->{Courses_Model::COLUMN_DATE_START}) > strtotime("now")) {
         $this->courseRegistration();
      }

      // privátní zóna
      $this->view()->isPrivate = false;
      if (Auth::isAdmin() OR $this->view()->course->isPrivateUser(Auth::getUserId()) == true) {
         $this->view()->isPrivate = true;
      }

      if ($this->category()->getRights()->isWritable()) {
         $formDelete = new Form('course_delete_', true);

         $eId = new Form_Element_Hidden('id');
         $eId->setValues($this->view()->course->{Courses_Model::COLUMN_ID});
         $formDelete->addElement($eId);

         $eDelete = new Form_Element_SubmitImage('delete', $this->tr('Smazat položku'));
         $formDelete->addElement($eDelete);

         if ($formDelete->isValid()) {
            (new Courses_Model)->delete($formDelete->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
            $this->link()->route()->reload();
         }
         $this->view()->formDelete = $formDelete;
      }
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addCourseController()
   {
      $this->checkWritebleRights();
      $addForm = $this->createCourseForm();

      if ($addForm->isSend() AND $addForm->save->getValues() == false) {
         $this->link()->route()->reload();
      }

      if ($addForm->isValid()) {
         $model = new Courses_Model();

         // uložení nového obrázku


         $course = $this->processCourseForm($addForm, Courses_Model::getNewRecord());

         $newCours = $model->getCourseById($course->getPK());

         $this->infoMsg()->addMessage($this->tr('Položka byla uložena'));
         $this->link()->route('detail', array('urlkey' => $newCours->{Courses_Model::COLUMN_URLKEY}))->reload();
      }

      $this->view()->form = $addForm;
      $this->view()->edit = false;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editCourseController($urlkey)
   {
      $this->checkWritebleRights();
      $m = new Courses_Model();
      $course = $m->getCourse($urlkey);
      if (!$course) {
         return false;
      }

      $editForm = $this->createCourseForm($course);

      if ($editForm->isSend() AND $editForm->save->getValues() == false) {
         $this->link()->route('detailCourse')->reload();
      }

      if ($editForm->isValid()) {

         $course = $this->processCourseForm($editForm, $course);

         $this->infoMsg()->addMessage($this->tr('Položky byla uložena'));
         $this->link()->route('detailCourse', array('urlkey' => $course->{Courses_Model::COLUMN_URLKEY}))->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->course = $course;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createCourseForm(Model_ORM_Record $course = null)
   {
      $form = new Form('course_', true);

      $fGrpTexts = $form->addGroup('texts', $this->tr('Texty'));

      $iName = new Form_Element_Text('name', $this->tr('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, $fGrpTexts);

      $iTextShort = new Form_Element_TextArea('textShort', $this->tr('Úvodní text'));
      $iTextShort->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iTextShort, $fGrpTexts);

      $iText = new Form_Element_TextArea('text', $this->tr('Text'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, $fGrpTexts);

      $fGrpParams = $form->addGroup('params', $this->tr('Parametry'));

      $eDateStart = new Form_Element_Text('dateStart', $this->tr('Datum začátku'));
      $eDateStart->addValidation(new Form_Validator_NotEmpty());
      $eDateStart->addValidation(new Form_Validator_Date());
      $eDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateStart, $fGrpParams);

      $eDateStart = new Form_Element_Text('dateStop', $this->tr('Datum konce'));
      $eDateStart->addValidation(new Form_Validator_Date());
      $eDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateStart, $fGrpParams);

      $eTimeStart = new Form_Element_Text('timeStart', $this->tr('Předpokládaný čas začátku'));
      $eTimeStart->addValidation(new Form_Validator_Time());
      $form->addElement($eTimeStart, $fGrpParams);

      $ePrice = new Form_Element_Text('price', $this->tr('Cena (Kč)'));
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice, $fGrpParams);

      $eHoursLeng = new Form_Element_Text('hourseLen', $this->tr('Délka (hodin)'));
      $eHoursLeng->addValidation(new Form_Validator_IsNumber());
      $form->addElement($eHoursLeng, $fGrpParams);

      $eSeats = new Form_Element_Text('seats', $this->tr('Počet míst'));
      $eSeats->addValidation(new Form_Validator_IsNumber());
      $form->addElement($eSeats, $fGrpParams);

      $eSeatsBlocked = new Form_Element_Text('seatsBlocked', $this->tr('Počet blokovaných míst'));
      $eSeatsBlocked->addValidation(new Form_Validator_IsNumber());
      $eSeatsBlocked->setValues(0);
      $form->addElement($eSeatsBlocked, $fGrpParams);

      $ePlace = new Form_Element_Text('place', $this->tr('Místo konání'));
      $ePlace->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($ePlace, $fGrpParams);

      $itargetGroups = new Form_Element_Text('targetGroups', $this->tr('Cílové skupiny'));
      $form->addElement($itargetGroups, $fGrpParams);

      $peoplesRec = People_Model::getAllRecords();
      $eLecturers = new Form_Element_Select('lecturers', $this->tr('Lektoři'));
      $eLecturers->setMultiple(true);
      $eLecturers->addValidation(new Form_Validator_NotEmpty($this->tr('Musí být vybrán alespoň jeden lektor')));

      foreach ($peoplesRec as $rec) {
         $name = ($rec->{People_Model::COLUMN_DEGREE} != null ? $rec->{People_Model::COLUMN_DEGREE} . ' ' : '')
                 . $rec->{People_Model::COLUMN_NAME} . ' ' . $rec->{People_Model::COLUMN_SURNAME}
                 . ($rec->{People_Model::COLUMN_DEGREE_AFTER} != null ? ' ' . $rec->{People_Model::COLUMN_DEGREE_AFTER} : '');
         $eLecturers->addOption($name, $rec->getPK());
      }
      $form->addElement($eLecturers, $fGrpParams);

      $iAkredMPSV = new Form_Element_Text('akredMPSV', $this->tr('Akreditace MPSV'));
      $form->addElement($iAkredMPSV, $fGrpParams);

      $iAkredMSMT = new Form_Element_Text('akredMSMT', $this->tr('Akreditace MŠMT'));
      $form->addElement($iAkredMSMT, $fGrpParams);

      $eAllowReg = new Form_Element_Checkbox('allowReg', $this->tr('Povolit registraci'));
      $eAllowReg->setValues(true);
      $form->addElement($eAllowReg, $fGrpParams);

      $eIsNew = new Form_Element_Checkbox('isNew', $this->tr('Označit jako Nový'));
      $form->addElement($eIsNew, $fGrpParams);

      $eShow = new Form_Element_Checkbox('inList', $this->tr('Zobrazit v seznamu'));
      $eShow->setValues(true);
      $form->addElement($eShow, $fGrpParams);

      $eType = new Form_Element_Select('type', $this->tr('Typ kurzu'));
      $eType->setOptions(array('Kurz' => self::TYPE_COURSE, 'Konference' => self::TYPE_CONFERENTION, 'Seminář' => self::TYPE_SEMINARE));
      $form->addElement($eType, $fGrpParams);

//      $eFiles = new Form_Element_File('files', $this->tr('Připojené soubory'));
//      $eFiles->addValidation(new Form_Validator_FileType(Form_Validator_FileExtension::DOC));
//      $eFiles->setMultiple(true);
//      $form->addElement($eFiles, $fGrpParams);
      // private
      $fGrpPrivate = $form->addGroup('private', $this->tr('Privátní část'), $this->tr('Položky vyditelné pouze určitým uživatelům. Administrátorům jsou tyto informace vždy viditelné.'));

      $ePrivateUsers = new Form_Element_Select('privateUsers', $this->tr('Uživatelé'));
      $ePrivateUsers->setMultiple(true);
      $ePrivateUsers->setAdvanced(true);

      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb()->records(PDO::FETCH_OBJ);
      foreach ($users as $usr) {
         $ePrivateUsers->setOptions(array($usr->{Model_Users::COLUMN_NAME} . " " . $usr->{Model_Users::COLUMN_SURNAME}
             . ' (' . $usr->{Model_Users::COLUMN_USERNAME} . ') - ' . $usr->{Model_Users::COLUMN_GROUP_LABEL} . ' (' . $usr->{Model_Users::COLUMN_GROUP_NAME} . ')'
             => $usr->{Model_Users::COLUMN_ID}), true);
      }
      $form->addElement($ePrivateUsers, $fGrpPrivate);

      $iPrivateText = new Form_Element_TextArea('textPrivate', $this->tr('Text'));
      $iPrivateText->setAdvanced(true);
      $form->addElement($iPrivateText, $fGrpPrivate);

      $fGrpOther = $form->addGroup('other', $this->tr('Ostatní'), $this->tr('Systémová nastavení a nastavení meta tagů.'));

      $iUrlKey = new Form_Element_Text('urlkey', $this->tr('Url klíč'));
      $iUrlKey->setAdvanced(true);
      $iUrlKey->setSubLabel($this->tr('Pokud není klíč zadán, je generován automaticky z názvu kurzu'));
      $form->addElement($iUrlKey, $fGrpOther);

      $eImage = new Form_Element_ImageSelector('image', $this->tr('Obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
      $eImage->setUploadDir($this->category()->getModule()->getDataDir());
      $eImage->setAdvanced(true);

      $form->addElement($eImage, $fGrpOther);

      $iKeywords = new Form_Element_Text('metaKeywords', $this->tr('Klíčová slova'));
      $iKeywords->setSubLabel($this->tr('Pokud nesjou zadány, jsou použiti z kategorie'));
      $iKeywords->setAdvanced(true);
      $form->addElement($iKeywords, $fGrpOther);

      $iDesc = new Form_Element_TextArea('metaDesc', $this->tr('Popisek'));
      $iDesc->setSubLabel($this->tr('Pokud není zadán, je použit z kategorie'));
      $iDesc->setAdvanced(true);
      $form->addElement($iDesc, $fGrpOther);

      $eAllowFeed = new Form_Element_Checkbox('allowFeed', $this->tr('Povolit RSS export'));
      $eAllowFeed->setValues(false);
      $eAllowFeed->setAdvanced(true);
      $form->addElement($eAllowFeed, $fGrpOther);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      if ($course instanceof Model_ORM_Record) {
         /* @var $course Courses_Model_Record */
         // načtení dat
         $form->name->setValues($course->{Courses_Model::COLUMN_NAME});
         $form->textShort->setValues($course->{Courses_Model::COLUMN_TEXT_SHORT});
         $form->text->setValues($course->{Courses_Model::COLUMN_TEXT});
         $form->textPrivate->setValues($course->{Courses_Model::COLUMN_TEXT_PRIVATE});
         $form->dateStart->setValues(Utils_DateTime::fdate('%x', new DateTime($course->{Courses_Model::COLUMN_DATE_START})));
         if ($course->{Courses_Model::COLUMN_DATE_STOP} != null) {
            $form->dateStop->setValues(Utils_DateTime::fdate('%x', new DateTime($course->{Courses_Model::COLUMN_DATE_STOP})));
         }
         $form->price->setValues($course->{Courses_Model::COLUMN_PRICE});
         $form->hourseLen->setValues($course->{Courses_Model::COLUMN_HOURS_LEN});
         $form->seats->setValues($course->{Courses_Model::COLUMN_SEATS});
         $form->seatsBlocked->setValues($course->{Courses_Model::COLUMN_SEATS_BLOCKED});
         $form->place->setValues($course->{Courses_Model::COLUMN_PLACE});

         $form->akredMPSV->setValues($course->{Courses_Model::COLUMN_AKREDIT_MPSV});
         $form->akredMSMT->setValues($course->{Courses_Model::COLUMN_AKREDIT_MSMT});
         $form->targetGroups->setValues($course->{Courses_Model::COLUMN_TAGRT_GROUPS});
         $form->timeStart->setValues($course->{Courses_Model::COLUMN_TIME_START});

         // načtení lektorů
         $lecturers = $course->getLecturers();
         $idl = array();
         foreach ($lecturers as $l) {
            array_push($idl, $l->{Courses_Model::COLUMN_L_H_C_ID_LECTURER});
         }
         $form->lecturers->setValues($idl);

         $form->urlkey->setValues($course->{Courses_Model::COLUMN_URLKEY});
         $form->allowReg->setValues($course->{Courses_Model::COLUMN_ALLOW_REG});
         $form->type->setValues($course->{Courses_Model::COLUMN_TYPE});
         $form->isNew->setValues($course->{Courses_Model::COLUMN_IS_NEW});
         $form->inList->setValues($course->{Courses_Model::COLUMN_IN_LIST});
         $form->metaDesc->setValues($course->{Courses_Model::COLUMN_DESCRIPTION});
         $form->metaKeywords->setValues($course->{Courses_Model::COLUMN_KEYWORDS});
         $form->allowFeed->setValues((bool) $course->{Courses_Model::COLUMN_FEED});
         $form->image->setValues($course->{Courses_Model::COLUMN_IMAGE});

         // přidání uživatelů
         $users = $course->getUsers();
         $selected = array();
         foreach ($users as $user) {
            array_push($selected, $user->{Courses_Model::COLUMN_C_H_U_ID_USER});
         }
         $form->privateUsers->setValues($selected);
      }


      return $form;
   }

   /**
    * Zpracuje form editace kurzu
    * @param Form $form
    * @param Courses_Model_Record $course
    * @return \Model_ORM_Record
    */
   protected function processCourseForm(Form $form, Model_ORM_Record $course)
   {
      $imgName = null;
      if ($form->image->getValues()) {
         $image = $form->image->createFileObject('Filesystem_File_Image');
         $imgName = $image->getName();
      }

      $course->{Courses_Model::COLUMN_ID_USER} = Auth::getUserId();
      $course->{Courses_Model::COLUMN_NAME} = $form->name->getValues();
      $course->{Courses_Model::COLUMN_TEXT_SHORT} = vve_strip_html_comment($form->textShort->getValues());
      $course->{Courses_Model::COLUMN_TEXT} = vve_strip_html_comment($form->text->getValues());
      $course->{Courses_Model::COLUMN_TEXT_PRIVATE} = vve_strip_html_comment($form->textPrivate->getValues());
      $course->{Courses_Model::COLUMN_URLKEY} = $form->urlkey->getValues();
      $course->{Courses_Model::COLUMN_DESCRIPTION} = $form->metaDesc->getValues();
      $course->{Courses_Model::COLUMN_KEYWORDS} = $form->metaKeywords->getValues();
      $course->{Courses_Model::COLUMN_DATE_START} = $form->dateStart->getValues();
      $course->{Courses_Model::COLUMN_DATE_STOP} = $form->dateStop->getValues();
      $course->{Courses_Model::COLUMN_PRICE} = $form->price->getValues();
      $course->{Courses_Model::COLUMN_HOURS_LEN} = $form->hourseLen->getValues();
      $course->{Courses_Model::COLUMN_PLACE} = $form->place->getValues();
      $course->{Courses_Model::COLUMN_SEATS} = $form->seats->getValues();
      $course->{Courses_Model::COLUMN_SEATS_BLOCKED} = $form->seatsBlocked->getValues();
      $course->{Courses_Model::COLUMN_IS_NEW} = $form->isNew->getValues();
      $course->{Courses_Model::COLUMN_IN_LIST} = $form->inList->getValues();
      $course->{Courses_Model::COLUMN_ALLOW_REG} = $form->allowReg->getValues();
      $course->{Courses_Model::COLUMN_TYPE} = $form->type->getValues();
      $course->{Courses_Model::COLUMN_FEED} = $form->allowFeed->getValues();
      $course->{Courses_Model::COLUMN_AKREDIT_MPSV} = $form->akredMPSV->getValues();
      $course->{Courses_Model::COLUMN_AKREDIT_MSMT} = $form->akredMSMT->getValues();
      $course->{Courses_Model::COLUMN_TAGRT_GROUPS} = $form->targetGroups->getValues();
      $course->{Courses_Model::COLUMN_TIME_START} = $form->timeStart->getValues();
      $course->{Courses_Model::COLUMN_IMAGE} = $imgName;

      $course->save();

      /* @var $course Courses_Model_Record */
      $course->assignLecturers($form->lecturers->getValues());
      $course->assignPrivateUsers($form->privateUsers->getValues());

      return $course;
   }

   private function courseRegistration()
   {
      $idc = $this->view()->course->{Courses_Model::COLUMN_ID};

      $regForm = new Form('course_reg_');
      $basicGrp = $regForm->addGroup('basic', $this->tr('Základní informace'));

      $eName = new Form_Element_Text('name', $this->tr('Jméno'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eName, $basicGrp);

      $eSurName = new Form_Element_Text('surname', $this->tr('Přijmení'));
      $eSurName->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eSurName, $basicGrp);

      $eDegree = new Form_Element_Text('degree', $this->tr('Titul'));
      $regForm->addElement($eDegree, $basicGrp);

      $eGrade = new Form_Element_Text('grade', $this->tr('Pracovní zařazení'));
      $eGrade->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eGrade, $basicGrp);

      $ePracticeLenght = new Form_Element_Text('practiceLength', $this->tr('Délka praxe'));
//      $ePracticeLenght->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($ePracticeLenght, $basicGrp);

      $ePhone = new Form_Element_Text('phone', $this->tr('Telefon'));
      $ePhone->addValidation(new Form_Validator_NotEmpty());
      $ePhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK, $this->tr('Telefon nebyl zadán ve správném formátu (+420 123 456 789).')));
//      $ePhone->setSubLabel($this->tr('Telefon ve formátu +420123456789 nebo +421 123456789'));
      $regForm->addElement($ePhone, $basicGrp);

      $eMail = new Form_Element_Text('mail', $this->tr('E-mail'));
      $eMail->addValidation(new Form_Validator_NotEmpty());
      $eMail->addValidation(new Form_Validator_Email());
      $regForm->addElement($eMail, $basicGrp);

      $eRegNewsLetter = new Form_Element_Checkbox('regNewsletter', $this->tr('Registrovat k odběru novinek'));
      $eRegNewsLetter->setValues(true);
      $regForm->addElement($eRegNewsLetter, $basicGrp);

      $eNote = new Form_Element_TextArea('note', $this->tr('Poznámka'));
      $regForm->addElement($eNote, $basicGrp);

      $payGroup = $regForm->addGroup('pay', $this->tr('Fakturační údaje'));
      switch ($this->view()->course->{Courses_Model::COLUMN_TYPE}) {
         case self::TYPE_CONFERENTION:
            $label = $this->tr('Konference hrazena');
            break;
         case self::TYPE_SEMINARE:
            $label = $this->tr('Seminář hrazen');
            break;
         default:
            $label = $this->tr('Kurz hrazen');
            break;
      }
      $ePay = new Form_Element_Radio('payType', $label);
      $payTypes = array($this->tr('Organizací') => self::PAY_TYPE_ORGANISATION,
          $this->tr('Soukromě') => self::PAY_TYPE_PRIVATE);
      $ePay->setOptions($payTypes);
      $ePay->setValues(self::PAY_TYPE_ORGANISATION);
      $regForm->addElement($ePay, $payGroup);

      $eOrgName = new Form_Element_Text('orgName', $this->tr('Název'));
      $regForm->addElement($eOrgName, $payGroup);

      $eOrgAddress = new Form_Element_TextArea('orgAddress', $this->tr('Adresa'));
      $regForm->addElement($eOrgAddress, $payGroup);

      $eOrgICO = new Form_Element_Text('orgICO', $this->tr('IČ'));
      $regForm->addElement($eOrgICO, $payGroup);

      $eOrgPhone = new Form_Element_Text('orgPhone', $this->tr('Telefon'));
      $regForm->addElement($eOrgPhone, $payGroup);

      $ePrivateAddress = new Form_Element_TextArea('privateAddress', $this->tr('Adresa'));
      $regForm->addElement($ePrivateAddress, $payGroup);

      $eSend = new Form_Element_Submit('send', $this->tr('Odeslat'));
      $regForm->addElement($eSend);

      $eCheck = new Form_Element_Checkbox('check', $this->tr('Souhlas se zpracováním'));
      $eCheck->setSubLabel($this->tr('Jsem srozuměn/a se všeobecnými podmínkami registrace a souhlasím se zpracováním svých osobných údajů podle zákona č. 101/2000 Sb.'));
      $regForm->addElement($eCheck);


      if ($regForm->isSend()) {
         if ($regForm->check->getValues() == false) {
            $eCheck->setError($this->tr('S podmínkami musíte souhlasit, jinak nelze registraci dokončit.'));
         }

         if ($regForm->payType->getValues() == 'organisation') {
            $regForm->orgName->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgAddress->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgICO->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgPhone->addValidation(new Form_Validator_NotEmpty());
         } else {
            $regForm->privateAddress->addValidation(new Form_Validator_NotEmpty());
         }
      }

      if ($regForm->isValid()) {
         $phone = preg_replace('/\s*/m', '', $regForm->phone->getValues());
         $reg = Courses_Model_Registrations::getNewRecord();

         $reg->{Courses_Model_Registrations::COLUMN_NAME} = $regForm->name->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_SURNAME} = $regForm->surname->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_DEGREE} = $regForm->degree->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_GRADE} = $regForm->grade->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_PRACTICE_LENGHT} = $regForm->practiceLength->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_MAIL} = $regForm->mail->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_NOTE} = $regForm->note->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_PAY_TYPE} = $regForm->payType->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_ORG_NAME} = $regForm->orgName->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_ORG_ADDR} = $regForm->orgAddress->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_ORG_ICO} = $regForm->orgICO->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_ORG_PHONE} = $regForm->orgPhone->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_PRIVATE_ADDR} = $regForm->privateAddress->getValues();
         $reg->{Courses_Model_Registrations::COLUMN_ID_COURSE} = $idc;
         $reg->{Courses_Model_Registrations::COLUMN_PHONE} = $phone;

         $reg->save();

         // pokud je registrace k newsletteru, přidáme jej
         if ($regForm->regNewsletter->getValues() == true) {
            Mails_Model_Addressbook::addUniqueMail($regForm->mail->getValues(), $this->category()->getParam(self::PARAM_NEWSLETTER_MAIL_GRP, Mails_Model_Groups::GROUP_ID_DEFAULT), $regForm->name->getValues() . ' ' . $regForm->surname->getValues());
         }

         $course = $this->view()->course;
         // odeslání emailu  s registrací na registrovaného a admina
         // řetězec mailu jako xml


         $mail = new Email(true);
         // obsah
         $cnt = $this->createMailCourseBody($course, $reg, $regForm);
//         var_dump(Email::getBaseHtmlMail($cnt));die;
         $mail->setContent(Email::getBaseHtmlMail($cnt));
         // předmět
         switch ($course->{Courses_Model::COLUMN_TYPE}) {
            case self::TYPE_CONFERENTION:
               $mail->setSubject('['.CUBE_CMS_WEB_NAME.'] Nová registrace do konference "' . $course->{Courses_Model::COLUMN_NAME} . '"');
               break;
            case self::TYPE_SEMINARE:
               $mail->setSubject('['.CUBE_CMS_WEB_NAME.'] Nová registrace do semináře "' . $course->{Courses_Model::COLUMN_NAME} . '"');
               break;
            default:
               $mail->setSubject('['.CUBE_CMS_WEB_NAME.'] Nová registrace do kurzu "' . $course->{Courses_Model::COLUMN_NAME} . '"');
               break;
         }
         // příjemci
         $mail->addAddress($regForm->mail->getValues()); // form
         if ($this->category()->getParam(self::PARAM_SEND_ADMIN_NOTIF, self::PARAM_SEND_ADMIN_NOTIF_DEFAULT) == true) {
            // vytažení příjemců admin
            $mail->addAddress($this->getAdminAddreses());
         }
         $mail->send();
         if($this->category()->getParam(self::PARAM_CONTACT_CAT)){
            $link = Url_Link::getCategoryLink($this->category()->getParam(self::PARAM_CONTACT_CAT));
            $this->infoMsg()->addMessage(sprintf($this->tr('Registace byla uložena. E-mailem automaticky obdržíte automaticky generované potvrzení o přijetí Vaší přihlášky. Pokud potvrzovací email do 24 hodin neobdržíte, <a href="%s" title="kontakt">kontaktuje nás</a>.'),(string)$link));
         } else {
            $this->infoMsg()->addMessage($this->tr('Registace byla uložena. E-mailem automaticky obdržíte automaticky generované potvrzení o přijetí Vaší přihlášky. Pokud potvrzovací email do 24 hodin neobdržíte, kontaktuje nás.'));
         }
         $this->link()->param('registration', 'true')->reload();
      }

      $this->view()->formReg = $regForm;
   }

   private function writeMailTableRow($name, $value = null)
   {
      $r = '<tr>';
      if($value == null){
         $r .= '<th colspan="2">'.$name.'</th>';
      } else {
         $r .= '<th>'.$name.'</th>';
         $r .= '<td>'.$value.'</td>';
      }
      return $r.'</tr>';
   }

   protected function createMailCourseBody($course, $reg, $form)
   {
      $cnt = null;
      // úvod
      $cnt .= '<p>';
      switch ($this->view()->course->{Courses_Model::COLUMN_TYPE}) {
         case self::TYPE_CONFERENTION:
            $cnt .= 'Byla registrována přihláška ke konferenci "' . $course->{Courses_Model::COLUMN_NAME} . '"';
            break;
         case self::TYPE_SEMINARE:
            $cnt .= 'Byla registrována přihláška k semináři "' . $course->{Courses_Model::COLUMN_NAME} . '"';
            break;
         default:
            $cnt .= 'Byla registrována přihláška ke kurzu "' . $course->{Courses_Model::COLUMN_NAME} . '"';
            break;
      }
      $cnt .= ' na stránkách <a href="' . Url_Link::getMainWebDir() . '">' . CUBE_CMS_WEB_NAME . '</a>.</p>';

      $cnt .= '<p>V případě dalších dotazů či uplatnění slev vám co nejdříve odpovíme.</p>';

      // odkaz na kurz
      $cnt .= '<p>';
      switch ($course->{Courses_Model::COLUMN_TYPE}) {
         case self::TYPE_CONFERENTION:
            $cnt .= 'Detail konference naleznete';
            break;
         case self::TYPE_SEMINARE:
            $cnt .= 'Detail semináře naleznete';
            break;
         default:
            $cnt .= 'Detail kurzu naleznete';
            break;
      }
      $cnt .= ' <a href="'.$this->link().'" title="'.$course->{Courses_Model::COLUMN_NAME}.'">zde</a>.';

      $cnt .= '</p>';

      // info o kurzu
      $cnt .= '<h2>Informace o kurzu</h2>'; // sof table
      $cnt .= '<table class="styled">'; // sof table
      // název
      $cnt .= $this->writeMailTableRow('Název', $course->{Courses_Model::COLUMN_NAME}); // sof table
      // termín
      $term = Utils_DateTime::fdate("%x", new DateTime($course->{Courses_Model::COLUMN_DATE_START}));
      if ($course->{Courses_Model::COLUMN_DATE_STOP} != null) {
         $term .= ' - ' . Utils_DateTime::fdate("%x", new DateTime($course->{Courses_Model::COLUMN_DATE_STOP}));
      }
      $cnt .= $this->writeMailTableRow('Termín', $term); // sof table
      // délka
      if ($course->{Courses_Model::COLUMN_HOURS_LEN} != 0) {
         $cnt .= $this->writeMailTableRow('Délka', $course->{Courses_Model::COLUMN_HOURS_LEN} . " hodin"); // sof table
      }
      // cena
      if ($course->{Courses_Model::COLUMN_PRICE} != 0) {
         $cnt .= $this->writeMailTableRow('Cena', $course->{Courses_Model::COLUMN_PRICE} . " Kč"); // sof table
      }
      // popisek
      $cnt .= $this->writeMailTableRow('Stručný popis', strip_tags($course->{Courses_Model::COLUMN_TEXT_SHORT}, 'br')); // sof table
      // místo
      if ($course->{Courses_Model::COLUMN_PLACE} != null) {
         $cnt .= $this->writeMailTableRow('Místo konání', $course->{Courses_Model::COLUMN_PLACE}); // sof table
      }
      $cnt .= '</table>'; // sof table

      $cnt .= '<br />';
      
      $cnt .= '<h2>Detail registrace</h2>';
      
      // detail  přihlášky
      $cnt .= '<table class="styled">';
      // jméno
      $name = ($reg->{Courses_Model_Registrations::COLUMN_DEGREE} != null ? $reg->{Courses_Model_Registrations::COLUMN_DEGREE}.' ' : '');
      $name .= $reg->{Courses_Model_Registrations::COLUMN_NAME}.' '.$reg->{Courses_Model_Registrations::COLUMN_SURNAME};
      
      $cnt .= $this->writeMailTableRow('Jméno a přimení', $name);
      $cnt .= $this->writeMailTableRow('Pracovní zařazení', ($reg->{Courses_Model_Registrations::COLUMN_GRADE} ? 'Ano' : 'Ne'));
      $cnt .= $this->writeMailTableRow('Délka praxe', $reg->{Courses_Model_Registrations::COLUMN_PRACTICE_LENGHT});
      $cnt .= $this->writeMailTableRow('Telefon', $reg->{Courses_Model_Registrations::COLUMN_PHONE});
      $cnt .= $this->writeMailTableRow('E-mail', $reg->{Courses_Model_Registrations::COLUMN_MAIL});
      $cnt .= $this->writeMailTableRow('Registrace k odběru<br /> novinek', ($form->regNewsletter->getValues() ? 'Ano' : 'Ne'));
      $cnt .= $this->writeMailTableRow('Poznámka', $reg->{Courses_Model_Registrations::COLUMN_NOTE});
      
      $cnt .= '</table>';

      $cnt .= '<br />';
      $cnt .= '<h2>Fakturační údaje</h2>';     
      $cnt .= '<table class="styled">';

      switch ($reg->{Courses_Model_Registrations::COLUMN_PAY_TYPE}) {
         case self::PAY_TYPE_ORGANISATION:
            $cnt .= $this->writeMailTableRow('Způsob fakturace', 'Organizací');
            $cnt .= $this->writeMailTableRow('Název', $reg->{Courses_Model_Registrations::COLUMN_ORG_NAME});
            $cnt .= $this->writeMailTableRow('Adresa', $reg->{Courses_Model_Registrations::COLUMN_ORG_ADDR});
            $cnt .= $this->writeMailTableRow('IČ', $reg->{Courses_Model_Registrations::COLUMN_ORG_ICO});
            $cnt .= $this->writeMailTableRow('Telefon', $reg->{Courses_Model_Registrations::COLUMN_ORG_PHONE});
            break;
         case self::PAY_TYPE_PRIVATE:
         default:
            $cnt .= $this->writeMailTableRow('Způsob fakturace', 'Soukromě');
            $cnt .= $this->writeMailTableRow('Adresa', $reg->{Courses_Model_Registrations::COLUMN_PRIVATE_ADDR});
            break;
      }

      $cnt .= '</table>';
      
      return $cnt;
   }

   /**
    * Metoda vrací emaily pro příjemce nových adres z nastaavení
    */
   private function getAdminAddreses()
   {
      $mails = array();
      $str = $this->category()->getParam(self::PARAM_OTHER_RECIPIENS, null);
      if ($str != null) {
         $mails = explode(';', $str);
      }
      $usersId = $this->category()->getParam(self::PARAM_ADMINS, array());

      $modelusers = new Model_Users();

      foreach ($usersId as $id) {
         $user = $modelusers->record($id);
         $mails = array_merge($mails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
      }
      $mails = array_unique($mails);
      return $mails;
   }

   /**
    * Kontroller pro seznam registrovaných ke kurzu
    */
   public function registrationsCourseController()
   {
      $this->checkWritebleRights();

      $modelC = new Courses_Model();
      $this->view()->course = $modelC->getCourse($this->getRequest('urlkey'));

      if ($this->view()->course == false)
         return false;

      $modelReg = new Courses_Model_Registrations();

      $formCancelReg = new Form('cancel_reg_');

      $eId = new Form_Element_Hidden('id');
      $eId->addValidation(new Form_Validator_IsNumber());
      $formCancelReg->addElement($eId);

      $eSubmit = new Form_Element_SubmitImage('submit');
      $formCancelReg->addElement($eSubmit);

      if ($formCancelReg->isValid()) {
         $reg = $modelReg->getRegistration($formCancelReg->id->getValues());

         $modelReg->cancelRegistration($formCancelReg->id->getValues());

         // zda mail o zrušení registrace

         $this->infoMsg()->addMessage(sprintf($this->tr('Registrace na jméno %s,%s byla zrušena'), $reg->{Courses_Model_Registrations::COLUMN_SURNAME}, $reg->{Courses_Model_Registrations::COLUMN_NAME}));

         $this->link()->reload();
      }
      $this->view()->formCancel = $formCancelReg;

      $this->view()->registrations = $modelReg->getRegistrations($this->view()->course->{Courses_Model::COLUMN_ID});
   }

   /**
    * Kontroler pro seznam uložených míst
    */
   public function placesListController()
   {
      $model = new Courses_Model_Places();
      $searched = $this->getRequestParam('term', null);
      $this->view()->places = $model->getPlaces($searched);
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings, Form &$form)
   {
      $formGrpUsers = $form->addGroup('users', 'Registrace');

      // odeslání upozornění
      $elemCheckAdminNotice = new Form_Element_Checkbox('admin_notice', 'Odesílat upozornění na nové registrace');
      if (!isset($settings[self::PARAM_SEND_ADMIN_NOTIF])) {
         $elemCheckAdminNotice->setValues(true);
      } else {
         $elemCheckAdminNotice->setValues($settings[self::PARAM_SEND_ADMIN_NOTIF]);
      }
      $form->addElement($elemCheckAdminNotice, $formGrpUsers);

      // maily správců
      $elemEamilRec = new Form_Element_TextArea('emails_rec', 'Adresy správců');
      $elemEamilRec->setSubLabel('E-mailové  adresy správců, kterým chodí upozornění
na nové registrace do kurzu. Může jich být více a jsou odděleny středníkem. Místo tohoto boxu
lze využít následující výběr již existujících uživatelů.');
      $form->addElement($elemEamilRec, $formGrpUsers);

      if (isset($settings[self::PARAM_OTHER_RECIPIENS])) {
         $form->emails_rec->setValues($settings[self::PARAM_OTHER_RECIPIENS]);
      }

      // admin recipients
      $elemAdmins = new Form_Element_SelectUser('admins', 'Adresy uživatelů v systému');
      $elemAdmins->loadUsers(array('groupId' => 1));
      $elemAdmins->setMultiple();
      $form->addElement($elemAdmins, $formGrpUsers);
      
      if (isset($settings[self::PARAM_ADMINS])) {
         $form->admins->setValues($settings[self::PARAM_ADMINS]);
      }

      // kategorie pro formulář
      $elemContactCat = new Form_Element_Select('catContact', 'Kateogrie kontaktu');
      $cats = Category_Structure::getStructure(Category_Structure::ALL)->getCategoryPaths(' / ');
      foreach ($cats as $id => $name) {
         $elemContactCat->addOption($name, $id);
      }
      if (isset($settings[self::PARAM_CONTACT_CAT])) {
         $elemContactCat->setValues($settings[self::PARAM_CONTACT_CAT]);
      }
      $form->addElement($elemContactCat, $formGrpUsers);
      
      
      // registrace newsletteru
      $formGrpNewsletter = $form->addGroup('newsletter', 'Nastavení registrace newsletteru');

      $modelMailsGroups = new Mails_Model_Groups();
      $groups = $modelMailsGroups->getGroups();

      $elemMailsGroups = new Form_Element_Select('newsletterGroup', 'Skupina v adresáři s maily');

      foreach ($groups as $group) {
         $elemMailsGroups->setOptions(array($group->{Mails_Model_Groups::COLUMN_NAME} => $group->{Mails_Model_Groups::COLUMN_ID}), true);
      }
      $form->addElement($elemMailsGroups, $formGrpNewsletter);

      if (isset($settings[self::PARAM_NEWSLETTER_MAIL_GRP])) {
         $form->newsletterGroup->setValues($settings[self::PARAM_NEWSLETTER_MAIL_GRP]);
      } else {
         $form->newsletterGroup->setValues(Mails_Model_Groups::GROUP_ID_DEFAULT);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_SEND_ADMIN_NOTIF] = $form->admin_notice->getValues();
         $settings[self::PARAM_ADMINS] = $form->admins->getValues();
         $settings[self::PARAM_OTHER_RECIPIENS] = $form->emails_rec->getValues();
         $settings[self::PARAM_NEWSLETTER_MAIL_GRP] = $form->newsletterGroup->getValues();
         $settings[self::PARAM_CONTACT_CAT] = $form->catContact->getValues();
      }
   }

}
