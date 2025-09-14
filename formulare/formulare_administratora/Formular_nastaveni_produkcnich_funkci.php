<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_nastaveni_produkcnich_funkci
 *
 * @author pike
 */
class Formular_nastaveni_produkcnich_funkci extends Formular_administratora {
    //put your code here
    function __construct() {
        parent::__construct();
    }

    public function vygeneruj_a_vloz_formular() {
        $informace_o_ekonomikach_xhtml = Informace_o_ekonomikach_xhtml_vystupy::get_informace_o_ekonomikach_xhtml();
        $informace_o_ekonomikach_xhtml->generuj_tabulku_prudukcnich_funkci();
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_ekonomiky"),
                    "id_ekonomiky", $this->vygeneruj_vyber_ekonomiky(), "select");
                echo $this->generator_formularu->generuj_radek_a_text_area($this->prekladac->vloz_retezec("produkcni_funkce_spotrebni_zbozi"),
                    "produkcni_funkce_spotrebni_zbozi", "2", Prikaz_vyroby::get_klicova_slova());
                echo $this->generator_formularu->generuj_radek_a_text_area($this->prekladac->vloz_retezec("produkcni_funkce_kapitalove_zbozi"),
                    "produkcni_funkce_kapitalove_zbozi", "2", Prikaz_vyroby::get_klicova_slova());
                echo $this->generator_formularu->generuj_radek_a_text_area($this->prekladac->vloz_retezec("produkcni_funkce_lidsky_kapital"),
                    "produkcni_funkce_lidsky_kapital", "2");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nastaveni_produkcnich_funkci\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }
}
?>
