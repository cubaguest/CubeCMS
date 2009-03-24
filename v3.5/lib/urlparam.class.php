<?php
/**
 * Description of UrlParams
 * Třída slouží pro práci s parametry v url, e nastavována přímo z requestu
 * a jsou v ní uloženy všechny předané parametry i s hodnotami
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author		$Author: $ $Date:$
 *				$LastChangedBy: $ $LastChangedDate: $
 * @abstract	Třída pro obsluhu parametrů v URL
 */
class UrlParam {
    /**
     * Pole s parametry v url (ty co jsou oddělené lomítkem a lze je popsat
     * regulérním výrazem)
     * @var array
     */
    private static $params = array();

    /**
     * Pole s normálními parametry, tj. co jsou za otazníkem
     * @var array
     */
    private static $normalParams = array();

    /**
     * Název parametru
     * @var string
     */
    private $name = null;

    /**
     * Hodnota parametru
     * @var string
     */
    private $value = null;

    /**
     * Proměná obsahuje regulérní výraz pro parsování  parametru
     * @var string
     */
    private $pattern = null;

    /**
     * Proměná obsahuje jestli je daný parametr nastaven
     * @var boolean
     */
    private $isSet = false;

    /*
     * STATICKÉ METODY
     */

    /**
     * Metoda nastaví daný parametr do pole parametrů
     * @param string $param -- řetězec s parametrem
     */
    public static function setParam($param) {
        array_push(self::$params, $param);
    }

    /**
     * Metoda parsuje a nastavuje normálové parametry (tj. ty co jsou obsaženy
     * za otazníkem v url)
     * @param string $params -- řetězec s parametry (se všemi)
     */
    public static function setNormalParams($params) {
        self::$normalParams = $params;
    }

    /**
     * Metoda vrací pole s normálovými paarametry (jsou umístěny za otazníkem)
     * @return array -- pole s parametry
     */
    public static function getNormalParams() {
        return self::$normalParams;
    }

    /**
     * Metoda vrací pole s parametry, předávanými pomocí url (před otazníkem)
     * a oddělenými lomítky
     *
     * @return array -- pole s parametry
     */
    public static function getParams() {
        return self::$params;
    }

    /*
     * METODY
     */

    /**
     * Konstruktor třídy vytvoří objekt parametru pro URL
     *
     * @param string -- název parametru
     * @param mixed -- (option) regulérní výraz pro parsování parametru. pokud
     * není zadán, jedná se o normálový parametr. je zadávan jen výraz co je
     * hodnota, k výrazu se přřidává název proměnné např:  '([0-9]+)'
     */
    public function  __construct($name, $pattern = false) {
        if($pattern){
            $this->pattern = '^'.$name.$pattern.'$';
        }

        $this->name = $name;
//        Kontrola existence parametru
        $this->checkParam();
    }

    /**
     * Metoda nastavuje daný parametr z předané url
     */
    private function checkParam() {
//        Jedná-li se o standartní parametr
        if(!$this->isNormalParam()){
            $regs = array();
            foreach (self::$params as $param){
                //                okud parametr vyhovuje
                if(ereg($this->pattern, $param, $regs)){
                    $this->isSet = true;
                    if(isset ($regs[1])){
                        $this->value = $regs[1];
                    } else {
                        $this->value = true;
                    }
                }
            }
        }
//        Jedná-li se o normálový parametr
        else {
            if(key_exists($this->name, self::$normalParams)){
                $this->isSet = true;
                $this->value = pos(self::$normalParams);
                reset(self::$normalParams);
            }
        }
    }

    /**
     * Matoda nastavuje hodnotu parametru
     * @param mixed $value -- hodnota
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Metoda vrací hodnotu parametru v  url
     * @return mixed -- hodnota parametru
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Metoda vrací true pokud je nastavena hodnota parametru
     * @return boolean
     */
    public function isValue() {
        return $this->isSet;
    }

    /**
     * Metoda vrací regulérní výraz pro parsování parametru
     * @return string -- regulérní výraz
     */
    public function getPattern() {
        if(!$this->isNormalParam()){
            return $this->pattern;
        } else {
            return false;
        }
    }

    /**
     * Metoda vrací jestli se jedná o parametr v url nebo normalový
     * @return boolean -- true pokud se jedná o normálový parametr
     */
    public function isNormalParam() {
        if($this->pattern == null){
            return true;
        } else {
            return false;
        }
    }

    /**
     * magická metoda volaná při převodu na řetězec
     * @return string -- url parametr
     */
    public function  __toString() {
        if(!$this->isNormalParam()){
            return $this->name.$this->value;
        } else {
            return $this->name.Links::URL_SEP_PARAM_VALUE.$this->value;
        }
    }
}
?>
