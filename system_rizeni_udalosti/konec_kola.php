<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    session_start();

    function __autoload($class_name) {
        if (file_exists('udalosti/' . $class_name . '.php')) {
            require_once ('udalosti/' . $class_name . '.php');
        }
        if (file_exists('tridy/' . $class_name . '.php')) {
            require_once ('tridy/' . $class_name . '.php');
        }
        if (file_exists('tridy/spravci_GUI/' . $class_name . '.php')) {
            require_once ('tridy/spravci_GUI/' . $class_name . '.php');
        }
        if (file_exists('formulare/' . $class_name . '.php')) {
            require_once ('formulare/' . $class_name . '.php');
        }
    }

    require_once 'inc/mysql_connect.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();
    $spravce_konfigurace = new spravce_konfigurace();
    $cislo_aktualniho_kola = $spravce_konfigurace->get_cislo_aktualniho_kola();

    $casovac = new Casovac();

    $spravce_trhu = new Spravce_trhu();
    $spravce_produkce = new spravce_produkce();
    $spravce_uveru = new spravce_uveru();
    $spravce_hodnocení = new Spravce_hodnoceni();
    $spravce_administrativnich_zaznamu = new Spravce_administrativnich_zaznamu();

    
    $casovac->registruj_ovladac($spravce_trhu->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $casovac->registruj_ovladac($spravce_produkce->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $casovac->registruj_ovladac($spravce_uveru->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $casovac->registruj_ovladac($spravce_hodnocení->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $casovac->registruj_ovladac($spravce_administrativnich_zaznamu->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $casovac->registruj_ovladac($spravce_konfigurace->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $casovac->zpracuj_signal_casovace();
?>
