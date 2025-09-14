<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_nastaveni_hrace
 *
 * @author pike
 */
class Formular_nastaveni_hrace extends Formular {
    //put your code here

    public function vygeneruj_a_vloz_formular() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $this->prekladac;
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nastaveni_hrace\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }


    function __construct() {
        parent::__construct();
    }

}
?>
