<?php
/**
 * Soubor s funkcemi pro vytvoření pokročilejších html tagů
 */

/**
 * Funkce vyttvoří formulářový prvke select pro výběr data
 * @param string $name -- název prvku select
 * @param int $time -- časové razítko
 * @param int $yearOffsetPlus -- kolik let má být dopředu
 * @param int $yearOffsetMinus -- kolik let má být dozadu
 * @param string $format -- formát "YMD"
 * @param boolean $names -- pokud je true jsou zobrazeny názvy měsíců textem
 * @param string $class -- název třídy prvků
 */
function crSelectDate($name, $time = null, $yearOffsetPlus = 10, $yearOffsetMinus = -10,
   $format = null, $names = true, $class = null) {
   $params = array();
   $params['name'] = $name;
   $params['format'] = strtoupper($format);
   if($format == null) {
      switch (Locale::getLang()) {
         case "cs":
            $params['format'] = "DMY";
            break;
         default:
            $params['format'] = "YMD";
            break;
      }
   }

   $params['class'] = null;
   if($class != null) {
      $params['class'] = " class=\"$class\"";
   }
   $params['time'] = time();
   if($time != null) {
      $params['time'] = $time;
   }
   $params['names'] = $names;
   $params['offsetPlus'] = $yearOffsetPlus;
   $params['offsetMinus'] = $yearOffsetMinus;

   // vytvořeníí dnů
   if(isset ($_POST[$params['name']]["day"])) {
      $selected = addslashes($_POST[$params['name']]["day"]);
   } else {
      $selected = date("j", $params['time']);
   }
   // vytvoření selectu
   $daysResult = null;
   $daysResult = "<select name=\"{$params['name']}[day]\"{$params['class']}>\n";
   for ($i = 1; $i <= 31; $i++) {
      $daysResult .= "<option value=\"{$i}\"";
      if($i == $selected) {
         $daysResult .= " selected=\"selected\"";
      }
      $daysResult .= ">";
      $daysResult .= $i;
      $daysResult .= "</option>\n";
   }
   $daysResult .= "</select>\n";

   // vytvoření měsíců
   if(isset ($_POST[$params['name']]["mounth"])) {
      $selectMounth = addslashes($_POST[$params['name']]["mounth"]);
   } else {
      $selectMounth = date("n", $params['time']);
   }
   // vytvoření selectu
   $mounthResult = null;
   $mounthResult = "<select name=\"{$params['name']}[mounth]\"{$params['class']}>\n";
   for ($i = 1; $i <= 12; $i++) {
      $mounthResult .= "<option value=\"{$i}\"";
      if($i == $selectMounth) {
         $mounthResult .= " selected=\"selected\"";
      }

      $mounthResult .= ">";
      if($params['names']) {
         $mounthResult .= strftime("%B", mktime(0, 0, 0, $i));
      } else {
         $mounthResult .= $i;
      }
      $mounthResult .= "</option>\n";
   }
   $mounthResult .= "</select>\n";

   // vytvoření roků
   if(isset ($_POST[$params['name']]["year"])) {
      $selectYear = addslashes($_POST[$params['name']]["year"]);
   } else {
      $selectYear = date("Y", $params['time']);
   }
   $startYear = $selectYear+$params['offsetMinus'];
   $stopYear = $selectYear+$params['offsetPlus'];
   // vytažení měsíců
   $yearResult = null;
   $yearResult = "<select name=\"{$params['name']}[year]\"{$params['class']}>\n";
   for ($i = $startYear; $i <= $stopYear; $i++) {
      $yearResult .= "<option value=\"{$i}\"";
      if($i == $selectYear) {
         $yearResult .= " selected=\"selected\"";
      }
      $yearResult .= ">";
      $yearResult .= $i;
      $yearResult .= "</option>\n";
   }
   $yearResult .= "</select>\n";

   $htmlResult = null;
   // podle pořadí vytvoření selectů
   for ($i = 0; $i <= 2; $i++) {
      $c = substr($params['format'], $i, 1);
      switch ($c) {
         case 'D':
            $htmlResult .= $daysResult;
            break;
         case 'M':
            $htmlResult .= $mounthResult;
            break;
         case 'Y':
            $htmlResult .= $yearResult;
            break;
      }
   }
   print ($htmlResult);
}

function vve_tpl_langImage($lang){
//   $element = new Html_Element('img');

   return "<img src=\"images/langs/{$lang}.png\" alt=\"{$lang}\" />";

}

?>
