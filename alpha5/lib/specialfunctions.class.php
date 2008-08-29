<?php
/**
 * Třidá se speciálními funkcemi
 */
class SpecialFunctions {
	/**
	 * Pole obsahuje protokoly, jak začíná url adresa
	 * @var array/string
	 */
	private $protocolsArray = array('http://', 'https://', 'ftp://', 'ftps://');
	
	
	/**
	 * Konstruktor objektu se specielními funkcemi
	 *
	 * @param Db -- objekt pro přístup k databázi
	 * @param Module -- objekt pro přístup k vlastnostem modulu
	 */
	function __construct() {
	}
	
	/**
	 * Funkce odstrani nekorektni znaky z řetězce
	 *
	 * @param string -- řetězec z kterého se odstraní znaky
	 * @return string -- výsledný řetězec
	 */
	public function utf2ascii($text)
	{
		$return = Str_Replace(
		Array("á","č","ď","é","ě","í","ľ","ň","ó","ř","š","ť","ú","ů","ý","ž","Á","Č","Ď","É","Ě","Í","Ľ","Ň","Ó","Ř","Š","Ť","Ú","Ů","Ý","Ž") ,
		Array("a","c","d","e","e","i","l","n","o","r","s","t","u","u","y","z","A","C","D","E","E","I","L","N","O","R","S","T","U","U","Y","Z") ,
		$text);

		$return = Str_Replace(Array(" ", "_"), "-", $return); //nahradí mezery a podtržítka pomlčkami
		$return = Str_Replace(array("----","---","--"), "-", $return); //odstraní nekolik pomlcek za sebou
		$return = Str_Replace(Array("(",")",".","!",",","\"","'"), "", $return); //odstraní ().!,"'
		$return = StrToLower($return); //velká písmena nahradí malými.
		return $return;
	}
	
	/**
	 * Funkce vytvoří klíč o velikosti 50 znaků, který je určen pro uložení do db
	 *
	 * @param string -- text ze kterého se má klíč vytvořit
	 * @param array -- pole již existujících klíčů
	 * @param integer -- minimální počet znaků
	 * @param integer -- maximální počet znaků
	 * @return string -- vygenerovaný klíč pro db
	 */
	public function createDatabaseKey($text, $keysArray = null, $minLenght = 8, $maxLenght = 50)
	{
		//	odstranění tagů
		$text=ereg_replace("<[^>]+>", "", $text);

		//	pevod na ascii
		$newKey = $this->utf2ascii($text);
		$newKey = substr($newKey, 0, $maxLenght);

		//	Porpvnání klíču již uloženyých a vytvoření unikátního
		if($keysArray != null){
			$step = 1;
			$newUniqueKey = $newKey;
			$uniqueKey = false;

			while ($uniqueKey != true) {
				if(!in_array($newUniqueKey, $keysArray)){
					$uniqueKey = true;
				} else {
					$newUniqueKey=$newKey."-".$step++;
				}
				//			echo $newUniqueKey."-".$step."<br>";
			}
			$newKey=$newUniqueKey;
		}

		if(strlen($newKey) < 6){
			$newKey = str_pad($newKey, $minLenght, "-", STR_PAD_BOTH);
		}
		//	echo $newKey;
		return $newKey;
	}

	/**
	 * funkce odtraní z textu všechny tagy
	 *
	 * @param string -- textový řetězec
	 * @return string -- text bez tagů
	 */
	public function removeAllTags($text)
	{
		//	odstranění tagů
		$text=ereg_replace("<[^>]+>", "", $text);
		return $text;
	}


	/**
	 * Funkce převede některé speciílní znaky na alternativy html
	 * použito v komentářích
	 *
	 * @param string -- zadaný text
	 * @return string -- text. u kterého jsou znaky převedeny
	 */
	public function decodeSpecialChars($text)
	{
		$trans = array("#"=>"&#035;", "$"=>"&#036;","&"=>"&amp;","/"=>"&#047;","<"=>"&lt;",">"=>"&gt;","@"=>"&#064;",
			   "{"=>"&#123;","}"=>"&#125;","["=>"&#091;","]"=>"&#093;", "\\" =>"&#092;");

		$preklad = strtr($text, $trans);
		return $preklad;
	}

	/**
	 * Funkce převede předložky a některé znaky s normální mezerou na nezalomitelné mezery
	 *
	 * @param string -- zadaný text
	 * @return string -- text. u kterého jsou převedeny předložky
	 */
	public function czechTypo($text)
	{
		//	$trans = array(" s "=>" s&nbsp;", "&nbsp;s "=>"&nbsp;s&nbsp;", " S "=>" S&nbsp;", " z "=>" z&nbsp;", "&nbsp;z "=>"&nbsp;z&nbsp;", " Z "=>" Z&nbsp;",
		//				   " a "=>" a&nbsp;", "&nbsp;a "=>"&nbsp;a&nbsp;", " A "=>" A&nbsp;", " i "=>" i&nbsp;", "&nbsp;i "=>"&nbsp;i&nbsp;", " I "=>" I&nbsp;",
		//				   " k "=>" k&nbsp;", "&nbsp;k "=>"&nbsp;k&nbsp;", " K "=>" K&nbsp;", " u "=>" u&nbsp;", "&nbsp;u "=>"&nbsp;u&nbsp;", " U "=>" U&nbsp;",
		//				   " v "=>" v&nbsp;", "&nbsp;v "=>"&nbsp;v&nbsp;", " V "=>" V&nbsp;", " o "=>" o&nbsp;", "&nbsp;o "=>"&nbsp;o&nbsp;", " O "=>" O&nbsp;",
		//				   " na "=>" na&nbsp;", "&nbsp;na "=>"&nbsp;na&nbsp;", " Na "=>" Na&nbsp;", " za "=>" za&nbsp;", "&nbsp;za "=>"&nbsp;za&nbsp;", " Za "=>" Za&nbsp;",
		//				   " ze "=>" ze&nbsp;", "&nbsp;ze "=>"&nbsp;ze&nbsp;", " Ze "=>" Ze&nbsp;", " ke "=>" ke&nbsp;", "&nbsp;ke "=>"&nbsp;ke&nbsp;", " Ke "=>" Ke&nbsp;",
		//				   " se "=>" se&nbsp;", "&nbsp;se "=>"&nbsp;se&nbsp;", " Se "=>" Se&nbsp;", " od "=>" od&nbsp;", "&nbsp;od "=>"&nbsp;od&nbsp;", " Od "=>" Od&nbsp;",
		//				   " do "=>" do&nbsp;", "&nbsp;do "=>"&nbsp;do&nbsp;", " Do "=>"Do&nbsp;",
		//				   " pod "=>" pod&nbsp;", "&nbsp;pod "=>"&nbsp;pod&nbsp;", " Pod "=>" Pod&nbsp;", " nad "=>" nad&nbsp;", "&nbsp;nad "=>"&nbsp;nad&nbsp;", " Nad "=>" Nad&nbsp;",
		//				   " při "=>" při&nbsp;", "&nbsp;při "=>"&nbsp;při&nbsp;", " Při "=>" Při&nbsp;", " pro "=>" pro&nbsp;", "&nbsp;pro "=>"&nbsp;pro&nbsp;", " Pro "=>" Pro&nbsp;",
		//				   " bez "=>" bez&nbsp;", "&nbsp;bez "=>"&nbsp;bez&nbsp;", " Bez "=>" Bez&nbsp;",
		//				   " Kč"=>"&nbsp;Kč", " ˚C"=>"&nbsp;˚C", " V"=>"&nbsp;V");
		//
		//	$preklad = strtr($text, $trans);
		//	$preklad = strtr($preklad, $trans);

		$czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|ze|na|do|od|se|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";

		// překlad předložek na konci řádku
		$pattern = "[[:blank:]]{1}".$czechPripositions."[[:blank:]]{1}";
		$replacement = " \\1&nbsp;";
		$text = eregi_replace($pattern, $replacement, $text);

		$pattern = "&[a-z]+;".$czechPripositions."[[:blank:]]{1}";
		$replacement = "&nbsp;\\1&nbsp;";
		$text = eregi_replace($pattern, $replacement, $text);

		//	zkratky množin
		$pattern = "([0-9])[[:blank:]]{1}(kč|˚C|V|A|W)";
		$replacement = "\\1&nbsp;\\2";
		$text = eregi_replace($pattern, $replacement, $text);

		//mezera mezi číslovkami
		$pattern = "([0-9])([[:blank:]]{1})([0-9]{3})";
		$replacement = "\\1&nbsp;\\3";
		$text = ereg_replace($pattern, $replacement, $text);
		return $text;
	}

	/**
	 * Funkce pro generování mikročasu
	 *
	 * @return integer -- mikročas v milisekundách
	 */
	public function getMicroTime()
	{
		List ($usec, $sec) = Explode (' ', microtime());
		return ((float)$sec + (float)$usec);
	}

	/**
	 * Funkce nacte heslo ze souboru
	 * Heslo je ulozeno v md5 souctu
	 *
	 * @param string -- soubor s heslem
	 * @return string -- při úspěchu vrací string, jinak false
	 */
	public function load_passwd($passwd_file)
	{

		if (file_exists($passwd_file))
		{
			if ($soubor = fopen($passwd_file, "r"))
			{
				$passwd_md5=fread($soubor,32);
				fclose($soubor);
				return $passwd_md5;
			}
			else
			return 1;
		}
		else
		return 1;
	}

	/**
	 * Funkce zjišťuje jestli je číslo typu integer
	 * //TODO není třeba
	 * @param mix -- proměná, která se má ověřit
	 * @return boolean -- vrací trua, jestliže je integer, jinak false
	 */
	public function isInt($to_validate)
	{
		$RegExp = "/^[-+]?\d+$/";
		return (boolean)preg_match($RegExp,$to_validate);
	}

	/**
	 * Funkce maže zadaný adresář z filesystému i s obsahem
	 *
	 * @param dir -- adresář, který se má smazat
	 */
	public function deltree($f){
		foreach(glob($f.'/*') as $sf){
			if (is_dir($sf) && !is_link($sf)){
				deltree($sf);
				rmdir($sf);
			}else{
				unlink($sf);
			}
		}
	}

	/**
	 * Funkce ověřuje správné zadání kontrloního obrázk
	 * //TODO není třeba
	 * @param string -- název postu s odesýlaným textem
	 * @return boolean -- true pokud se data shodují
	 */
	public function verify_image($post_name)
	{
		if($_POST[$post_name] == $_SESSION["control_chars"]){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * funkce vygeneruje náhodné znaky pro ověření obrázkem
	 * a uloží je do session
	 * //TODO není třeba
	 */
	public function generate_verify_chars()
	{
		$chars = "ABCDEFHJKLMNPRSTUVWXYZ1234567890";
		$passwd = null;
		for ($i=0; $i<5; $i++){
			$passwd .= $chars{rand(0, strlen($chars)-1)};

		}
		//$passwd=strtoupper(substr(md5(rand()),0,5));
		$_SESSION["control_chars"] = $passwd;
	}
	
	/**
	 * Metoda vytvoří za zadaného řetězce url adresu
	 * 
	 * @param string -- řeťezec s url
	 * @return string -- opravená url
	 */
	public function createUrl($url) {
		$isProtoHeader = false;
		
		foreach ($this->protocolsArray as $protocol) {
			$strLen = strlen($protocol);
			if (strncasecmp($url, $protocol, $strLen) == 0){
				$isProtoHeader = true;
			}
		}
		
		if(!$isProtoHeader){
			$url = $this->protocolsArray[0].$url;
		}
		
		return $url;
	}
	
	/**
	 * Metoda kontroluje emailovou adresu
	 * 
	 * @param string -- adresa, která se má kontrolovat
	 * @return boolean -- true pokud se jedná o email
	 */
	public function checkMail ($email) {

		if (Eregl("^[a-z0-9_\.]+@[a-z0-9_\.]+[a-z]{2,3}$", $email)) {
			return true;
		}
		else {
			return false;
		}
	}
	
}

?>