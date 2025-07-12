<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of spravce_produkce
 *
 * @author pike
 */
class spravce_produkce extends Trida_reagujici_na_udalosti {
    //put your code here

    private $kolo;
    private $pole_individualnich_produkcnich_funkci;

    function __construct() {
        $this->pole_identifikatoru_udalosti['KONEC_KOLA'] = 'pripis_vysledky_produkce_vyrobcum';

        $this->kolo = $GLOBALS['spravce_konfigurace']->get_cislo_aktualniho_kola();

        parent::__construct();
    }

    private function urci_parametry_individualnich_produkcnich_funkci() {
        $dotaz_na_zjisteni_loginu = "SELECT login FROM prikazy_produkce WHERE kolo = " . $this->kolo . ";";
        $vysledek = mysql_query($dotaz_na_zjisteni_loginu);
        $pole_individualnich_produkcnich_funkci = array();

        while ($radek = mysql_fetch_array($vysledek)) {
            extract($radek);
            $dotaz_na_mnozstvi_kapitaloveho_zbozi = "SELECT mnozstvi_kapitaloveho_zbozi FROM kapitalove_zbozi_ve_vyrobe " .
                "WHERE login='" . $login . "' AND kolo = " . $this->kolo . ";";
            $vysledek_mnozstvi_kapitaloveho_zbozi = mysql_query($dotaz_na_mnozstvi_kapitaloveho_zbozi);
            $radek_mnozstvi_kapitaloveho_zbozi = mysql_fetch_array($vysledek_mnozstvi_kapitaloveho_zbozi);
            extract($radek_mnozstvi_kapitaloveho_zbozi);

            $mnozstvi_prace = 0;

            $dotaz_na_prikaz_produkce = "SELECT hodin_prace, druh_zbozi FROM prikazy_produkce WHERE login='"
                . $login . "' AND kolo = " . $this->kolo . ";";
            $vysledek_prikaz_produkce = mysql_query($dotaz_na_prikaz_produkce) or die ($dotaz_na_prikaz_produkce);
            if (mysql_num_rows($vysledek_prikaz_produkce) == 1) {
                $radek_prikaz_produkce = mysql_fetch_array($vysledek_prikaz_produkce);
                $mnozstvi_prace += $radek_prikaz_produkce['hodin_prace'];
            }

            $dotaz_na_nakoupenou_praci = "SELECT hodin_prace FROM nakoupena_prace WHERE login='" .
                $login . "' AND kolo = " . $this->kolo . ";";
            $vysledek_nakoupena_prace = mysql_query($dotaz_na_nakoupenou_praci);
            if (mysql_num_rows($vysledek_nakoupena_prace) > 0) {
                $radek_nakoupena_prace = mysql_fetch_array($vysledek_nakoupena_prace);
                extract($radek_nakoupena_prace);
                $mnozstvi_prace += $hodin_prace;
            }

            $individualni_produkcni_funkce = new individualni_produkcni_funkce($login, $mnozstvi_prace,
                $mnozstvi_kapitaloveho_zbozi, $this->kolo, $radek_prikaz_produkce['druh_zbozi']);
            $velikost_produkce = $individualni_produkcni_funkce->vypocti_velikost_produkce();
            
            $pole_individualnich_produkcnich_funkci[] = $individualni_produkcni_funkce;
        }

        $this->pole_individualnich_produkcnich_funkci = $pole_individualnich_produkcnich_funkci;
    }

    public function pripis_vysledky_produkce_vyrobcum () {
        $this->urci_parametry_individualnich_produkcnich_funkci();

        foreach ($this->pole_individualnich_produkcnich_funkci as $individualni_produkcni_funkce) {
            $individualni_produkcni_funkce->vygeneruj_a_proved_dotaz_na_upravdu_databaze();
        }
    }
    

}
?>
