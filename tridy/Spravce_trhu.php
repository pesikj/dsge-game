<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_trhu
 *
 * @author pike
 */
class Spravce_trhu extends Trida_reagujici_na_udalosti{
    //put your code here

    private $pole_trhu;
    private $kolo;

    public function __construct() {
        $this->pole_identifikatoru_udalosti['KONEC_KOLA'] = 'zobchoduj_polozky_na_vsech_trzich';
        $this->kolo = $GLOBALS['spravce_konfigurace']->get_cislo_aktualniho_kola();

        parent::__construct();
    }

    public function zmena_hotovosti () {


        $dotaz = "SELECT * FROM skladovane_polozky WHERE kolo = " . ($this->kolo - 1) . ";";
        $vysledek = mysql_query($dotaz);


        while ($radek = mysql_fetch_array($vysledek)) {
            $dotaz_uprava = "UPDATE skladovane_polozky SET mnozstvi_kapitalu = " . $radek['mnozstvi_kapitalu'] . ", mnozstvi_spotrebniho_zbozi = ".
                $radek['mnozstvi_spotrebniho_zbozi'] . ", mnozstvi_kapitaloveho_zbozi = " . $radek['mnozstvi_kapitaloveho_zbozi'] .
                " WHERE login = '" .
                $radek['login'] . "' AND kolo = " . $this->kolo . ";";
                echo $dotaz_uprava;
            mysql_query($dotaz_uprava);
        }
        
        unset ($radek);

        $dotaz_3 = "SELECT * FROM skladovane_polozky WHERE kolo = " . ($this->kolo - 1) . ";";
        $vysledek_3 = mysql_query($dotaz_3);

        while ($radek = mysql_fetch_array($vysledek_3)) {
            $dotaz_uprava_2 = "UPDATE kapitalove_zbozi_ve_vyrobe SET mnozstvi_kapitaloveho_zbozi =" . $radek['mnozstvi_kapitaloveho_zbozi'] . " WHERE login = '" .
                $radek['login'] . "' AND kolo = " . $radek['kolo'];

            mysql_query($dotaz_uprava_2);
        }
    }

    public function zobchoduj_polozky_na_vsech_trzich ($odesilatel, $parametry) {
        //$this->zmena_hotovosti();
        $spravce_konfigurace = $GLOBALS['spravce_konfigurace'];
        $pole_trhu = array();
        foreach ($spravce_konfigurace->get_aktivni_trhy() as $aktualni_polozka) {
            $novy_trh = new trh($aktualni_polozka, $spravce_konfigurace->get_cislo_aktualniho_kola());
            $novy_trh->zobchoduj_polozky_na_trhu();
            $pole_trhu[$aktualni_polozka] = $novy_trh;
        }
        $this->pole_trhu = $pole_trhu;
        $this->zaznam_trznich_cen();
    }

    private function zaznam_trznich_cen() {
        $pole_trhu = $this->pole_trhu;
        foreach ($pole_trhu as $nazev_trhu => $trh) {
            $dotaz_na_zaznam_trzni_ceny = "INSERT INTO vyvoj_trznich_cen VALUES (" . $this->kolo .
                ", '" . $nazev_trhu . "', " . $trh->getTrzni_cena() . "); ";

            mysql_query($dotaz_na_zaznam_trzni_ceny) or die ($dotaz_na_zaznam_trzni_ceny);
        }
    }
}
?>
