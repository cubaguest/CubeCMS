<?php
/*
 * Třída modelu s detailem kontformu
 * 
 */
class Kontform_Model_Detail extends Model_Db {
   /**
    * Tabulka s detaily
    */
    const DB_TABLE = 'kontform';
    
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUMN_ID = 'id';
	const COLUMN_NAME = 'name';
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
        $sqlInsert = $this->getDb($this->sys())->insert()->table(Db::table(self::DB_TABLE))
               ->colums(array_keys($textArr))
               ->values(array_values($textArr));
          //TODO zde ještě odeslat informace na email..
         //	Vložení do db
         return $this->getDb()->query($sqlInsert);
   }




}

?>