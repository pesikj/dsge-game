<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hrac
 *
 * @author pike
 */
class hrac extends Trida_generujici_udalosti {
    //put your code here

    private $mnozstvi_kapitalu = 0;
    private $mnozstvi_kapitaloveho_zbozi = 0;
    private $mnozstvi_kapitaloveho_zbozi_ve_vyrobe = 0;
    private $mnozstvi_spotrebniho_zbozi = 0;
    private $login = "";
    private $cislo_aktualniho_kola;

    public function getMnozstvi_kapitalu() {
        return floor($this->mnozstvi_kapitalu);
    }

    public function getMnozstvi_kapitaloveho_zbozi() {
        return $this->mnozstvi_kapitaloveho_zbozi;
    }


    public function getMnozstvi_spotrebniho_zbozi() {
        return $this->mnozstvi_spotrebniho_zbozi;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getMnozstvi_kapitaloveho_zbozi_ve_vyrobe() {
        return $this->mnozstvi_kapitaloveho_zbozi_ve_vyrobe;
    }

    

    public function zvys_mnozstvi_kapitalu($zvyseni) {
        $this->mnozstvi_kapitalu += $zvyseni;
        $dotaz_na_snizeni_mnozstvi_kapitalu = "UPDATE skladovane_polozky SET " .
            " mnozstvi_kapitalu = mnozstvi_kapitalu + " . $zvyseni .
            " WHERE login ='" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

    public function sniz_mnozstvi_kapitalu ($snizeni) {
        $this->mnozstvi_kapitalu -= $snizeni;
        $dotaz_na_snizeni_mnozstvi_kapitalu = "UPDATE skladovane_polozky SET " .
            " mnozstvi_kapitalu = mnozstvi_kapitalu - " . $snizeni .
            " WHERE login ='" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

    public function zvys_mnozstvi_kapitaloveho_zbozi ($zvyseni) {
        $this->mnozstvi_kapitaloveho_zbozi += $zvyseni;
        $dotaz_na_snizeni_mnozstvi_kapitalu = "UPDATE skladovane_polozky SET " .
            " mnozstvi_kapitaloveho_zbozi = mnozstvi_kapitaloveho_zbozi + " . $zvyseni .
            " WHERE login ='" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

    public function sniz_mnozstvi_kapitaloveho_zbozi ($snizeni) {
        $this->mnozstvi_kapitaloveho_zbozi -= $snizeni;
        $dotaz_na_snizeni_mnozstvi_kapitalu = "UPDATE skladovane_polozky SET " .
            " mnozstvi_kapitaloveho_zbozi = mnozstvi_kapitaloveho_zbozi - " . $snizeni .
            " WHERE login ='" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

    public function zvys_mnozstvi_spotrebniho_zbozi ($zvyseni) {
        $this->mnozstvi_spotrebniho_zbozi += $zvyseni;
        $dotaz_na_snizeni_mnozstvi_kapitalu = "UPDATE skladovane_polozky SET " .
            " mnozstvi_spotrebniho_zbozi = mnozstvi_spotrebniho_zbozi + " . $zvyseni .
            " WHERE login ='" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

    public function sniz_mnozstvi_spotrebniho_zbozi ($snizeni) {
        $this->mnozstvi_spotrebniho_zbozi -= $snizeni;
        $dotaz_na_snizeni_mnozstvi_kapitalu = "UPDATE skladovane_polozky SET " .
            " mnozstvi_spotrebniho_zbozi = mnozstvi_spotrebniho_zbozi - " . $snizeni .
            " WHERE login ='" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

    public function get_mnozstvi_volneho_casu() {
        $dotaz_na_zjisteni_vlastni_prace = "SELECT hodin_prace FROM prikazy_produkce WHERE " .
            "login = '" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        $vyledek_vlastni_prace = mysql_query($dotaz_na_zjisteni_vlastni_prace) or die ($dotaz_na_zjisteni_vlastni_prace);
        $hodin_vlastni_prace = 0;
        if (mysql_num_rows($vyledek_vlastni_prace) == 1) {
            $radek = mysql_fetch_array($vyledek_vlastni_prace);
            $hodin_vlastni_prace = $radek['hodin_prace'];
        }

        $dotaz_na_zjisteni_prodane_prace = "SELECT hodin_prace FROM prodana_prace WHERE login ='" . $this->login .
            "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        $vysledek = mysql_query($dotaz_na_zjisteni_prodane_prace) or die ($dotaz_na_zjisteni_prodane_prace);

        if (mysql_fetch_row($vysledek) != false) {
            $radek = mysql_fetch_array($vysledek);
            return 24 - $hodin_vlastni_prace - $radek['hodin_prace'];
        } else {
            return 24 - $hodin_vlastni_prace;
        }
    }

    public function get_mnozstvi_spotrebovanych_statku() {
        $dotaz_na_mnozstvi_spotrebovanych_statku = "SELECT mnozstvi_statku FROM spotreba WHERE " .
            "login = '" . $this->login . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        $vysledek = mysql_query($dotaz_na_mnozstvi_spotrebovanych_statku) or die ($dotaz_na_mnozstvi_spotrebovanych_statku);
        if (mysql_num_rows($vysledek) == 1) {
            $radek = mysql_fetch_array($vysledek);
            return $radek['mnozstvi_statku'];
        } else {
            return 0;
        }
    }

    public function get_pravo_zmena_konfigurace_hry() {
        $dotaz_na_pravo_zmena_konfigurace_hry = "SELECT zmena_konfigurace_hry FROM administratorska_prava WHERE " .
            "login = '" . $this->login . "';";
        $vysledek = mysql_query($dotaz_na_pravo_zmena_konfigurace_hry) or die ($dotaz_na_pravo_zmena_konfigurace_hry);
        if (mysql_num_rows($vysledek) == 1) {
            $radek = mysql_fetch_array($vysledek);
            return $radek['zmena_konfigurace_hry'];
        } else {
            return 0;
        }
    }

    public function get_pravo_prohlizeni_konfigurace_hry() {
        $dotaz_na_pravo_prohlizeni_konfigurace_hry = "SELECT prohlizeni_konfigurace_hry FROM administratorska_prava WHERE " .
            "login = '" . $this->login . "';";
        $vysledek = mysql_query($dotaz_na_pravo_prohlizeni_konfigurace_hry) or die ($dotaz_na_pravo_prohlizeni_konfigurace_hry);
        if (mysql_num_rows($vysledek) == 1) {
            $radek = mysql_fetch_array($vysledek);
            return $radek['prohlizeni_konfigurace_hry'];
        } else {
            return 0;
        }
    }

    public function get_aktivita_v_aktualnim_kole() {
        $dotaz_na_aktivitu_v_soucasnem_kole = "SELECT aktivni FROM aktivita_hracu WHERE login = '" . $this->login .
            "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";
        $vysledek_aktivita_v_soucasnem_kole = mysql_query($dotaz_na_aktivitu_v_soucasnem_kole) or die ($dotaz_na_aktivitu_v_soucasnem_kole);
        if (mysql_num_rows($vysledek_aktivita_v_soucasnem_kole) == 1) {
            $radek_aktivita_v_soucasnem_kole = mysql_fetch_array($vysledek_aktivita_v_soucasnem_kole);
            return $radek_aktivita_v_soucasnem_kole['aktivni'];
        } else {
            return 0;
        }

    }

    public function over_opravneni_k_akci ($id_opravneni) {
        $dotaz_na_zjisteni_opravneni = "SELECT " . $id_opravneni . " FROM administratorska_prava WHERE login = '" . $this->login . "'; ";
        $vysledek = mysql_query($dotaz_na_zjisteni_opravneni);
        if (gettype($vysledek) != 'resource') {
            return 0;
        } else {
            if (mysql_num_rows($vysledek) == 0) {
                return 0;
            } else {
                $radek = mysql_fetch_array($vysledek);
                return $radek[$id_opravneni];
            }
        }
    }

    public function get_pocet_bodu() {
        $dotaz_na_pocet_bodu = "SELECT SUM(hodnoceni) AS pocet_bodu, COUNT(hodnoceni) AS pocet_hodnoceni FROM hodnoceni_hracu WHERE login = '" . $this->login . "'; ";
        $vysledek_pocet_bodu = mysql_query($dotaz_na_pocet_bodu) or die ($dotaz_na_pocet_bodu);
        $radek = mysql_fetch_array($vysledek_pocet_bodu);
        if ($radek['pocet_hodnoceni'] == 0) {
            return 0;
        }
        return $radek['pocet_bodu'];
    }

    public function __construct($login) {
        $this->login = $login;
        $this->pole_identifikatoru_udalosti[] = 'ZADOST_O_STRANKU';

        $spravce_konfigurace = new spravce_konfigurace();
        $cislo_aktualniho_kola = $spravce_konfigurace->get_cislo_aktualniho_kola();
        $this->cislo_aktualniho_kola = $cislo_aktualniho_kola;

        $dotaz = "SELECT mnozstvi_kapitalu, mnozstvi_spotrebniho_zbozi," .
            "mnozstvi_kapitaloveho_zbozi FROM skladovane_polozky WHERE login='" .
            $login . "' AND kolo = " . $cislo_aktualniho_kola . ";";
        $vysledek = mysql_query($dotaz) or die ($dotaz);
        $udaje_o_hraci = mysql_fetch_array($vysledek);

        unset ($vysledek);

        $this->mnozstvi_kapitalu = $udaje_o_hraci['mnozstvi_kapitalu'];
        $this->mnozstvi_spotrebniho_zbozi = $udaje_o_hraci['mnozstvi_spotrebniho_zbozi'];
        $this->mnozstvi_kapitaloveho_zbozi = $udaje_o_hraci['mnozstvi_kapitaloveho_zbozi'];

        $dotaz = "SELECT mnozstvi_kapitaloveho_zbozi FROM kapitalove_zbozi_ve_vyrobe WHERE login='" .
            $login . "' AND kolo = " . $cislo_aktualniho_kola . ";";
        $vysledek = mysql_query($dotaz);
        $udaje_kapital_ve_vyrobe = mysql_fetch_array($vysledek);

        $this->mnozstvi_kapitaloveho_zbozi_ve_vyrobe = $udaje_kapital_ve_vyrobe['mnozstvi_kapitaloveho_zbozi'];

        parent::__construct();
    }

    public function pozadavek_hrace($_GET) {
        $parametry = $_GET;
        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ZADOST_O_STRANKU', $this, $parametry);
    }

    public function get_over_zapojeni_hrace_do_hry() {
        $dotaz_na_zapojeni_hrace_do_hry = "SELECT * FROM hraci WHERE login = '" . $this->login . "';";
        $vyledek_zapojeni_hrace_do_hry = mysql_query($dotaz_na_zapojeni_hrace_do_hry) or die ($dotaz_na_zapojeni_hrace_do_hry);
        if (mysql_num_rows($vyledek_zapojeni_hrace_do_hry) == 1) {
            return true;
        } else {
            return false;
        }
    }

}
?>
