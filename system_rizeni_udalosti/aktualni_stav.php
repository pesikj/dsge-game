<?php
    session_start();
    require_once 'tridy/prikaz_produkce.php';


    include 'html/html_struktura.php';
    vloz_xhtml_hlavicku();

?>
<body>
<div id="wrap">
    <div id="header"><h1>Simulační hra</h1></div>

<?php
    $default_data = array('hodin_prace' => 0, 'druh_zbozi' => 1);
    $pole_chyb = array();
    include_once('inc/mysql_connect.php');
    include_once('inc/config.php');

    echo "<div id=\"main\">";
    if ( $_SESSION['auth'] != 1) {
        include 'formulare/prihlasovaci_formular.php';
    } else {
        require_once('tridy/kolo.php');
        require_once('formulare/formular_trideni_aktualnich_informaci.php');

        $default_data = array ('zobrazovana_informace' => 'trh_spotrebniho_zbozi', 'vybrane_kolo' => $kolo);

        if (isset ($_GET['zobrazovana_informace'])) {
            $default_data['zobrazovana_informace'] = $_GET['zobrazovana_informace'];
        }

        if (isset ($_GET['vybrane_kolo'])) {
            $default_data['vybrane_kolo'] = $_GET['vybrane_kolo'];
        }

        $formular_trideni_aktualnich_informaci = new formular_trideni_aktualnich_informaci();
        $formular_trideni_aktualnich_informaci->generuj_formular_aktualnich_informaci($default_data);


        $zobrazovana_informace = $default_data['zobrazovana_informace'];
        $vybrane_kolo = $default_data['vybrane_kolo'];

        $kolo = new kolo($vybrane_kolo);
        $kolo->zjisti_konkretni_aktualni_informaci($zobrazovana_informace);
        
        
        echo "</div>";

        vloz_menu($_SESSION['login']);

        echo "<div id=\"footer\">";
	echo "<p>Jiří Pešík</p>";
	echo "</div>";
        echo "</div>";
        echo "</body>";

        include 'html/foot.php';
    }