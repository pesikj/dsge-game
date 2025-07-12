<?php
    session_start();
    require_once 'tridy/prikaz_produkce.php';


    include 'html/html_struktura.php';
    vloz_xhtml_hlavicku();

?>
<div id="wrap">
    <div id="header"><h1>Simulační hra</h1></div>

<?php
    $default_data = array('hodin_prace' => 0, 'druh_zbozi' => 1);
    $pole_chyb = array();
    include_once('inc/mysql_connect.php');
    include_once('inc/config.php');
    include_once('tridy/individualni_produkcni_funkce.php');
    $hrac = vytvor_objekt_reprezentujici_hrace();
    vloz_horizontalni_pruh($hrac);

    echo "<div id=\"main\">";
    if ( $_SESSION['auth'] != 1) {
        
        include 'formulare/prihlasovaci_formular.php';
    } else {
        if (isset ($_POST['vyrabene_zbozi'])) {
            zpracuj_vysledek_formulare();
        }

        if (sizeof($pole_chyb) == 0) {
            zkontroluj_existenci_prikazu_produkce();
        }
        
        vloz_formular_pro_zadani_prikazu_produkce();
        vytvor_a_vloz_rozhodovaci_graf();

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

    function vloz_formular_pro_zadani_prikazu_produkce() {
        include_once ('formulare/formular_produkce.php');

        $formular_produkce = new formular_produkce();
        $formular_produkce->generuj_formular_produkce($GLOBALS['default_data'], $GLOBALS['pole_chyb']);
    }

    function zkontroluj_existenci_prikazu_produkce() {
        $dotaz = "SELECT * FROM prikazy_produkce WHERE login = '" . $_SESSION['login'] . "' AND kolo = " . $GLOBALS['kolo'];
        $vysledek = mysql_query($dotaz);
        if (mysql_num_rows($vysledek) == 1) {
            $radek = mysql_fetch_array($vysledek);
            extract($radek);
            $default_data = array();
            $default_data['hodin_prace'] = $hodin_prace;
            $default_data['druh_zbozi'] = $druh_zbozi;
            $GLOBALS['default_data'] = $default_data;
        }
    }

    //nejprve pracujeme jenom se změnou množství práce
    function vytvor_a_vloz_rozhodovaci_graf () {
        include_once 'formulare/formular_grafu_produkce.php';

        $hrac = $GLOBALS['hrac'];
        $dotaz_na_mnozstvi_kapitaloveho_zbozi = "SELECT mnozstvi_kapitaloveho_zbozi FROM kapitalove_zbozi_ve_vyrobe " .
            "WHERE login='" . $hrac->getLogin()  . "';";

        $mnozstvi_kapitaloveho_zbozi = 0;

        $vysledek_mnozstvi_kapitaloveho_zbozi = mysql_query($dotaz_na_mnozstvi_kapitaloveho_zbozi);
        if (mysql_num_rows($vysledek_mnozstvi_kapitaloveho_zbozi) > 0) {
            $radek_mnozstvi_kapitaloveho_zbozi = mysql_fetch_array($vysledek_mnozstvi_kapitaloveho_zbozi);
            extract($radek_mnozstvi_kapitaloveho_zbozi);
        }
        
        echo "V současné době je do výroby zapojeno " . $mnozstvi_kapitaloveho_zbozi . " kapitálových statků<br />";

        $default_data_pro_formular_grafu = array('variabilni_vyrobni_faktor' => 'prace',
            'mnozstvi_fixniho_faktoru' => $mnozstvi_kapitaloveho_zbozi, 'osa_x_max' => 100);

        if (isset ($_GET['mnozstvi_fixniho_faktoru'])) {
            $default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru'] = $_GET['mnozstvi_fixniho_faktoru'];
        }

        if (isset ($_GET['graf_variabilni_vyrobni_faktor'])) {
            $default_data_pro_formular_grafu['variabilni_vyrobni_faktor'] = $_GET['graf_variabilni_vyrobni_faktor'];
        }

        if (isset ($_GET['osa_x_max'])) {
            $default_data_pro_formular_grafu['osa_x_max'] = $_GET['osa_x_max'];
        }

        $formular_grafu_produkce = new formular_grafu_produkce();
        $formular_grafu_produkce->generuj_formular_grafu_produkce($default_data_pro_formular_grafu);

        $prace = array();
        $produkce = array();


        echo "<p style=\"text-align:center\">";
            echo "<img src=\"grafy/individualni_produkcni_funkce.php?variabilni_vyrobni_faktor=". $default_data_pro_formular_grafu['variabilni_vyrobni_faktor']  .
                "&mnozstvi_fixniho_faktoru= ". $default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru']  .
                "&osa_x_max=" . $default_data_pro_formular_grafu['osa_x_max']  .  "\" />";
        echo "</p>";

    }



    //chyba E - překročení limitu 24 hodin, příkaz není uložen (E - error)
    //chyba S - nesoulad mezi nejvyšší nabízenou dobou práce a práci pro vlastní potřebu (S - chyba sumace)
    function zpracuj_vysledek_formulare() {
        $default_data = array();

        if (zkontroluj_zadane_hodiny_prace() == false) {
            return;
        }

        $prikaz_produkce = new prikaz_produkce();
        $prikaz_produkce->setHodin_prace($_POST['hodin_prace']);
        $druh_zbozi = 0;
        if ($_POST['vyrabene_zbozi'] == 'kapitalove_zbozi') {
            $druh_zbozi = 2;
        } else {
            $druh_zbozi = 1;
        }
        $prikaz_produkce->setDruh_zbozi($druh_zbozi);


        $dotaz = "SELECT * FROM prikazy_produkce WHERE login = '" . $_SESSION['login'] . "' AND kolo = ". $GLOBALS['kolo'];
        $vysledek = mysql_query($dotaz);

        $dotaz = "";
        if (mysql_num_rows($vysledek) == 0) {
            $dotaz = $prikaz_produkce->vytvor_SQL_definici($_SESSION['login'], $GLOBALS['kolo'], true);
        } else {
            $dotaz = $prikaz_produkce->vytvor_SQL_definici($_SESSION['login'], $GLOBALS['kolo'], false);
        }

        mysql_query($dotaz);
    }

    function zkontroluj_zadane_hodiny_prace() {
        $hodin_prace = $_POST['hodin_prace'];
        $pole_chyb = array();
        $default_data = array();

        if ($hodin_prace > 24) {
            $pole_chyb[1] = "E";
            nastav_default_data_pri_chybe();
            $GLOBALS['pole_chyb'] = $pole_chyb;
            return false;
        }

        $dotaz = "SELECT mnozstvi4, mnozstvi3, mnozstvi2, mnozstvi1 FROM trh_prace_nabidka " .
            " WHERE login='" . $_SESSION['login'] . "' AND kolo = " . $GLOBALS['kolo'] ."; ";
        $vysledek = mysql_query($dotaz);
        if (mysql_num_rows($vysledek) > 0) {
            $radek = mysql_fetch_array($vysledek);
            extract($radek);

            if (($hodin_prace + $mnozstvi4) > 24) {
                $pole_chyb[1] = "S";
                $GLOBALS['pole_chyb'] = $pole_chyb;
                nastav_default_data_pri_chybe();
                return false;
            }
            if (($hodin_prace + $mnozstvi3) > 24) {
                $pole_chyb[1] = "S";
                $GLOBALS['pole_chyb'] = $pole_chyb;
                nastav_default_data_pri_chybe();
                return false;
            }
            if (($hodin_prace + $mnozstvi2) > 24) {
                $pole_chyb[1] = "S";
                $GLOBALS['pole_chyb'] = $pole_chyb;
                nastav_default_data_pri_chybe();
                return false;
            }
            if (($hodin_prace + $mnozstvi1) > 24) {
                $pole_chyb[1] = "S";
                $GLOBALS['pole_chyb'] = $pole_chyb;
                nastav_default_data_pri_chybe();
                return false;
            }
        }

        return true;
    }

    function nastav_default_data_pri_chybe() {
        $hodin_prace = $_POST['hodin_prace'];
        $pole_chyb = array();
        $default_data = array();

        $default_data['hodin_prace'] = $hodin_prace;

        $druh_zbozi = 0;
        if ($_POST['vyrabene_zbozi'] == 'kapitalove_zbozi') {
            $druh_zbozi = 2;
        } else {
            $druh_zbozi = 1;
        }
        $default_data['druh_zbozi'] = $druh_zbozi;

        $GLOBALS['default_data'] = $default_data;
    }