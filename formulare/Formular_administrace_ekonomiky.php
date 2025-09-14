<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_administrace
 *
 * @author pike
 */
class Formular_administrace_ekonomiky {
    //put your code here
    private $pravo_ukladat;
    private $spravce_konfigurace;
    private $pravo_reset_hry;
    private $pravo_ukonceni_kola;
    
    public function __construct(hrac $hrac) {
        $this->pravo_ukladat = $hrac->get_pravo_zmena_konfigurace_hry();
        $this->spravce_konfigurace = new spravce_konfigurace();
        $this->pravo_ukonceni_kola = $hrac->over_opravneni_k_akci('ukonceni_kola');
        $this->pravo_reset_hry = $hrac->over_opravneni_k_akci('reset_hry');

        $this->vygeneruj_a_vloz_formular_administrace();
    }

    private function vygeneruj_a_vloz_formular_administrace() {
        $prekladac = $GLOBALS['prekladac'];
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        $pageURL = str_replace("&", "&amp;", $pageURL);

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                $this->pole_vychozich_hodnot();
                $this->pole_amortizacni_faktor();
                $this->pole_produkcni_funkce();
                $this->pole_hodnotici_funkce();
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"adminisrace\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"Ulož změny\"";
                    if ($this->pravo_ukladat == 0) {
                        echo "disabled=\"disabled\"";
                    }
                    echo "/>";
            echo "</p>";
        echo "</form>";

        $this->formular_konec_kola();
        $this->formular_reset_hry();
    }

    private function pole_produkcni_funkce() {
        $spravce_konfigurace = $this->spravce_konfigurace;
        echo "<tr>";
            echo "<td colspan=\"2\">";
                echo "Rovnice produkční funkce";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td colspan=\"2\">";
                echo "<textarea rows=\"2\" name=\"produkcni_funkce\" style=\"width: 100%\">";
                    echo $spravce_konfigurace->get_produkcni_funkce();
                echo "</textarea>";
            echo "</td>";
        echo "</tr>";
    }

    private function pole_hodnotici_funkce() {
        $spravce_konfigurace = $this->spravce_konfigurace;
        echo "<tr>";
            echo "<td colspan=\"2\">";
                echo "Rovnice hodnotící funkce";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td colspan=\"2\">";
                echo "<textarea rows=\"2\" name=\"hodnotici_funkce\" style=\"width: 100%\">";
                    echo $spravce_konfigurace->get_hodnotici_funkce();
                echo "</textarea>";
            echo "</td>";
        echo "</tr>";
    }

    private function pole_amortizacni_faktor() {
        echo "<tr>";
            echo "<td colspan=\"2\" style=\"text-align: center\">";
                echo "Amortizace";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>";
                echo "Amortizační faktor";
            echo "</td>";
            echo "<td>";
                echo "<input type=\"text\" name=\"amortizacni_faktor\" value=\"" .
                    $this->spravce_konfigurace->get_amortizacni_faktor() . "\" />";
            echo "</td>";
        echo "</tr>";
    }

    private function pole_vychozich_hodnot() {
        echo "<tr>";
            echo "<td colspan=\"2\" style=\"text-align: center\">";
                echo "Výchozí hodnoty";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>";
                echo "Spotřební zboží na skladě";
            echo "</td>";
            echo "<td>";
                echo "<input type=\"text\" name=\"pocatecni_hodnota_spotrebni_zbozi_na_sklade\" value=\"" .
                    $this->spravce_konfigurace->get_pocatecni_hodnotu("spotrebni_zbozi_na_sklade"). "\" />";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>";
                echo "Kapitálové zboží na skladě";
            echo "</td>";
            echo "<td>";
                echo "<input type=\"text\" name=\"pocatecni_hodnota_kapitalove_zbozi_na_sklade\" value=\"" .
                    $this->spravce_konfigurace->get_pocatecni_hodnotu("kapitalove_zbozi_na_sklade"). "\" />";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>";
                echo "Kapitálové zboží ve výrobě";
            echo "</td>";
            echo "<td>";
                echo "<input type=\"text\" name=\"pocatecni_hodnota_kapitalove_zbozi_ve_vyrobe\" value=\"" .
                    $this->spravce_konfigurace->get_pocatecni_hodnotu("kapitalove_zbozi_ve_vyrobe"). "\" />";
            echo "</td>";
        echo "</tr>";
                echo "<tr>";
            echo "<td>";
                echo "Finanční hotovost (kapitál)";
            echo "</td>";
            echo "<td>";
                echo "<input type=\"text\" name=\"pocatecni_hodnota_kapital\" value=\"" .
                    $this->spravce_konfigurace->get_pocatecni_hodnotu("kapital"). "\" />";
            echo "</td>";
        echo "</tr>";
    }

    private function formular_reset_hry() {
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"reset_hry\" />";
                    if ($this->pravo_reset_hry == 1) {
                        echo "<input type=\"submit\" name=\"tlacitko\" value=\"Reset hry\" />";
                    }
            echo "</p>";
        echo "</form>";
    }

    private function formular_konec_kola() {
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"konec_kola\" />";
                    if ($this->pravo_ukonceni_kola == 1) {
                        echo "<input type=\"submit\" name=\"tlacitko\" value=\"Konec kola\" />";
                    }
            echo "</p>";
        echo "</form>";
    }
}
?>
