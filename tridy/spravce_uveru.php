<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of spravce_uveru
 *
 * @author pike
 */
class spravce_uveru extends Trida_reagujici_na_udalosti {
    //put your code here

    private $aktualni_kolo;

    function __construct() {
        $this->pole_identifikatoru_udalosti['KONEC_KOLA'] = 'zuctuj_existujici_uvery';
        $this->aktualni_kolo = $GLOBALS['spravce_konfigurace']->get_cislo_aktualniho_kola();
    }

    public function zuctuj_existujici_uvery ($odesilatel, $parametry) {
        $spravce_konfigurace = $GLOBALS['spravce_konfigurace'];
        $pole_trhu = array();
        foreach ($spravce_konfigurace->get_aktivni_trhy() as $aktualni_polozka) {
            if (strstr($aktualni_polozka, 'kapitalu') != false) {
                $this->zuctuj_uvery_z_daneho_trhu($aktualni_polozka);
            }
        }
    }

    private function zuctuj_uvery_z_daneho_trhu($nazev_trhu) {
        $delka_uveru = preg_replace("/\D/", "", $nazev_trhu);
        $dotaz_na_uvery = "SELECT * FROM uvery_ziskane_" . $delka_uveru . "_obdobi;";
        $vysledek_uvery = mysql_query($dotaz_na_uvery) or die ($dotaz_na_uvery);
        $vyplacene_uroky_celkem = 0;

        while ($radek = mysql_fetch_array($vysledek_uvery)) {
            $kolo = $radek['kolo'];
            $castka = $radek['castka'];
            $login = $radek['login'];
            $hrac = new hrac($login);

            $dotaz_na_urokovou_miru = "SELECT cena FROM vyvoj_trznich_cen WHERE kolo = "
                . $kolo . " AND nazev_trhu = '" . $nazev_trhu . "'; ";
            $vysledek = mysql_query($dotaz_na_urokovou_miru);
            $radek_mira = mysql_fetch_array($vysledek);
            $urokova_mira = $radek_mira['cena'];
            $urokova_mira /= 100;
            $urok = 0;

            if (($kolo < $this->aktualni_kolo) && (($kolo + $delka_uveru) >= $this->aktualni_kolo)) {
                $urok = $urokova_mira * $castka;
                $hrac->sniz_mnozstvi_kapitalu($urok);
            }
            if ($kolo + $delka_uveru == $this->aktualni_kolo) {
                $hrac->sniz_mnozstvi_kapitalu($castka);
            }
        }

        unset ($radek);

        $dotaz_na_uspory = "SELECT * FROM uspory_zapujcene_" . $delka_uveru . "_obdobi;";
        $vysledek_uspory = mysql_query($dotaz_na_uspory);
        $prijate_uroky_celkem = 0;

        while ($radek = mysql_fetch_array($vysledek_uspory)) {
            $kolo = $radek['kolo'];
            $castka = $radek['castka'];
            $login = $radek['login'];

            $hrac = new hrac($login);

            $dotaz_na_urokovou_miru = "SELECT cena FROM vyvoj_trznich_cen WHERE kolo = "
                . $kolo . " AND nazev_trhu = '" . $nazev_trhu . "'; ";
            $vysledek = mysql_query($dotaz_na_urokovou_miru) or die ($dotaz_na_urokovou_miru);
            $radek_mira = mysql_fetch_array($vysledek);
            $urokova_mira = $radek_mira['cena'];
            $urokova_mira /= 100;
            $urok = 0;

            if (($kolo < $this->aktualni_kolo) && (($kolo + $delka_uveru) >= $this->aktualni_kolo)) {
                $urok = $urokova_mira * $castka;
                $hrac->zvys_mnozstvi_kapitalu($urok);
            }
            if ($kolo + $delka_uveru == $this->aktualni_kolo) {
                $hrac->zvys_mnozstvi_kapitalu($castka);
            }
        }
    }
}
?>
