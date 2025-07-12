<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Casovac
 *
 * @author pike
 */
class Casovac extends Trida_generujici_udalosti {
    //put your code here

    public function __construct() {
        $this->pole_identifikatoru_udalosti[] = 'KONEC_KOLA';

        parent::__construct();
    }

    public function zpracuj_signal_casovace() {
        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('KONEC_KOLA', $this, array());
    }
}
?>
