<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_hodnoticich_funkci
 *
 * @author pike
 */
class Formular_hodnoticich_funkci extends Formular_administratora{
    //put your code here

    function __construct() {
        parent::__construct();
    }

    public function vygeneruj_a_vloz_formular() {
        $informace_o_ekonomikach_xhtml = Informace_o_ekonomikach_xhtml_vystupy::get_informace_o_ekonomikach_xhtml();
        $informace_o_ekonomikach_xhtml->generuj_hodnoticich_funkci();
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_ekonomiky"),
                    "id_ekonomiky", $this->vygeneruj_vyber_ekonomiky(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_druhu_subjektu"),
                    "id_druhu_subjektu", $this->vygeneruj_vyber_druhu_subjektu(), "select");
                echo $this->generator_formularu->generuj_radek_a_text_area($this->prekladac->vloz_retezec("hodnotici_funkce"),
                    "hodnotici_funkce", "2", Zaznam_o_cinnosti_subjektu::get_klicova_slova());
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"hodnotici_funkce\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

}
?>
