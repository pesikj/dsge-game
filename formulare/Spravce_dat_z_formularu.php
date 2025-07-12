<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_formularu
 *
 * @author pike
 */
class Spravce_dat_z_formularu extends Trida_generujici_udalosti {
    //put your code here

    private $data_ke_zpracovani;
    private $hrac;
    private $cislo_aktualniho_kola;
    private $pole_chyb_formulare_nabidky;
    private $pole_chyb_formulare_produkce;
    private $spravce_konfigurace;

    public function __construct($data_ke_zpracovani) {
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_NABIDKY';
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_POPTAVKY';
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_PRODUKCE';
        $this->pole_identifikatoru_udalosti[] = 'RESET_HRY';
        $this->pole_identifikatoru_udalosti[] = 'KONEC_KOLA';
        $this->pole_identifikatoru_udalosti[] = 'ZADOST_NOVEHO_HRACE_O_ZAPOJENI_DO_HRY';

        $this->data_ke_zpracovani = $data_ke_zpracovani;
        $this->odstran_neciselne_znaky();
        $this->hrac = $GLOBALS['hrac'];
        $this->cislo_aktualniho_kola = $GLOBALS['cislo_aktualniho_kola'];
        $this->spravce_konfigurace= $GLOBALS['spravce_konfigurace'];

        parent::__construct();
    }

    public function spust_zpracovani_dat() {
        if ($this->data_ke_zpracovani['typ_formulare'] == 'poptavka') {
            $this->zpracovani_formular_trh_poptavka();
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'nabidka') {
            $this->zkontroluj_nabidku();
            $this->zpracovani_formular_trh_nabidka();
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'produkce') {
            $this->zpracovani_formular_produkce();
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'reset_hry') {
            if ($this->hrac->over_opravneni_k_akci('reset_hry') == 1) {
                $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('RESET_HRY', $this, array());
            }
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'konec_kola') {
            if ($this->hrac->over_opravneni_k_akci('ukonceni_kola') == 1) {
                $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('KONEC_KOLA', $this, array());
            }
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'adminisrace') {
            if ($this->hrac->over_opravneni_k_akci('zmena_konfigurace_hry') == 1) {
                $this->zpracovani_formular_administrace();
            }
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'editor') {
            if ($this->hrac->over_opravneni_k_akci('zmena_konfigurace_hry') == 1) {
                $this->zpracovani_formulare_editoru();
            }
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'zapojeni') {
            $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ZADOST_NOVEHO_HRACE_O_ZAPOJENI_DO_HRY', $this, array());
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'export_dat') {
            $this->zpracovani_formulare_export_dat();
        }
    }

    private function odstran_neciselne_znaky() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        foreach ($data_ke_zpracovani as $aktualni_klic => $aktualni_hodnota) {
            $data_ke_zpracovani[$aktualni_klic] = trim($aktualni_hodnota);
            if ((strstr($aktualni_klic, 'cena') != false) || (strstr($aktualni_klic, 'mnozstvi') != false)|| (strstr($aktualni_klic, 'hodin_prace') != false) ) {
                $data_ke_zpracovani[$aktualni_klic] = preg_replace("/\D/", "", $aktualni_hodnota);
                if (strlen($data_ke_zpracovani[$aktualni_klic]) == 0) {
                    $data_ke_zpracovani[$aktualni_klic]=0;
                }
            }
        }
        $this->data_ke_zpracovani = $data_ke_zpracovani;
    }

    private function zpracovani_formular_trh_poptavka() {
        $this->data_ke_zpracovani;
        $nova_poptavka = new individualni_poptavka($this->hrac->getLogin(), $this->cislo_aktualniho_kola);

        foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_ceny => $aktualni_hodnota_hledani_ceny) {
            if (strstr($aktualni_klic_hledani_ceny, 'cena') != false) {
                $cislo_aktualni_polozky = preg_replace("/\D/", "", $aktualni_klic_hledani_ceny);
                foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_mnozstvi => $aktualni_hodnota_hledani_mnozstvi) {
                    if (strstr($aktualni_klic_hledani_mnozstvi, 'mnozstvi' . $cislo_aktualni_polozky) != false) {
                        $nova_poptavka->pridej_parametr_poptavky($aktualni_hodnota_hledani_ceny, $aktualni_hodnota_hledani_mnozstvi);
                    }
                }
            }
        }

        $nova_poptavka->vypoctiPoptavku();

        $nazev_tabulky = $this->data_ke_zpracovani['nazev_tabulky'];

        $dotaz_na_overeni_existence_poptavky = "SELECT * FROM " . $nazev_tabulky .
            " WHERE login='" . $this->hrac->getLogin() ."' AND kolo= " . $this->cislo_aktualniho_kola . ";";
        $vysledek_kontroly = mysql_query($dotaz_na_overeni_existence_poptavky);

        $dotaz = "";
        if (mysql_num_rows($vysledek_kontroly) == 1) {
            $dotaz = $nova_poptavka->vytvor_SQL_definici($nazev_tabulky, $this->hrac->getLogin(), false);
        } else {
            $dotaz = $nova_poptavka->vytvor_SQL_definici($nazev_tabulky, $this->hrac->getLogin(), true);
        }


        mysql_query($dotaz);

        $parametry = array();
        $parametry['chyba_v_datech'] = false;
        $parametry['pole_meznich_cen'] = $nova_poptavka->getPole_meznich_cen();

        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_POPTAVKY', $this, $parametry);
    }

    private function zpracovani_formular_trh_nabidka() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $nova_nabidka = new individualni_nabidka($this->hrac->getLogin(), $this->cislo_aktualniho_kola);
        foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_ceny => $aktualni_hodnota_hledani_ceny) {
            if (strstr($aktualni_klic_hledani_ceny, 'cena') != false) {
                $cislo_aktualni_polozky = preg_replace("/\D/", "", $aktualni_klic_hledani_ceny);
                foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_mnozstvi => $aktualni_hodnota_hledani_mnozstvi) {
                    if (strstr($aktualni_klic_hledani_mnozstvi, 'mnozstvi' . $cislo_aktualni_polozky) != false) {
                        $nova_nabidka->pridej_parametr_nabidky($aktualni_hodnota_hledani_ceny, $aktualni_hodnota_hledani_mnozstvi);
                    }
                }
            }
        }
        $nazev_tabulky = $data_ke_zpracovani['nazev_tabulky'];

        if (sizeof($this->pole_chyb_formulare_nabidky) == 0) {
            $dotaz_na_overeni_existence_nabidky = "SELECT * FROM " . $nazev_tabulky .
                " WHERE login='" . $this->hrac->getLogin() . "' AND kolo=" . $this->cislo_aktualniho_kola . ";";
            $vysledek_kontroly = mysql_query($dotaz_na_overeni_existence_nabidky) or die ($dotaz_na_overeni_existence_nabidky);

            $dotaz = "";
            if (mysql_num_rows($vysledek_kontroly) == 1) {
                $dotaz = $nova_nabidka->vytvor_SQL_definici($nazev_tabulky, $this->hrac->getLogin(), false);
            } else {
                $dotaz = $nova_nabidka->vytvor_SQL_definici($nazev_tabulky, $this->hrac->getLogin(), true);
            }

            mysql_query($dotaz);
        }


        $parametry = array();
        $parametry['chyba_v_datech'] = (sizeof($this->pole_chyb_formulare_nabidky) == 0);
        $parametry['pole_meznich_cen'] = $nova_nabidka->getPole_meznich_cen();
        $parametry['pole_chyb'] = $this->pole_chyb_formulare_nabidky;

        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_NABIDKY', $this, $parametry);
    }

    private function zpracovani_formular_produkce() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $druh_zbozi = 1;
        if ($data_ke_zpracovani['vyrabene_zbozi'] == 'kapitalove_zbozi') {
            $druh_zbozi = 2;
        }
        $prikaz_produkce = new prikaz_produkce($data_ke_zpracovani['hodin_prace'], $druh_zbozi);


        if ($this->zkontroluj_zadane_hodiny_prace() == true) {
            $dotaz = "SELECT * FROM prikazy_produkce WHERE login = '" . $this->hrac->getLogin() . "' AND kolo = ". $this->cislo_aktualniho_kola;
            $vysledek = mysql_query($dotaz);

            $dotaz = "";
            if (mysql_num_rows($vysledek) == 0) {
                $dotaz = $prikaz_produkce->vytvor_SQL_definici($this->hrac->getLogin(), $this->cislo_aktualniho_kola, true);
            } else {
                $dotaz = $prikaz_produkce->vytvor_SQL_definici($this->hrac->getLogin(), $this->cislo_aktualniho_kola, false);
            }

            mysql_query($dotaz);
        }

        $parametry = array();
        $parametry['chyba_v_datech'] = $this->zkontroluj_zadane_hodiny_prace();
        $parametry['pole_chyb'] = $this->pole_chyb_formulare_produkce;
        $parametry['default_data'] = array();
        $parametry['default_data']['hodin_prace'] = $prikaz_produkce->getHodin_prace();
        $parametry['default_data']['druh_zbozi'] = $prikaz_produkce->getDruh_zbozi();

        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_PRODUKCE', $this, $parametry);
        
    }

    private function zkontroluj_nabidku () {
        $hrac = $this->hrac;
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $pole_chyb = array ();
        if ($data_ke_zpracovani['nazev_tabulky'] == 'trh_prace_nabidka') {
            $dotaz = "SELECT hodin_prace FROM prikazy_produkce WHERE login='" . $hrac->getLogin() .
                "' AND kolo = " .$this->cislo_aktualniho_kola . ";";
            $vysledek = mysql_query($dotaz);
            $radek = mysql_fetch_array($vysledek);

            if ($radek != false) {
                $hodin_prace = $radek['hodin_prace'];
            } else {
                $hodin_prace = 0;
            }

            foreach ($data_ke_zpracovani as $klic => $hodnota) {
                if (strstr($klic, 'mnozstvi') != false) {
                    if ($hodnota > (24 - $hodin_prace)) {
                        $pole_chyb[preg_replace("/\D/", "", $klic)] = 'E';
                    }
                }
            }

        } else if ($data_ke_zpracovani['nazev_tabulky'] == 'trh_spotrebniho_zbozi_nabidka') {
            foreach ($data_ke_zpracovani as $klic => $hodnota) {
                if (strstr($klic, 'mnozstvi') != false) {
                    if ($hodnota > $hrac->getMnozstvi_spotrebniho_zbozi()) {
                        $pole_chyb[preg_replace("/\D/", "", $klic)] = 'E';
                    }
                }
            }
        } else if ($data_ke_zpracovani['nazev_tabulky'] == 'trh_kapitaloveho_zbozi_nabidka') {
            foreach ($data_ke_zpracovani as $klic => $hodnota) {
                if (strstr($klic, 'mnozstvi') != false) {
                    if ($hodnota > $hrac->getMnozstvi_kapitaloveho_zbozi()) {
                        $pole_chyb[preg_replace("/\D/", "", $klic)] = 'E';
                    }
                }
            }
        } else if (strpos( $data_ke_zpracovani['nazev_tabulky'],'kapitalu') > 0) {
            foreach ($data_ke_zpracovani as $klic => $hodnota) {
                if (strstr($klic, 'mnozstvi') != false) {
                    if ($hodnota > $hrac->getMnozstvi_kapitalu()) {
                        $pole_chyb[preg_replace("/\D/", "", $klic)] = 'E';
                    }
                }
            }
        }

        $this->pole_chyb_formulare_nabidky = $pole_chyb;
    }

    private function zkontroluj_zadane_hodiny_prace() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $hodin_prace = $data_ke_zpracovani['hodin_prace'];
        $pole_chyb = array();

        if ($hodin_prace > 24) {
            $pole_chyb[1] = "E";
            $this->pole_chyb_formulare_produkce = $pole_chyb;
            return false;
        }

        $dotaz = "SELECT mnozstvi4, mnozstvi3, mnozstvi2, mnozstvi1 FROM trh_prace_nabidka " .
            " WHERE login='" . $this->hrac->getLogin() . "' AND kolo = " . $this->cislo_aktualniho_kola ."; ";
        $vysledek = mysql_query($dotaz);
        if (mysql_num_rows($vysledek) > 0) {
            $radek = mysql_fetch_array($vysledek);

            foreach ($radek as $aktualni_polozka) {
                if (($aktualni_polozka + $hodin_prace) > 24) {
                    $pole_chyb[1] = "S";
                    $this->pole_chyb_formulare_produkce = $pole_chyb;
                    return false;
                }
            }
        }

        return true;
    }

    private function zpracovani_formular_administrace () {
        $spravce_konfigurace = $this->spravce_konfigurace;
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $spravce_konfigurace->set_pocatecni_hodnotu('spotrebni_zbozi_na_sklade', $data_ke_zpracovani['pocatecni_hodnota_spotrebni_zbozi_na_sklade']);
        $spravce_konfigurace->set_pocatecni_hodnotu('kapitalove_zbozi_na_sklade', $data_ke_zpracovani['pocatecni_hodnota_kapitalove_zbozi_na_sklade']);
        $spravce_konfigurace->set_pocatecni_hodnotu('kapitalove_zbozi_ve_vyrobe', $data_ke_zpracovani['pocatecni_hodnota_kapitalove_zbozi_ve_vyrobe']);
        $spravce_konfigurace->set_pocatecni_hodnotu('kapital', $data_ke_zpracovani['pocatecni_hodnota_kapital']);

        $spravce_konfigurace->set_produkcni_funkce($data_ke_zpracovani['produkcni_funkce']);
        $spravce_konfigurace->set_hodnotici_funkce($data_ke_zpracovani['hodnotici_funkce']);

    }

    private function zpracovani_formulare_editoru() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $spravce_clanku = new Spravce_clanku();
        $spravce_clanku->uloz_uvodni_clanek(stripslashes($data_ke_zpracovani['obsah']));

    }

    private function zpracovani_formulare_export_dat() {
        $nazev_tabulky = $this->data_ke_zpracovani['tabulka'];
        header("Content-disposition: attachment; filename=" . $nazev_tabulky . ".csv");
        header("Content-type: text/plain");

        $dotaz_na_nazvy_sloupcu = "SHOW COLUMNS FROM " . $nazev_tabulky . ";";
        $vysledek_nazvy_sloupcu = mysql_query($dotaz_na_nazvy_sloupcu) or die ($dotaz_na_nazvy_sloupcu);

        while ($radek = mysql_fetch_assoc($vysledek_nazvy_sloupcu)) {
            foreach ($radek as $klic => $hodnota) {
                if ($klic == 'Field') {
                    echo $hodnota . ";";
                }
            }
        }
        unset ($radek);
        unset ($hodnota);
        echo "\n";

        $dotaz_na_data_z_tabulky = "SELECT * FROM " . $nazev_tabulky . ";";
        $vysledek_data_z_tabulky = mysql_query($dotaz_na_data_z_tabulky) or die ($dotaz_na_data_z_tabulky);

        while ($radek = mysql_fetch_assoc($vysledek_data_z_tabulky)) {
            foreach ($radek as $hodnota) {
                echo $hodnota . ";";
            }
            echo "\n";
        }

        exit;
    }
}
?>
