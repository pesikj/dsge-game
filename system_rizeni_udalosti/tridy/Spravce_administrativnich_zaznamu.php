<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_administrativnich_zaznamu
 *
 * @author pike
 */
class Spravce_administrativnich_zaznamu extends Trida_reagujici_na_udalosti{
    //put your code here

    private $cislo_kola;
    private $spravce_konfigurace;

    public function __construct() {
        $this->pole_identifikatoru_udalosti['KONEC_KOLA'] = 'proved_aministrativni_zaznam_o_konci_kola';
        $this->pole_identifikatoru_udalosti['RESET_HRY'] = 'smaz_data_o_prubehu_hry';
        parent::__construct();

        $this->cislo_kola = $GLOBALS['spravce_konfigurace']->get_cislo_aktualniho_kola();
        $this->spravce_konfigurace = $GLOBALS['spravce_konfigurace'];
    }

    function proved_aministrativni_zaznam_o_konci_kola($odesilatel, $parametry) {
        $dotaz_na_zjisteni_skladovanych_polozek = "SELECT * FROM skladovane_polozky WHERE kolo = " .
            $this->cislo_kola . "; ";
        $vysledek_skladovane_polozky = mysql_query($dotaz_na_zjisteni_skladovanych_polozek)
            or die ($dotaz_na_zjisteni_skladovanych_polozek);

        while ($radek = mysql_fetch_array($vysledek_skladovane_polozky)) {
            $dotaz_na_zapsani_polozek = "INSERT INTO skladovane_polozky VALUES ( '" . $radek['login'] . "', " .
                ($this->cislo_kola + 1) . ", " . $radek['mnozstvi_kapitalu'] . ", " . $radek['mnozstvi_spotrebniho_zbozi'] . ", " .
                $radek['mnozstvi_kapitaloveho_zbozi'] . "); ";
            mysql_query($dotaz_na_zapsani_polozek) or die ($dotaz_na_zapsani_polozek);
        }

        unset ($radek);

        $dotaz_na_zjisteni_kapitaloveho_zbozi_ve_vyrobe = "SELECT * FROM kapitalove_zbozi_ve_vyrobe WHERE kolo = " .
            $this->cislo_kola . "; ";
        $vysledek_kapitalove_zbozi_ve_vyrobe = mysql_query($dotaz_na_zjisteni_kapitaloveho_zbozi_ve_vyrobe)
            or die ($dotaz_na_zjisteni_kapitaloveho_zbozi_ve_vyrobe);

        $spravce_konfigurace = $this->spravce_konfigurace;
        $amortizacni_faktor = $spravce_konfigurace->get_amortizacni_faktor();
        settype($amortizacni_faktor, float);
        while ($radek = mysql_fetch_array($vysledek_kapitalove_zbozi_ve_vyrobe)) {
            $kapitalove_zbozi_vstupujici_do_dalsiho_kola = $radek['mnozstvi_kapitaloveho_zbozi'] * (1 - $amortizacni_faktor);
            $kapitalove_zbozi_vstupujici_do_dalsiho_kola = round($kapitalove_zbozi_vstupujici_do_dalsiho_kola);
            $dotaz_na_prevod_kapitaloveho_zbozi = "INSERT INTO kapitalove_zbozi_ve_vyrobe VALUES ( '" . $radek['login'] . "', " .
                ($this->cislo_kola + 1) . ", " . $kapitalove_zbozi_vstupujici_do_dalsiho_kola . "); ";
            mysql_query($dotaz_na_prevod_kapitaloveho_zbozi) or die ($dotaz_na_prevod_kapitaloveho_zbozi);
        }
    }

    public function smaz_data_o_prubehu_hry ($odesilatel, $parametry) {
        $spravce_konfigurace = $this->spravce_konfigurace;

        mysql_query ("TRUNCATE TABLE prodane_spotrebni_zbozi");
        mysql_query ("TRUNCATE TABLE prodane_kapitalove_zbozi");
        mysql_query ("TRUNCATE TABLE hodnoceni_hracu");
        mysql_query ("TRUNCATE TABLE nakoupena_prace");
        mysql_query ("TRUNCATE TABLE prodana_prace");
        mysql_query ("TRUNCATE TABLE spotreba");
        mysql_query ("TRUNCATE TABLE uspory_zapujcene_2_obdobi");
        mysql_query ("TRUNCATE TABLE uvery_ziskane_2_obdobi");
        mysql_query ("TRUNCATE TABLE vyvoj_trznich_cen");
        mysql_query ("TRUNCATE TABLE nakupy_kapitaloveho_zbozi");
        mysql_query ("TRUNCATE TABLE skladovane_polozky");
        mysql_query ("TRUNCATE TABLE statistika_kola");
        mysql_query ("TRUNCATE TABLE trh_kapitaloveho_zbozi_nabidka");
        mysql_query ("TRUNCATE TABLE trh_kapitaloveho_zbozi_poptavka");
        mysql_query ("TRUNCATE TABLE trh_kapitalu_2_obdobi_nabidka");
        mysql_query ("TRUNCATE TABLE trh_kapitalu_2_obdobi_poptavka");
        mysql_query ("TRUNCATE TABLE trh_prace_nabidka");
        mysql_query ("TRUNCATE TABLE trh_prace_poptavka");
        mysql_query ("TRUNCATE TABLE trh_spotrebniho_zbozi_nabidka");
        mysql_query ("TRUNCATE TABLE trh_spotrebniho_zbozi_poptavka");
        mysql_query ("TRUNCATE TABLE zaznamy_o_produkci");
        mysql_query ("TRUNCATE TABLE kapitalove_zbozi_ve_vyrobe");
        mysql_query ("TRUNCATE TABLE prikazy_produkce");
        mysql_query ("TRUNCATE TABLE aktivita_hracu");

        $dotaz_na_seznam_loginu = "SELECT login FROM hraci";
        $vysledek_seznam_loginu = mysql_query($dotaz_na_seznam_loginu) or die ($dotaz_na_seznam_loginu);

        while ($radek = mysql_fetch_array($vysledek_seznam_loginu)) {
            $login = $radek['login'];
            $dotaz_na_vlozeni_vychozich_hodnot = "INSERT INTO skladovane_polozky VALUES ('" . $login . "', 1, " .
                 $spravce_konfigurace->get_pocatecni_hodnotu("kapital") . ", " .
                 $spravce_konfigurace->get_pocatecni_hodnotu("spotrebni_zbozi_na_sklade") . ", " .
                 $spravce_konfigurace->get_pocatecni_hodnotu("kapitalove_zbozi_na_sklade") . "); ";
            mysql_query($dotaz_na_vlozeni_vychozich_hodnot) or die ($dotaz_na_vlozeni_vychozich_hodnot);

            $dotaz_kapitalove_zbozi_ve_vyrobe = "INSERT INTO kapitalove_zbozi_ve_vyrobe VALUES ('" . $login . "', 1, " .
                 $spravce_konfigurace->get_pocatecni_hodnotu("kapitalove_zbozi_ve_vyrobe") . "); ";
            mysql_query($dotaz_kapitalove_zbozi_ve_vyrobe) or die ($dotaz_kapitalove_zbozi_ve_vyrobe);
        }

        foreach ($spravce_konfigurace->get_pole_pocatecnich_cen() as $aktualni_klic => $aktualni_cena) {
            $dotaz_na_vlozeni_pocatecni_ceny = "INSERT INTO vyvoj_trznich_cen VALUES (0, '" . $aktualni_klic . "', " . $aktualni_cena . "); ";
            mysql_query($dotaz_na_vlozeni_pocatecni_ceny) or die ($dotaz_na_vlozeni_pocatecni_ceny);
        }

    }
}
?>
