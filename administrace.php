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
        require_once('tridy/konec_kola.php');
        require_once('tridy/kolo.php');


        vloz_formular();

        if (isset ($_POST['nove_kolo'])) {
            $kolo_objekt = new kolo($kolo);
            $kolo_objekt->uzavri_kolo();
        }

        echo "</div>";

        vloz_menu($_SESSION['login']);



        echo "<div id=\"footer\">";
	echo "<p>Jiří Pešík</p>";
	echo "</div>";
        echo "</div>";
        echo "</body>";

        include 'html/foot.php';

    }


        function vloz_formular() {
            $pageURL = 'http';
            $pageURL .= "://";
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            echo "<form action=\"" . $pageURL . "\" method=\"POST\">";

                echo "<input type=\"submit\" name=\"nove_kolo\" value=\"Ukonči současné a spusť nové kolo\" />";
            echo "</form>";
        }