<?php
/*
 * Třída modelu s detailem kontformu
 * 
 */
class Orderform_Model_Detail extends Model_Db {
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUMN_CITY_ID = 'id_contact';
	const COLUMN_CITY_NAME = 'name';
    
	const COLUMN_SURNAME = 'surname';
	const COLUMN_EMAIL = 'email';
	const COLUMN_QUESTION = 'question';
	const COLUMN_IP = 'ip';

//	private $text = null;
	

    /*
     * Metoda si klade za cil ulozit obsahy jednotlivych prvku
     * ve formulari do databaze.
     */
   public function saveKontform($name,$surname,$email,$question) {

         $textArr = $this->createValuesArray(self::COLUMN_NAME, $name,
            self::COLUMN_SURNAME, $surname,
            self::COLUMN_EMAIL, $email,
            self::COLUMN_QUESTION, $question,
            self::COLUMN_IP, $_SERVER['REMOTE_ADDR']);
        $sqlInsert = $this->getDb($this->sys())->insert()->table($this->module()->getDbTable())
               ->colums(array_keys($textArr))
               ->values(array_values($textArr));
          //TODO zde ještě odeslat informace na email..
         //	Vložení do db
         return $this->getDb()->query($sqlInsert);
   }




}

?>