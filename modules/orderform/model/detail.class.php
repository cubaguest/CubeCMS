<?php
/*
 * Třída modelu s detailem kontformu
 * 
 */
class Orderform_Model_Detail extends Model_Db {
   /**
    * Tabulka s detaily
    */
    const DB_TABLE = 'objednavky';

	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUMN_CITY_ID = 'id_contact';
	const COLUMN_CITY_NAME = 'name';
    
	const COLUMN_NAME = 'name';
	const COLUMN_SURNAME = 'surname';
	const COLUMN_COMPANY = 'company';
	const COLUMN_EMAIL = 'email';
	const COLUMN_PHONE = 'phone';
	const COLUMN_IP = 'ip';

//	private $text = null;
	

    /*
     * Metoda si klade za cil ulozit obsahy jednotlivych prvku
     * ve formulari do databaze.
     */
   public function saveOrderform($name,$surname,$company,$phone,$email) {

         $textArr = $this->createValuesArray(
            self::COLUMN_NAME, $name,
            self::COLUMN_SURNAME, $surname,
            self::COLUMN_COMPANY, $company,
            self::COLUMN_PHONE, $phone,
            self::COLUMN_EMAIL, $email,
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