<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_vytvoreni_meny
 *
 * @author pike
 */
class Formular_vytvoreni_meny extends Formular_administratora{
    //put your code here

    function __construct() {
        parent::__construct();
    }

    public function vygeneruj_a_vloz_formular() {
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_meny"),
                    "id_meny");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("nazev_meny"),
                    "nazev_meny");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_meny\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }
}
?>
