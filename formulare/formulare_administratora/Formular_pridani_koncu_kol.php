<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_pridani_koncu_kol
 *
 * @author pike
 */
class Formular_pridani_koncu_kol extends Formular_administratora{
    //put your code here

    public function vygeneruj_a_vloz_formular() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $this->prekladac;

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("pridani_casu_konce_kola"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("id_integracniho_celku"),
                    "id_integracniho_celku", $this->vygeneruj_vyber_integracnich_celku(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("den_v_tydnu"),
                    "den_v_tydnu", $this->vygeneruj_vyber_dnu_v_tydnu(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("hodina"),
                    "hodina");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("minuta"),
                    "minuta");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"pridani_koncu_kol\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";

    }

    protected function vygeneruj_vyber_dnu_v_tydnu() {
        $dny_v_tydnu = array ('Mon' => 'pondeli', 'Tue' => 'utery', 'Wed' => 'streda', 'Thu' => 'ctvrtek', 'Fri' => 'patek',
            'Sat' => 'sobota', 'Sun' => 'nedele', '*' => 'kazdy');
        $vyber = "";
        foreach ($dny_v_tydnu as $aktualni_den_id => $aktualni_den_hodnota) {
            $vyber .= "<option value=\"" . $aktualni_den_id . "\">" . $this->prekladac->vloz_retezec($aktualni_den_hodnota) . "</option>";
        }
        return $vyber;
    }
}
?>
