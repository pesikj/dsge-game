<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    function __autoload($class_name) {
        $pole_adresaru_se_soubory[] = 'udalosti/';
        $pole_adresaru_se_soubory[] = 'tridy/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_GUI/';
        $pole_adresaru_se_soubory[] = 'tridy/toky/';
        $pole_adresaru_se_soubory[] = 'tridy/subjekty/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_trhu/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_rozesilani_emailu/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_danove_soustavy/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_migrace/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_vyroby/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_obligaci/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_hodnoceni/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_menovych_kurzu/';
        $pole_adresaru_se_soubory[] = 'tridy/komponenty_lidskeho_kapitalu/';
        $pole_adresaru_se_soubory[] = 'formulare/';
        $pole_adresaru_se_soubory[] = 'inc/';

        foreach ($pole_adresaru_se_soubory as $aktualni_adresar) {
            if (file_exists($aktualni_adresar . $class_name . '.php')) {
                require_once ($aktualni_adresar . $class_name . '.php');
            }
        }
    }
    

    session_start();
    require_once 'inc/mysql_connect.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();
    $casovac = new Casovac();
    $prekladac = new Prekladac();

    $integracni_celek = null;
    $cislo_aktualniho_kola = null;
    $ekonomika = null;


    $dotaz_na_integracni_celky = "SELECT * FROM integracni_celky; ";
    $vysledek_integracni_celky = mysql_query($dotaz_na_integracni_celky) or die($dotaz_na_integracni_celky);
    while ($radek_integracni_celky = mysql_fetch_assoc($vysledek_integracni_celky)) {
        $novy_integracni_celek = new Integracni_celek($radek_integracni_celky['id_integracniho_celku']);
        $casovac->registruj_ovladac($novy_integracni_celek->generuj_ovladac_na_udalost('CASOVY_SIGNAL'));
    }
    @set_time_limit(100000);
    $casovac->vysli_casovy_signal();

?>
