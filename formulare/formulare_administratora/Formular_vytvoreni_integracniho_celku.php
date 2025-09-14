<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_vytvoreni_integracniho_celku
 *
 * @author pike
 */
class Formular_vytvoreni_integracniho_celku extends Formular_administratora{
    //put your code here

    function __construct() {
        parent::__construct();
    }


    public function vygeneruj_a_vloz_formular() {
        $prekladac = $GLOBALS['prekladac'];
        $generator_formularu = $this->generator_formularu;

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_integracniho_celku"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_id_integracniho_celku"),
                    "id_integracniho_celku");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_povolena_migrace"),
                    "povolena_migrace", "", "checkbox");
                echo $generator_formularu->generuj_radek_a_text_area($prekladac->vloz_retezec("formular_superadministratora_celek_popis"),
                    "popis", "2");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_integracniho_celku\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }
}
?>
