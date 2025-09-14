<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_nahrani_trznich_prikazu_ze_souboru
 *
 * @author pike
 */
class Formular_nahrani_prikazu_ze_souboru extends Formular_administratora {

    public function vygeneruj_a_vloz_formular() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $this->prekladac;
        $pageURL = $this->pageURL;
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("formular_prikazu_ze_souboru"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("nazev_souboru"),
                    "nazev_souboru");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nahrani_trznich_prikazu_ze_souboru\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

    function __construct() {
        parent::__construct();
    }

}
?>
