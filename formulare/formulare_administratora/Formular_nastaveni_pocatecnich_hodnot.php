<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_nastaveni_pocatecnich_hodnot
 *
 * @author pike
 */
class Formular_nastaveni_pocatecnich_hodnot extends Formular_administratora {
    //put your code here

    public function vygeneruj_a_vloz_formular() {
        $informace_o_ekonomikach_xhtml_vystupy = Informace_o_ekonomikach_xhtml_vystupy::get_informace_o_ekonomikach_xhtml();
        $informace_o_ekonomikach_xhtml_vystupy->generuj_tabulku_pocatecnich_hodnot();
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_ekonomiky"),
                    "id_ekonomiky", $this->vygeneruj_vyber_ekonomiky(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_druhu_subjektu"),
                    "id_druhu_subjektu", $this->vygeneruj_vyber_druhu_subjektu(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_komodity"),
                    "id_komodity", $this->vygeneruj_vyber_komodit_pro_pocatecni_hodnoty(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("pocatecni_hodnota"),
                    "pocatecni_hodnota");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nastaveni_pocatecnich_hodnot\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_trhu"),
                    "id_trhu", $this->vygeneruj_vyber_trhu(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("trzni_cena"),
                    "trzni_cena");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nastaveni_pocatecnich_cen\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
        
    }

    public function __construct() {
        parent::__construct();
    }
}
?>
