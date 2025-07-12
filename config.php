<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//    require_once 'mysql_connect.php';
//    $dotaz_na_aktualni_kolo = "SELECT kolo FROM statistika_kola WHERE datum_a_cas_konce =
//        '0000-00-00 00:00:00';";
//    $vysledek = mysql_query($dotaz_na_aktualni_kolo);
//    $radek = mysql_fetch_array($vysledek);
//    $kolo = $radek['kolo']; // tuto proměnnou postupně zlikvidovat !!!
//    $aktualni_kolo = $radek['kolo'];


    //soubor může být includovaný i soubory z podadresářů,
    //proto ověříme, kde je soubor s konfigurací


class spravce_konfigurace extends Trida_reagujici_na_udalosti {
    private $sx;
    private $cesta_k_souboru;
    private $cesta_k_xsd_schematu;

    public function __construct() {
        $this->pole_identifikatoru_udalosti['KONEC_KOLA'] = 'ukonci_kolo';
        $this->pole_identifikatoru_udalosti['RESET_HRY'] = 'nastav_aktualni_kolo_na_1';
        if (file_exists('inc/tmp/conf.xml')) {
            $cesta_k_souboru = 'inc/tmp/conf.xml';
            $cesta_k_xsd_schematu = 'conf_schema.xsd';

        } else if (file_exists('../tmp/conf.xml')) {
            $cesta_k_souboru = '../tmp/conf.xml';
            $cesta_k_xsd_schematu = '../conf_schema.xsd';
        }
        
        $sx = simplexml_load_file($cesta_k_souboru);
        $this->sx = $sx;
        $this->cesta_k_souboru = $cesta_k_souboru;
        $this->cesta_k_xsd_schematu = $cesta_k_xsd_schematu;

        parent::__construct();
    }

    public function get_cislo_aktualniho_kola() {
        $sx = $this->sx;
        return $sx->aktualni_info->kolo[0];
    }

    public function get_adresa_serveru() {
        return $this->sx->server_info->adresa[0];
    }

    public function get_pristup_k_databazi() {
        $pole_udaju_pro_pristup = array();
        $pole_udaju_pro_pristup['adresa_databazoveho_serveru'] = $this->sx->pristup_databaze->adresa_databazoveho_serveru[0];
        $pole_udaju_pro_pristup['login'] = $this->sx->pristup_databaze->login[0];
        $pole_udaju_pro_pristup['passwd'] = $this->sx->pristup_databaze->passwd[0];
        $pole_udaju_pro_pristup['jmeno_databaze'] = $this->sx->pristup_databaze->jmeno_databaze[0];
        $pole_udaju_pro_pristup['uzamceni_databaze'] = $this->sx->aktualni_info->uzamceni_databaze[0];
        return $pole_udaju_pro_pristup;
    }

    public function get_amortizacni_faktor() {
        return $this->sx->amortizace_kapitalu->amortizacni_faktor[0];
    }

    function validuj_konfiguracni_soubor() {
        $dom_xml = new DomDocument;
        $dom_xml->Load($this->cesta_k_souboru);

        if ($dom_xml->schemaValidate($this->cesta_k_xsd_schematu)) {
            return true;
        } else {
            return false;
        }
    }

    private function zmen_cislo_aktualniho_kola_o_jeden() {
        $sx = $this->sx;
        $sx->aktualni_info->kolo[0] = $sx->aktualni_info->kolo[0] + 1;
        file_put_contents($this->cesta_k_souboru, $sx->asXML());
    }

    public function get_produkcni_funkce() {
        return $this->sx->produkcni_funkce->funkce[0];
    }

    public function set_produkcni_funkce($produkcni_funkce) {
        $this->sx->produkcni_funkce->funkce[0] = $produkcni_funkce;
        file_put_contents($this->cesta_k_souboru, $this->sx->asXML());
    }

    public function get_hodnotici_funkce() {
        return $this->sx->hodnotici_funkce->funkce[0];
    }

    public function set_hodnotici_funkce($hodnotici_funkce) {
        $this->sx->hodnotici_funkce->funkce[0] = $hodnotici_funkce;
        file_put_contents($this->cesta_k_souboru, $this->sx->asXML());
    }

    public function get_aktivni_trhy() {
        $sx = $this->sx;
        $aktivni_trhy = array();

        foreach ($sx->trhy->trh as $aktualni_trh) {
            $aktivni_trhy[] = (String) $aktualni_trh['tabulka'];
        }

        foreach ($sx->trhy[0]->trhy_kapitalu[0]->trh_kapitalu as $aktualni_trh_kapitalu) {
            $aktivni_trhy[] = (String) $aktualni_trh_kapitalu['tabulka'];
        }

        return $aktivni_trhy;
    }

    public function get_pocatecni_hodnotu ($id_hodnoty) {
        $polozka = $this->sx->xpath("pocatecni_hodnoty/pocatecni_hodnota[@id_polozky = '" . $id_hodnoty . "' ]");
        return $polozka[0];
    }

    public function set_pocatecni_hodnotu ($id_hodnoty, $hodnota) {
        $sx = $this->sx;
        for ($i = 0; $i < 4 ; $i++) {
            if ($sx->pocatecni_hodnoty->pocatecni_hodnota[$i]['id_polozky'] == $id_hodnoty) {
                $sx->pocatecni_hodnoty->pocatecni_hodnota[$i] = $hodnota;
            }
        }
        file_put_contents($this->cesta_k_souboru, $sx->asXML());
    }

    public function get_pole_pocatecnich_cen () {
        $pole_pocatecnich_cen = array();
        $sx = $this->sx;

        foreach ($sx->pocatecni_ceny->pocatecni_cena as $aktualni_pocatecni_cena) {
            $pole_pocatecnich_cen[(String)$aktualni_pocatecni_cena['id_trhu']] = (String)$aktualni_pocatecni_cena;
        }

        return $pole_pocatecnich_cen;
    }

    public function ukonci_kolo($odesilatel, $parametry) {
        $this->zmen_cislo_aktualniho_kola_o_jeden();
    }

    public function nastav_aktualni_kolo_na_1($odesilatel, $parametry) {
        $sx = $this->sx;
        $sx->aktualni_info->kolo[0] = 1;
        file_put_contents($this->cesta_k_souboru, $sx->asXML());
    }

    public function set_amortizacni_faktor($amortizacni_faktor) {
        $sx = $this->sx;
        $sx->amortizace_kapitalu->amortizacni_faktor[0] = $amortizacni_faktor;
        file_put_contents($this->cesta_k_souboru, $sx->asXML());
    }

}
?>
