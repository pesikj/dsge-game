<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_vytvoreni_ekonomiky
 *
 * @author pike
 */
class Formular_vytvoreni_ekonomiky extends Formular_administratora {
    //put your code here

    function __construct() {
        parent::__construct();
    }

    public function vygeneruj_a_vloz_formular() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $GLOBALS['prekladac'];

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_ekonomiky"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_id_ekonomiky"),
                    "id_ekonomiky");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_pristupovy_kod"),
                    "pristupovy_kod");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_vyber_celku_pro_ekonomiku"),
                    "id_integracniho_celku", $this->vygeneruj_vyber_integracnich_celku(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_nazev_meny"),
                    "nazev_meny", $this->vygeneruj_vyber_meny(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("druh_subjektu"),
                    "vychozi_id_druhu_subjektu", $this->vygeneruj_vyber_druhu_subjektu(), "select");
                echo $generator_formularu->generuj_radek_a_text_area($prekladac->vloz_retezec("formular_superadministratora_ekonomika_popis"),
                    "popis", "2");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_ekonomiky\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }


}
?>
