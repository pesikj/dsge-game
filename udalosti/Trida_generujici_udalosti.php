<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Trida_generujici_udalosti
 *
 * @author pike
 */
class Trida_generujici_udalosti {
    protected $fronta_udalosti;
    protected $pole_identifikatoru_udalosti = array();

    /**
     * Konstruktor třídy. Jsou vytvořeny a zařazeny do fronty všechny události, které
     * může třída generovat.
     */
    public function __construct() {
        $this->fronta_udalosti = new Fronta_udalosti();
        foreach ($this->pole_identifikatoru_udalosti as $aktualni_identifikator_udalosti) {
            $nova_udalost = new Udalost($aktualni_identifikator_udalosti);
            $this->fronta_udalosti->pridej_udalost_do_fronty($nova_udalost);
        }
    }

    /**
     *
     * @param <type> $ovladac
     * Registrace nového ovladače, je předán frontě událostí.
     */
    public function registruj_ovladac($ovladac) {
        $this->fronta_udalosti->prirad_udalosti_ve_fronte_ovladac($ovladac);
    }
}
?>
