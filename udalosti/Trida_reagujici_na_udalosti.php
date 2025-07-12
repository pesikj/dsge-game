<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Rodičovská třída pro všechny třídy, které mají schopnost reagovat na nějakou událost.
 */
class Trida_reagujici_na_udalosti {
    //klíčem je identifikátor události a hodnotou název metody, která je spuštěna
    protected $pole_identifikatoru_udalosti = array();

    public function __construct() {

    }

    /**
     *
     * @param <type> $identifikator_udalosti
     * @return Ovladac Vygenerovaný ovladač
     *
     * Každá třída si umí vytvořit ovladač, které je na ni napojený.
     */
    public function generuj_ovladac_na_udalost($identifikator_udalosti) {
        foreach ($this->pole_identifikatoru_udalosti as $aktualni_identifikator_udalosti => $metoda_reagujici_na_udalost) {
            if ($aktualni_identifikator_udalosti == $identifikator_udalosti) {
                $ovladac = new Ovladac($identifikator_udalosti, $metoda_reagujici_na_udalost, $this);
                return $ovladac;
            }
        }
        return false;
    }

}
?>
