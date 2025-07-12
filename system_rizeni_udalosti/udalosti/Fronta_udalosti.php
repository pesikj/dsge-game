<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Fronta událostí
 */
class Fronta_udalosti {
    private $fronta_udalosti;


    function __construct() {
        $this->fronta_udalosti = new ArrayObject();
    }


    public function pridej_udalost_do_fronty(Udalost $pridavana_udalost) {
        if ($this->obsahuje_udalost($pridavana_udalost) == false) {
            $this->fronta_udalosti->append($pridavana_udalost);
        }
    }


    public function obsahuje_udalost(Udalost $testovana_udalost) {
        foreach ($this->fronta_udalosti as $udalost) {
            if ($udalost->getIdentifikator() == $testovana_udalost->getIdentifikator()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Vyhledá správnou událost ve frontě a přiřadí ji ovladač v parametru.
     * @param Ovladac $ovladac
     */
    public function prirad_udalosti_ve_fronte_ovladac(Ovladac $ovladac) {
        foreach ($this->fronta_udalosti as $udalost) {
            if ($udalost->getIdentifikator() == $ovladac->getNazev_udalosti()) {
                $udalost->registruj_ovladac($ovladac);
            }
        }
    }

    /**
     * Volání události ve frontě, postupně aktivuje všechny ovldadače té události.
     * @param <type> $identifikator_udalosti
     * @param <type> $odesilatel
     * @param <type> $parametry
     */
    public function vyvolani_udalosti_ve_fronte($identifikator_udalosti, $odesilatel, $parametry) {
        foreach ($this->fronta_udalosti as $udalost) {
            if ($udalost->getIdentifikator() == $identifikator_udalosti) {
                $udalost->vyvolani_udalosti($odesilatel, $parametry);
            }
        }
    }
}

?>
