<?php
    session_start();
    require_once 'tridy/trh.php';


    include 'html/html_struktura.php';
    vloz_xhtml_hlavicku();

?>
<div id="wrap">
    <div id="header"><h1>Simulační hra</h1></div>

<?php
    $default_data = array(10 => 0, 20 => 0, 30 => 0, 40 => 0);
    $pole_chyb = array();
    include_once('inc/mysql_connect.php');
    require_once('inc/config.php');
    $hrac = vytvor_objekt_reprezentujici_hrace();
    vloz_horizontalni_pruh($hrac);

    echo "<div id=\"main\">";
    if ( $_SESSION['auth'] != 1) {
        include 'formulare/prihlasovaci_formular.php';
    } else {
        if (isset ($_POST['nazev_tabulky'])) {
            zpracuj_vysledek_formulare();
        }

        vloz_obsah_stranky_pro_prislusny_trh();

        echo "</div>";

        vloz_menu($_SESSION['login']);
        
        echo "<div id=\"footer\">";
	echo "<p>Jiří Pešík</p>";
	echo "</div>";
        echo "</div>";


        include 'html/foot.php';
    }

    function vytvor_objekt_reprezentujici_hrace() {
        include_once ('tridy/hrac.php');
        $hrac = new hrac($_SESSION['login']);
        $dotaz = "SELECT mnozstvi_kapitalu, mnozstvi_spotrebniho_zbozi_na_sklade," .
            "mnozstvi_kapitaloveho_zbozi_na_sklade FROM hraci WHERE login='" .
            $_SESSION['login'] . "';";
        $vysledek = mysql_query($dotaz);
        $udaje_o_hraci = mysql_fetch_assoc($vysledek);
        extract($udaje_o_hraci);
        $hrac->setMnozstvi_spotrebniho_zbozi($mnozstvi_spotrebniho_zbozi_na_sklade);
        $hrac->setMnozstvi_kapitalu($mnozstvi_kapitalu);
        $hrac->setMnozstvi_kapitaloveho_zbozi($mnozstvi_kapitaloveho_zbozi_na_sklade);

        return $hrac;
    }

    function vloz_obsah_stranky_pro_prislusny_trh() {
        if ($_GET['id_trhu'] == 'trh_spotrebniho_zbozi') {
            echo '<h3>Trh spotřebního zboží</h3>';

            zjisti_a_vloz_trzni_cenu_z_minuleho_kola();
            zjisti_existenci_nabidky_pro_toto_kolo("trh_spotrebniho_zbozi_nabidka", "cena");
            zjisti_existenci_poptavky_pro_toto_kolo("trh_spotrebniho_zbozi_poptavka", "cena");

        } else if ($_GET['id_trhu'] == 'trh_kapitaloveho_zbozi') {
            echo '<h3>Trh kapitálového zboží</h3>';

            zjisti_a_vloz_trzni_cenu_z_minuleho_kola();
            zjisti_existenci_nabidky_pro_toto_kolo("trh_kapitaloveho_zbozi_nabidka", "cena");
            zjisti_existenci_poptavky_pro_toto_kolo("trh_kapitaloveho_zbozi_poptavka", "cena");
        } else if ($_GET['id_trhu'] == 'trh_prace') {
            echo '<h3>Trh práce</h3>';

            $dotaz = "SELECT hodin_prace FROM prikazy_produkce WHERE login='" . $_SESSION['login'] .
                "' AND kolo= " . $GLOBALS['kolo'] . ";";
            $vysledek = mysql_query($dotaz);
            $radek = mysql_fetch_array($vysledek);
            if ($radek != false) {
                extract($radek);
                echo "Maximálně můžete nabízet " . (24 - $hodin_prace) . " hodin práce. <br/>";
            } else {
                echo "Maximálně můžete nabízet 24 hodin práce. <br/>";
            }

            zjisti_a_vloz_trzni_cenu_z_minuleho_kola();

            zjisti_existenci_nabidky_pro_toto_kolo("trh_prace_nabidka", "hodinová mzda");
            zjisti_existenci_poptavky_pro_toto_kolo("trh_prace_poptavka", "hodinová mzda");
        } else if ($_GET['id_trhu'] == 'trh_kapitalu_2_obdobi') {
            echo '<h3>Trh kapitálu, 2 období</h3>';

            zjisti_a_vloz_trzni_cenu_z_minuleho_kola();
            zjisti_existenci_nabidky_pro_toto_kolo("trh_kapitalu_2_obdobi_nabidka", "úroková míra (v %)");
            zjisti_existenci_poptavky_pro_toto_kolo("trh_kapitalu_2_obdobi_poptavka", "úroková míra (v %)");
        } else {
            echo "Tento trh neexistuje";
        }
    }

    function zjisti_a_vloz_trzni_cenu_z_minuleho_kola() {
        $dotaz_na_trzni_cenu_z_minuleho_kola = "SELECT " . $_GET['id_trhu'] . " FROM vyvoj_trznich_cen WHERE kolo = " .
            ($GLOBALS['kolo'] - 1) . "; ";
        $vysledek = mysql_query($dotaz_na_trzni_cenu_z_minuleho_kola);
        $radek = mysql_fetch_array($vysledek);
        $trzni_cena_v_minulem_kole = $radek[$_GET['id_trhu']];
        echo "Tržní cena v minulém kole byla: " . $trzni_cena_v_minulem_kole . "<br />";
    }

    function zjisti_existenci_nabidky_pro_toto_kolo($nazev_tabulky, $oznaceni_P) {
        include_once 'formulare/formular_nabidka.php';
        include_once 'tridy/individualni_nabidka.php';

        $pole_chyb = $GLOBALS['pole_chyb'];

        $dotaz = "SELECT * FROM ". $nazev_tabulky .
            " WHERE kolo = " . $GLOBALS['kolo'] . " AND login = '" . $_SESSION['login'] ."'; ";
        $vysledek = mysql_query($dotaz);
        $individualni_nabidka = new individualni_nabidka($_SESSION['login'], $GLOBALS['kolo']);

        if (sizeof($pole_chyb) == 0) {
            while ($radek = mysql_fetch_array($vysledek)) {
                $individualni_nabidka->pridej_parametr_nabidky($radek['cena1'], $radek['mnozstvi1']);
                $individualni_nabidka->pridej_parametr_nabidky($radek['cena2'], $radek['mnozstvi2']);
                $individualni_nabidka->pridej_parametr_nabidky($radek['cena3'], $radek['mnozstvi3']);
                $individualni_nabidka->pridej_parametr_nabidky($radek['cena4'], $radek['mnozstvi4']);
                $GLOBALS['default_data'] = $individualni_nabidka->getPole_meznich_cen();
            }
        }

        $formular = new formular_nabidka();
        $formular->generuj_formular_nabidky($GLOBALS['default_data'], $nazev_tabulky, $pole_chyb, $oznaceni_P);
    }

    function zjisti_existenci_poptavky_pro_toto_kolo($nazev_tabulky, $oznaceni_P) {
        include_once 'formulare/formular_poptavka.php';
        include_once 'tridy/individualni_poptavka.php';
        $dotaz = "SELECT * FROM " . $nazev_tabulky .
            " WHERE kolo = " . $GLOBALS['kolo'] . " AND login = '" . $_SESSION['login'] ."'; ";
        $vysledek = mysql_query($dotaz);
        $individualni_poptavka = new individualni_poptavka($_SESSION['login'], $GLOBALS['kolo']);

        $default_data = array(10 => 0, 20 => 0, 30 => 0, 40 => 0);
        while ($radek = mysql_fetch_array($vysledek)) {
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena1'], $radek['mnozstvi1']);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena2'], $radek['mnozstvi2']);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena3'], $radek['mnozstvi3']);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena4'], $radek['mnozstvi4']);
            $default_data = $individualni_poptavka->getPole_meznich_cen();
        }

        $formular = new formular_poptavka();
        $formular->generuj_formular_poptavky($default_data, $nazev_tabulky, $oznaceni_P);
    }

    function zpracuj_vysledek_formulare() {
        //Očistíme data od nečíselných znaků
        $_POST['cena1'] = preg_replace("/\D/", "", $_POST['cena1']);
        $_POST['cena2'] = preg_replace("/\D/", "", $_POST['cena2']);
        $_POST['cena3'] = preg_replace("/\D/", "", $_POST['cena3']);
        $_POST['cena4'] = preg_replace("/\D/", "", $_POST['cena4']);
        $_POST['mnozstvi1'] = preg_replace("/\D/", "", $_POST['mnozstvi1']);
        $_POST['mnozstvi2'] = preg_replace("/\D/", "", $_POST['mnozstvi2']);
        $_POST['mnozstvi3'] = preg_replace("/\D/", "", $_POST['mnozstvi3']);
        $_POST['mnozstvi4'] = preg_replace("/\D/", "", $_POST['mnozstvi4']);

        if ($_GET['typ_formulare'] == 'poptavka') {
            include_once ('tridy/individualni_poptavka.php');
            $nova_poptavka = new individualni_poptavka($_SESSION['login'], $GLOBALS['kolo']);
            $nova_poptavka->pridej_parametr_poptavky($_POST['cena1'], $_POST['mnozstvi1']);
            $nova_poptavka->pridej_parametr_poptavky($_POST['cena2'], $_POST['mnozstvi2']);
            $nova_poptavka->pridej_parametr_poptavky($_POST['cena3'], $_POST['mnozstvi3']);
            $nova_poptavka->pridej_parametr_poptavky($_POST['cena4'], $_POST['mnozstvi4']);
            $nova_poptavka->vypoctiPoptavku();

            $nazev_tabulky = $_POST['nazev_tabulky'];

            include_once ('inc/mysql_connect.php');

            $dotaz_na_overeni_existence_poptavky = "SELECT * FROM " . $nazev_tabulky .
                " WHERE login='" . $_SESSION['login'] ."' AND kolo= " . $GLOBALS['kolo'] . ";";
            $vysledek_kontroly = mysql_query($dotaz_na_overeni_existence_poptavky);

            $dotaz = "";
            if (mysql_num_rows($vysledek_kontroly) == 1) {
                $dotaz = $nova_poptavka->vytvor_SQL_definici($nazev_tabulky, $_SESSION['login'], false);
            } else {
                $dotaz = $nova_poptavka->vytvor_SQL_definici($nazev_tabulky, $_SESSION['login'], true);
            }


            mysql_query($dotaz);

        } else if ($_GET['typ_formulare'] == 'nabidka') {
            include_once ('inc/mysql_connect.php');

            $pole_chyb = zkontroluj_nabidku();

            if (sizeof($pole_chyb) > 0) {
                $GLOBALS['pole_chyb'] = $pole_chyb;
                $default_data = array();
                $default_data[$_POST['cena1']] = $_POST['mnozstvi1'];
                $default_data[$_POST['cena2']] = $_POST['mnozstvi2'];
                $default_data[$_POST['cena3']] = $_POST['mnozstvi3'];
                $default_data[$_POST['cena4']] = $_POST['mnozstvi4'];
                $GLOBALS['default_data'] = $default_data;
                return;
            }

            include_once ('tridy/individualni_nabidka.php');
            $nova_nabidka = new individualni_nabidka($_SESSION['login'], $GLOBALS['kolo']);
            $nova_nabidka->pridej_parametr_nabidky($_POST['cena1'], $_POST['mnozstvi1']);
            $nova_nabidka->pridej_parametr_nabidky($_POST['cena2'], $_POST['mnozstvi2']);
            $nova_nabidka->pridej_parametr_nabidky($_POST['cena3'], $_POST['mnozstvi3']);
            $nova_nabidka->pridej_parametr_nabidky($_POST['cena4'], $_POST['mnozstvi4']);
            $nova_nabidka->vypocti_nabidku();

            $nazev_tabulky = $_POST['nazev_tabulky'];

            $dotaz_na_overeni_existence_nabidky = "SELECT * FROM " . $nazev_tabulky .
                " WHERE login='" . $_SESSION['login'] . "' AND kolo=" . $GLOBALS['kolo'] . ";";
            $vysledek_kontroly = mysql_query($dotaz_na_overeni_existence_nabidky);

            $dotaz = "";
            if (mysql_num_rows($vysledek_kontroly) == 1) {
                $dotaz = $nova_nabidka->vytvor_SQL_definici($nazev_tabulky, $_SESSION['login'], false);
            } else {
                $dotaz = $nova_nabidka->vytvor_SQL_definici($nazev_tabulky, $_SESSION['login'], true);
            }

            mysql_query($dotaz);
        }
    }

    //E - kritická chyba, překročení velikosti skladovaných zásob - příkaz není uložen
    //
    function zkontroluj_nabidku () {
        $hrac = $GLOBALS['hrac'];
        $pole_chyb = array ();
        if ($_POST['nazev_tabulky'] == 'trh_prace_nabidka') {
            $dotaz = "SELECT hodin_prace FROM prikazy_produkce WHERE login='" . $hrac->getLogin() .
                "' AND kolo = " . $GLOBALS['kolo'] . ";";
            $vysledek = mysql_query($dotaz);
            $radek = mysql_fetch_array($vysledek);
            //Příkaz k vlastní produkci dosud nemusel být zadán, pak není třeba testovat chybu sumace
            if ($radek != false) {
                extract($radek);
                if ($_POST['mnozstvi4'] > (24 - $hodin_prace)) {
                    $pole_chyb[4] = "E";
                }
                if ($_POST['mnozstvi3'] > (24 - $hodin_prace)) {
                    $pole_chyb[3] = "E";
                }
                if ($_POST['mnozstvi2'] > (24 - $hodin_prace)) {
                    $pole_chyb[2] = "E";
                }
                if ($_POST['mnozstvi1'] > (24 - $hodin_prace)) {
                    $pole_chyb[1] = "E";
                }
            } else {
                if ($_POST['mnozstvi4'] > (24)) {
                    $pole_chyb[4] = "E";
                }
                if ($_POST['mnozstvi3'] > (24)) {
                    $pole_chyb[3] = "E";
                }
                if ($_POST['mnozstvi2'] > (24)) {
                    $pole_chyb[2] = "E";
                }
                if ($_POST['mnozstvi1'] > (24)) {
                    $pole_chyb[1] = "E";
                }
            }
            
        } else if ($_POST['nazev_tabulky'] == 'trh_spotrebniho_zbozi_nabidka') {
            if ($_POST['mnozstvi4'] > $hrac->getMnozstvi_spotrebniho_zbozi()) {
                $pole_chyb[4] = "E";
            }
            if ($_POST['mnozstvi3'] > $hrac->getMnozstvi_spotrebniho_zbozi()) {
                $pole_chyb[3] = "E";
            }
            if ($_POST['mnozstvi2'] > $hrac->getMnozstvi_spotrebniho_zbozi()) {
                $pole_chyb[2] = "E";
            }
            if ($_POST['mnozstvi1'] > $hrac->getMnozstvi_spotrebniho_zbozi()) {
                $pole_chyb[1] = "E";
            }
        } else if ($_POST['nazev_tabulky'] == 'trh_kapitaloveho_zbozi_nabidka') {
            if ($_POST['mnozstvi4'] > $hrac->getMnozstvi_kapitaloveho_zbozi()) {
                $pole_chyb[4] = "E";
            }
            if ($_POST['mnozstvi3'] > $hrac->getMnozstvi_kapitaloveho_zbozi()) {
                $pole_chyb[3] = "E";
            }
            if ($_POST['mnozstvi2'] > $hrac->getMnozstvi_kapitaloveho_zbozi()) {
                $pole_chyb[2] = "E";
            }
            if ($_POST['mnozstvi1'] > $hrac->getMnozstvi_kapitaloveho_zbozi()) {
                $pole_chyb[1] = "E";
            }
        } else if (strpos( $_POST['nazev_tabulky'],'kapitalu') > 0) {
            if ($_POST['mnozstvi4'] > $hrac->getMnozstvi_kapitalu()) {
                $pole_chyb[4] = "E";
            }
            if ($_POST['mnozstvi3'] > $hrac->getMnozstvi_kapitalu()) {
                $pole_chyb[3] = "E";
            }
            if ($_POST['mnozstvi2'] > $hrac->getMnozstvi_kapitalu()) {
                $pole_chyb[2] = "E";
            }
            if ($_POST['mnozstvi1'] > $hrac->getMnozstvi_kapitalu()) {
                $pole_chyb[1] = "E";
            }
        }
        return $pole_chyb;
    }
?>
