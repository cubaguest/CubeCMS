<?php
/**
 * Prototyp objektu s translatorem
 */
class TrObject {
   /**
    * Objekt překladače
    * @var Translator
    */
   protected $translator = null;

   /**
    * metoda nastaví translator
    * @param Translator $trs
    */
   public function setTranslator(Translator $trs = null) {
      if($trs == null){
         $this->translator = new Translator();
      } else {
         $this->translator = $trs;
      }
   }

   /**
    * Metoda vrací objekt překladatele
    * @return Translator
    */
   protected function translator() {
      if(!$this->translator instanceof Translator) $this->setTranslator();
      return $this->translator;
   }

   /**
    * Přímá metoda pro překlad
    * @param mixed $str -- řetězec nebo pole pro překlady
    * @param int $count -- (nepovinné) počet, podle kterého se volí překlad
    */
   protected function tr($str, $count = 0) {
      return $this->translator()->tr($str, $count);
   }
}
?>
