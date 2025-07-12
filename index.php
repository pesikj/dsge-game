<?php
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
        if (file_exists('inc/' . $class_name . '.php')) {
            require_once ('inc/' . $class_name . '.php');
        }
    }

    require_once 'inc/mysql_connect.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();
    $spravce_konfigurace = new spravce_konfigurace();
    $cislo_aktualniho_kola = $spravce_konfigurace->get_cislo_aktualniho_kola();

    $hrac = null;
    vytvor_objekt_reprezentujici_hrace();
    $spravce_dat_z_formularu = new Spravce_dat_z_formularu($_POST);

    $spravce_GUI = new spravce_GUI();

    $hrac->registruj_ovladac($spravce_GUI->generuj_ovladac_na_udalost('ZADOST_O_STRANKU'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_GUI->generuj_ovladac_na_udalost('ODESLANI_FORMULARE_NABIDKY'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_GUI->generuj_ovladac_na_udalost('ODESLANI_FORMULARE_POPTAVKY'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_GUI->generuj_ovladac_na_udalost('ODESLANI_FORMULARE_PRODUKCE'));
    
    $spravce_zaznamu_o_aktivite = new Spravce_zaznamu_o_aktivite();
    $spravce_dat_z_formularu->registruj_ovladac($spravce_zaznamu_o_aktivite->generuj_ovladac_na_udalost('ODESLANI_FORMULARE_POPTAVKY'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_zaznamu_o_aktivite->generuj_ovladac_na_udalost('ODESLANI_FORMULARE_NABIDKY'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_zaznamu_o_aktivite->generuj_ovladac_na_udalost('ODESLANI_FORMULARE_PRODUKCE'));

    $spravce_administrativnich_zaznamu = new Spravce_administrativnich_zaznamu();
    $spravce_dat_z_formularu->registruj_ovladac($spravce_administrativnich_zaznamu->generuj_ovladac_na_udalost('RESET_HRY'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_konfigurace->generuj_ovladac_na_udalost('RESET_HRY'));

    $spravce_trhu = new Spravce_trhu();
    $spravce_produkce = new spravce_produkce();
    $spravce_uveru = new spravce_uveru();
    $spravce_hodnocení = new Spravce_hodnoceni();
    $spravce_administrativnich_zaznamu = new Spravce_administrativnich_zaznamu();

    $spravce_dat_z_formularu->registruj_ovladac($spravce_trhu->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_produkce->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_uveru->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_hodnocení->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_administrativnich_zaznamu->generuj_ovladac_na_udalost('KONEC_KOLA'));
    $spravce_dat_z_formularu->registruj_ovladac($spravce_konfigurace->generuj_ovladac_na_udalost('KONEC_KOLA'));


    $spravce_dat_z_formularu->registruj_ovladac($spravce_administrativnich_zaznamu->generuj_ovladac_na_udalost('ZADOST_NOVEHO_HRACE_O_ZAPOJENI_DO_HRY'));

    $spravce_dat_z_formularu->spust_zpracovani_dat();
    $hrac->pozadavek_hrace($_GET);

    function vytvor_objekt_reprezentujici_hrace() {
        if (getenv("REMOTE_USER") != false) {
            $login = getenv("REMOTE_USER");
            $hrac = new hrac($login);
        } else if (isset ($_SESSION['login'])) {
            $login = $_SESSION['login'];
            $hrac = new hrac($login);
        } else {
            $hrac = new hrac("anonymous");
            $GLOBALS['hrac'] = $hrac;
            return;
        }
        
        $GLOBALS['hrac'] = $hrac;
    }
?>