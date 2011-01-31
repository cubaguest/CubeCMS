<?php
/**
 * Inteface pro ukládání tokenů
 * @author cuba
 */
interface Token_Store {
   /**
    *  Metoda pro kontrolu tokenu
    * @param string $token -- řetězec tokenu
    * @return bool
    */
   public static function check($token);
   /**
    *  Metoda pro uložení tokenu
    * @param string $token -- řetězec tokenu
    */
   public static function save($token);
   /**
    *  Metoda pro smazání tokenu
    * @param string $token -- řetězec tokenu
    */
   public static function delete($token);
   /**
    * Garbage collector -- odstraní staré tokeny
    */
   public static function gc();
}
?>
