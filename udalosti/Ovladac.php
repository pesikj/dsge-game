<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Reprezentuje ovladač. Každý ovladač se vztahuje k právě jedné události a současně
 * k právě jedné instanci třídy se schopností reagovat na události.
 */
class Ovladac {
    private $nazev_udalosti;
    private $nazev_spoustene_metody;
    private $napojeny_objekt;

    public function getNazev_udalosti() {
        return $this->nazev_udalosti;
    }

    function __construct($nazev_udalosti, $nazev_spoustene_metody, $napojeny_objekt) {
        //Kontrola napojeného objektu
        if ($napojeny_objekt instanceof Trida_reagujici_na_udalosti) {
            $this->napojeny_objekt = $napojeny_objekt;
            $this->nazev_udalosti = $nazev_udalosti;
            $this->nazev_spoustene_metody = $nazev_spoustene_metody;
        } else {
            return;
        }

    }

    /**
     * Úprava kódu a jeho spuštění
     * @param <type> $odesilatel
     * @param <type> $parametry
     */
    public function proved_akci_ovladace($odesilatel, $parametry) {
//        $pole_parametru_funkce = array ();
//        $pole_parametru_funkce[] = $odesilatel;
//        $pole_parametru_funkce[] = $parametry;
//        call_user_func(array($this->napojeny_objekt, $this->nazev_spoustene_metody), $pole_parametru_funkce);
        eval ('$this->napojeny_objekt->' . $this->nazev_spoustene_metody . '($odesilatel, $parametry);');
    }
}
?>
