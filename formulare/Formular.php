<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular
 *
 * @author pike
 */
class Formular {
    //put your code here

    protected $pageURL;
    protected $generator_formularu;
    protected $prekladac;
    protected $informace_o_ekonomikach;

    public function vygeneruj_a_vloz_formular() {
        ;
    }

    function __construct() {
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

        $this->pageURL = $pageURL;
        $this->generator_formularu = new Generator_formularu();
        $this->prekladac = $GLOBALS['prekladac'];
        $this->informace_o_ekonomikach = Informace_o_ekonomikach::get_informace_o_ekonomikach();
    }

    protected function vygeneruj_vyber_ekonomiky() {
        $pole_id_ekonomik = $this->informace_o_ekonomikach->get_pole_id_ekonomik();
        $vyber = "";
        foreach ($pole_id_ekonomik as $aktualni_id_ekonomiky => $id_integracniho_celku) {
            $vyber .= "<option value=\"" . $aktualni_id_ekonomiky . "\">" . $aktualni_id_ekonomiky . " (" . $id_integracniho_celku . ")" . "</option>";
        }
        return $vyber;
    }

    protected function vygeneruj_vyber_meny() {
        $pole_men = $this->informace_o_ekonomikach->get_pole_men();
        $vyber = "";
        foreach ($pole_men as $id_meny => $nazev_meny) {
            $vyber .= "<option value=\"" . $id_meny . "\">" . $nazev_meny . "</option>";
        }
        return $vyber;
    }

    protected function vygeneruj_vyber_integracnich_celku() {
        $pole_id_integracnich_celku = $this->informace_o_ekonomikach->get_pole_id_integracnich_celku();
        $vyber = "";

        foreach ($pole_id_integracnich_celku as $id_integracniho_celku => $cislo_aktualniho_kola) {
            $vyber .= "<option value=\"" .$id_integracniho_celku . "\">" . $id_integracniho_celku . "</option>";
        }

        return $vyber;
    }

    protected function vygeneruj_vyber_druhu_subjektu() {
        $prekladac = $GLOBALS['prekladac'];
        $vyber = "";
        $pole_druhu_subjektu = $this->informace_o_ekonomikach->get_pole_druhu_subjektu();
        foreach ($pole_druhu_subjektu as $id_druhu_subjektu) {
            $vyber .= "<option value=\"" . $id_druhu_subjektu . "\">" . $prekladac->vloz_retezec("druhy_subjektu_" . $id_druhu_subjektu) . "</option>";
        }
        return $vyber;
    }



    protected function vygeneruj_vyber_druhu_popisu_puvodu_toku() {
        $prekladac = $GLOBALS['prekladac'];
        $vyber = "";
        foreach ($this->informace_o_ekonomikach->get_pole_druhu_popisu_puvodu_toku() as $aktualni_druh_puvodu_toku) {
            $vyber .= "<option value=\"" . $aktualni_druh_puvodu_toku . "\">" . $prekladac->vloz_retezec($aktualni_druh_puvodu_toku) . "</option>";
        }
        return $vyber;
    }

    protected function vygeneruj_vyber_druhu_trhu() {
        $prekladac = $this->prekladac;
        $pole_id_druhu_trhu = $this->informace_o_ekonomikach->get_pole_id_druhu_trhu();
        $vyber = "";
        foreach ($pole_id_druhu_trhu as $id_druhu_trhu) {
            $vyber .= "<option value=\"" . $id_druhu_trhu . "\">" . $prekladac->vloz_retezec("druhy_trhu_" .$id_druhu_trhu) . "</option>";
        }
        return $vyber;
    }

    protected function vygeneruj_vyber_druhu_dani() {
        $prekladac = $this->prekladac;
        $pole_druhu_dani = $this->informace_o_ekonomikach->get_pole_druhu_dani_a_subjektu();
        $vyber = "";
        foreach ($pole_druhu_dani as $id_druhu_dane => $id_druhu_subjektu) {
            $vyber .= "<option value=\"" . $id_druhu_dane . "\">" . $prekladac->vloz_retezec($id_druhu_dane) . "</option>";
        }
        return $vyber;
    }

    protected function vygeneruj_vyber_komodit_pro_pocatecni_hodnoty() {
        $pole_komodit = $this->informace_o_ekonomikach->get_pole_nazvu_komodit_pocatecni_hodnoty();
        $vyber = "";
        foreach ($pole_komodit as $id_komodity) {
            $vyber .= "<option value=\"" . $id_komodity . "\">" . $id_komodity . "</option>";
        }
        return $vyber;
    }

    protected function vygeneruj_vyber_jazyka() {
        echo "<select name=\"id_jazyka\" class=\"form_field_ods\" />";
        $dotaz_na_jazyky = "SELECT * FROM jazyky;";
        $vysledek_jazyky = mysql_query($dotaz_na_jazyky) or die($dotaz_na_jazyky);
        while ($radek_jazyky = mysql_fetch_assoc($vysledek_jazyky)) {
            echo "<option value=\"" . $radek_jazyky['id_jazyka'] . "\">" . $radek_jazyky['plny_nazev'] . "</option>";
        }
        echo "</select>";
    }

    protected function vygeneruj_vyber_trhu() {
        $pole_druhu_trhu = $this->informace_o_ekonomikach->get_pole_druhu_trhu();
        $vyber = "";
        foreach ($pole_druhu_trhu as $id_trhu => $id_druhu_trhu) {
            $vyber .= "<option value=\"" . $id_trhu . "\">" . $this->informace_o_ekonomikach->get_nazev_trhu($id_trhu) . "</option>";
        }
        return $vyber;
    }
    


}
?>
