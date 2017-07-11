<?php

/**
 * Třída pro usnadnění práce s datem a časem
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_DateTime {

   /**
    * Metoda podobná date a strftime, umožňuje jednodušší vypsání data a to i podle locales
    * @param string $format -- formát viz. dále
    * @param int/string/DateTime $timestamp -- (option) čas
    * @return string -- formátované datum
    * <p>%d - Day of the month without leading zeros - 1 to 31</p>
    * <p>%D - Day of the month, 2 digits with leading zeros</p>
    * <p>%l - An abbreviated textual representation of the day</p>
    * <p>%L - A full textual representation of the day - Sunday through Saturday</p>
    * <p>%m - Numeric representation of a month, without leading zeros - 1 through 12</p>
    * <p>%M - Numeric representation of a month, with leading zeros - 01 through 12</p>
    * <p>%f(%b) - Abbreviated month name, based on the locale - Jan through Dec</p>
    * <p>%F(%B) - Full month name, based on the locale - January through December</p>
    * <p>%J - Celý název měsíce za řadovou číslovkou - ledna až prosince</p>
    * <p>%Y - A full numeric representation of a year, 4 digits - Examples: 1999 or 2003</p>
    * <p>%y - A two digit representation of a year - Examples: 99 or 03</p>
    * <p>%G - 24-hour format of an hour without leading zeros - 0 through 23</p>
    * <p>%g - 12-hour format of an hour without leading zeros - 1 through 12</p>
    * <p>%H - 24-hour format of an hour with leading zeros - 00 through 23</p>
    * <p>%h - 12-hour format of an hour with leading zeros - 01 through 12</p>
    * <p>%i - Minutes with leading zeros - 00 to 59</p>
    * <p>%s - Seconds, with leading zeros - 00 to 59</p>
    * <p>%x - Preferred date representation based on locale, without the time - Example: 02/05/09 for February 5, 2009</p>
    * <p>%X - Preferred time representation based on locale, without the date - Example: 03:59:16 or 15:59:16</p>
    */
   public static function fdate($format, $timestamp = null, $returnCurrent = true)
   {
      if ($timestamp instanceof DateTime) {
         $timestamp = $timestamp->format("U");
      } else if ($timestamp === null) {
         if(!$returnCurrent){
            return null;
         } 
         $timestamp = time();
      } else if (is_string($timestamp)) {
         $timestamp = new DateTime($timestamp);
         $timestamp = $timestamp->format("U");
      }

      $replacementArray = array(
          '%d' => array('func' => 'date', 'param' => 'j'),
          '%D' => array('func' => 'date', 'param' => 'd'),
          '%l' => array('func' => 'strftime', 'param' => '%a'),
          '%L' => array('func' => 'strftime', 'param' => '%A'),
          '%m' => array('func' => 'date', 'param' => 'n'),
          '%M' => array('func' => 'date', 'param' => 'm'),
          '%f' => array('func' => 'strftime', 'param' => '%b'),
          '%b' => array('func' => 'strftime', 'param' => '%b'),
          '%F' => array('func' => 'strftime', 'param' => '%B'),
          '%B' => array('func' => 'strftime', 'param' => '%B'),
          '%x' => array('func' => 'strftime', 'param' => '%x'),
          '%X' => array('func' => 'strftime', 'param' => '%X'),
          '%Y' => array('func' => 'date', 'param' => 'Y'),
          '%y' => array('func' => 'date', 'param' => 'y'),
          '%G' => array('func' => 'date', 'param' => 'G'),
          '%H' => array('func' => 'date', 'param' => 'H'),
          '%g' => array('func' => 'date', 'param' => 'g'),
          '%h' => array('func' => 'date', 'param' => 'h'),
          '%i' => array('func' => 'date', 'param' => 'i'),
          '%s' => array('func' => 'date', 'param' => 's'),
          '%J' => array('func' => 'Utils_DateTime::dateToMonth', 'param' => '')
      );

      foreach ($replacementArray as $str => $func) {
         $format = str_replace($str, call_user_func_array($func['func'], array($func['param'], (int) $timestamp)), $format);
      }
      return $format;
   }
   
   public static function dateToMonth($param, $timestam){
      $monthTR = array(
         'cs' => array(
            1 => 'ledna',
            'února',
            'března',
            'dubna',
            'května',
            'června',
            'července',
            'srpna',
            'září',
            'října',
            'listopadu',
            'prosince',
         )
      );
      return isset($monthTR[Locales::getLang()]) ? $monthTR[Locales::getLang()][date('n', $timestam)] : strftime('%B', $timestam);
   }

}
