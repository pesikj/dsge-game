<script type="text/javascript">
function generuj_pole_pro_zadavani_polozek(str) {
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("nastaveni_trhu_dle_druhu_trhu").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","data_pro_ajax/nastaveni_trhu_dle_druhu_trhu.php?id_druhu_trhu="+str,true);
    xmlhttp.send();
}
</script>


<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_superadministratora
 *
 * @author pike
 */
class Formular_superadministratora extends Formular{
    //put your code here


    public function  __construct() {
        parent::__construct();
        $this->generator_formularu = new Generator_formularu();
    }

    public function generuj_formular_superadministratora() {
        $hrac = $GLOBALS['hrac'];
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


        $this->vloz_formular_vytvoreni_integracniho_celku();
        $this->vloz_formular_vytvoreni_ekonomiky();
        $this->vloz_formular_vytvoreni_trhu();
        $this->vloz_formular_definovani_druhu_dane();
    }

    private function vloz_formular_vytvoreni_integracniho_celku() {
        $prekladac = $GLOBALS['prekladac'];
        $generator_formularu = $this->generator_formularu;

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_integracniho_celku"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_id_integracniho_celku"), 
                    "id_integracniho_celku");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_povolena_migrace"),
                    "povolena_migrace", "", "checkbox");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_auto_kurz"),
                    "automaticky_vyvazeny_menovy_kurz", "", "checkbox");
                echo $generator_formularu->generuj_radek_a_text_area($prekladac->vloz_retezec("formular_superadministratora_celek_popis"),
                    "popis", "2");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_integracniho_celku\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

    private function vloz_formular_vytvoreni_ekonomiky() {
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
                    "nazev_meny");
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

    private function vloz_formular_vytvoreni_trhu() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $GLOBALS['prekladac'];

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";

                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_trhu"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_trhu_nazev_trhu"), "nazev_trhu");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_trhu_id_meny"),
                    "id_meny", $this->vygeneruj_vyber_meny(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_vytvoreni_trhu_id_integracniho_celku"),
                    "id_integracniho_celku", $this->vygeneruj_vyber_integracnich_celku(), "select");

                    echo "<tr>";
                        echo "<td>";
                            echo $prekladac->vloz_retezec("formular_superadministratora_vytvoreni_trhu_id_druhu_trhu");
                        echo "</td>";
                        echo "<td>";
                            echo "<select id=\"id_ekonomiky\" name=\"id_druhu_trhu\" onchange=\"generuj_pole_pro_zadavani_polozek(this.value)\">";
                                echo $this->vygeneruj_vyber_druhu_trhu();
                            echo "</select>";
                        echo "</td>";
                    echo "</tr>";

            echo "</table>";
            echo $this->vygeneruj_tabulku_druhu_subjektu_a_prav_obchodovani_na_trhu();
            echo "<div id=\"nastaveni_trhu_dle_druhu_trhu\"></div>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_trhu\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

    private function vloz_formular_definovani_druhu_dane() {
        $prekladac = $GLOBALS['prekladac'];
        $generator_formularu = $this->generator_formularu;
        
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";

                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("formular_superadministratora_definovani_druhu_dane"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_druh_subjektu"),
                    "id_druhu_subjektu", $this->vygeneruj_vyber_druhu_subjektu(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_dan_z_hlavy"),
                    "dan_z_hlavy", "", "checkbox");
            echo "</table>";

            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_druhu_popisu_puvodu_toku"),
                    "druh_popisu_puvodu_toku", $this->vygeneruj_vyber_druhu_popisu_puvodu_toku(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_druh_trhu"),
                    "id_druhu_trhu", $this->vygeneruj_vyber_druhu_trhu(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("formular_superadministratora_koeficient_toku"),
                    "koeficient_toku");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_trhu\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

    private function vygeneruj_vyber_integracnich_celku() {
        $dotaz_na_integracni_celky = "SELECT * FROM integracni_celky; ";
        $vysledek_integracni_celky = mysql_query($dotaz_na_integracni_celky) or die($dotaz_na_integracni_celky);

        $vyber = "";

        while ($radek = mysql_fetch_array($vysledek_integracni_celky)) {
            $vyber .= "<option value=\"" . $radek['id_integracniho_celku'] . "\">" . $radek['id_integracniho_celku'] . "</option>";
        }

        return $vyber;
    }

    private function vygeneruj_vyber_druhu_subjektu() {
        $prekladac = $GLOBALS['prekladac'];
        $vyber = "";
        if (isset ($GLOBALS['integracni_celek'])) {
            foreach ($this->informace_o_ekonomikach->get_pole_druhu_subjektu() as $aktualni_druh_subjektu) {
                $vyber .= "<option value=\"" . $aktualni_druh_subjektu . "\">" . $prekladac->vloz_retezec("druhy_subjektu_" . $aktualni_druh_subjektu) . "</option>";
            }
        } else {
            $dotaz_na_druhy_subjektu = "SELECT * FROM druhy_subjektu; ";
            $vysledek_druhy_subjektu = mysql_query($dotaz_na_druhy_subjektu) or die($dotaz_na_druhy_subjektu);
            while ($radek = mysql_fetch_array($vysledek_druhy_subjektu)) {
                $vyber .= "<option value=\"" . $radek['id_druhu_subjektu'] . "\">" . $prekladac->vloz_retezec("druhy_subjektu_" . $radek['id_druhu_subjektu']) . "</option>";
            }
        }
        return $vyber;
    }



    private function vygeneruj_vyber_druhu_popisu_puvodu_toku() {
        $prekladac = $GLOBALS['prekladac'];
        $vyber = "";
        if (isset ($GLOBALS['integracni_celek'])) {
            foreach ($this->informace_o_ekonomikach->get_pole_druhu_popisu_puvodu_toku() as $aktualni_druh_puvodu_toku) {
                $vyber .= "<option value=\"" . $aktualni_druh_puvodu_toku . "\">" . $prekladac->vloz_retezec($aktualni_druh_puvodu_toku) . "</option>";
            }
        }
        return $vyber;
    }

    private function vygeneruj_vyber_druhu_trhu() {
        $prekladac = $GLOBALS['prekladac'];
        $dotaz_na_integracni_celky = "SELECT * FROM druhy_trhu; ";
        $vysledek_integracni_celky = mysql_query($dotaz_na_integracni_celky) or die($dotaz_na_integracni_celky);

        $vyber = "";

        while ($radek = mysql_fetch_array($vysledek_integracni_celky)) {
            $vyber .= "<option value=\"" . $radek['id_druhu_trhu'] . "\">" . $prekladac->vloz_retezec("druhy_trhu_" .$radek['id_druhu_trhu']) . "</option>";
        }

        return $vyber;
    }

    private function vygeneruj_tabulku_druhu_subjektu_a_prav_obchodovani_na_trhu() {
        $prekladac = $GLOBALS['prekladac'];
        
        $tabulka = "<table border=\"0\" style=\"width: 100%\">";
        $tabulka .= "<tr>";
            $tabulka .= "<th>" . $prekladac->vloz_retezec("id_druhu_subjektu") . "</th>";
            $tabulka .= "<th>" . $prekladac->vloz_retezec("pravo_poptavat") . "</th>";
            $tabulka .= "<th>" . $prekladac->vloz_retezec("pravo_nabizet") . "</th>";
        $tabulka .= "</tr>";
        foreach ($this->informace_o_ekonomikach->get_pole_druhu_subjektu() as $aktualni_druh_subjektu) {
            foreach ($GLOBALS['informace_o_ekonomikach']->get_pole_id_ekonomik() as $aktualni_id_ekonomiky => $aktualni_id_integracniho_celku) {
                $tabulka .= "<tr>";
                $value_checkboxu = $aktualni_druh_subjektu . ";" . $aktualni_id_ekonomiky;
                $tabulka .= "<td>" . $prekladac->vloz_retezec("druhy_subjektu_" . $aktualni_druh_subjektu) . " (" . $aktualni_id_ekonomiky . ")" . "</td>";
                $tabulka .= "<td style=\"text-align:center;\"><input type=\"checkbox\" value=\"" . $value_checkboxu . "\" name=\"prava_poptavat[]\" /></td>";
                $tabulka .= "<td style=\"text-align:center;\"><input type=\"checkbox\" value=\"" . $value_checkboxu . "\" name=\"prava_nabizet[]\" /></td>";
                $tabulka .= "</tr>";
            }
        }
        $tabulka .= "</table>";
        return $tabulka;
    }
}
?>
