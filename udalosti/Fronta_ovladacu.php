<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Fronta ovladačů, je vždy napojena na nějakou událost.
 */
class Fronta_ovladacu {
    private $identifikator_udalosti;
    private $fronta_ovladacu;

    /**
     * Konstruktor třídy.
     * @param <type> $identifikator_udalosti
     */
    function __construct($identifikator_udalosti) {
        $this->fronta_ovladacu = new ArrayObject();
        $this->identifikator_udalosti = $identifikator_udalosti;
    }

    /**
     * Přidání ovladače na konec fronty.
     * @param <type> $ovladac
     */
    public function pridej_ovladac ($ovladac) {
        $this->fronta_ovladacu->append($ovladac);
    }

    /**
     * Reakcí na vyvolání události je provedení akcí všech ovladačů ve frontě.
     * @param <type> $odesilatel
     * @param <type> $parametry
     */
    public function reaguj_na_vyvolani_udalosti($odesilatel, $parametry) {
        foreach ($this->fronta_ovladacu as $ovladac) {
            $ovladac->proved_akci_ovladace($odesilatel, $parametry);
        }
    }

}
?>
