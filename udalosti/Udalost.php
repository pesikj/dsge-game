<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Třída reprezentující událost. Pracuje především s vlastní frontou ovladačů.
 */
class Udalost {
    private $identifikator; //označení události
    private $fronta_ovladacu;


    public function getIdentifikator() {
        return $this->identifikator;
    }

    public function __construct($identifikator) {
        $this->fronta_ovladacu = new Fronta_ovladacu($identifikator);
        $this->identifikator = $identifikator;
    }

    /**
     * Přidání ovladače události
     * @param Ovladac $ovladac
     */
    public function registruj_ovladac(Ovladac $ovladac) {
        $this->fronta_ovladacu->pridej_ovladac($ovladac);
    }

    /**
     * Vyvolání události - reagují všechny ovladače ve frontě.
     * @param <type> $odesilatel
     * @param <type> $parametry
     */
    public function vyvolani_udalosti($odesilatel, $parametry) {
        $this->fronta_ovladacu->reaguj_na_vyvolani_udalosti($odesilatel, $parametry);
    }

}
?>
