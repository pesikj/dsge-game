<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once '../inc/mysql_connect.php';
    require_once '../tridy/Informace_o_ekonomikach.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();

    $id_integracniho_celku = $_GET['id_integracniho_celku'];
    $id_druhu_trhu = $_GET['id_druhu_trhu'];

    $poptavky = array ('trh_spotrebniho_zbozi' => array('spotrebitel' => 1, 'podnikatel' => 1, 'vlada' => 1),
                        'trh_kapitaloveho_zbozi' => array('vyrobce' => 1, 'podnikatel' => 1),
                        'trh_prace' => array('vyrobce' => 1, 'podnikatel' => 1),
                        'trh_uveru_a_uspor' => array('podnikatel' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'vlada' => 1, 'centralni_banka' => 1),
                        'trh_uveru' => array('podnikatel' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'vlada' => 1),
                        'trh_uspor' => array('centralni_banka' => 1),
                        'trh_obligaci' => array('centralni_banka' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'podnikatel' => 1, 'vlada' => 1),
                        'devizovy_trh' => array('centralni_banka' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'podnikatel' => 1, 'vlada' => 1));
    $nabidky = array ('trh_spotrebniho_zbozi' => array('spotrebitel' => 0, 'podnikatel' => 1, 'vyrobce' => 1),
                        'trh_kapitaloveho_zbozi' => array('vyrobce' => 1, 'podnikatel' => 1),
                        'trh_prace' => array('vyrobce' => 0, 'podnikatel' => 1, 'spotrebitel' => 1),
                        'trh_uveru_a_uspor' => array('podnikatel' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'vlada' => 1, 'centralni_banka' => 1),
                        'trh_uspor' => array('podnikatel' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'vlada' => 1),
                        'trh_uveru' => array('centralni_banka' => 1),
                        'trh_obligaci' => array('centralni_banka' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'podnikatel' => 1, 'vlada' => 1),
                        'devizovy_trh' => array('centralni_banka' => 1, 'spotrebitel' => 1, 'vyrobce' => 1, 'podnikatel' => 1, 'vlada' => 1));

    $id_druhu_trhu = $_GET['id_druhu_trhu'];
    $informace_o_ekonomikach = Informace_o_ekonomikach::get_informace_o_ekonomikach();
    $tabulka = "<table border=\"0\" style=\"width: 100%\">";
    $tabulka .= "<tr>";
        $tabulka .= "<th>" . "id_druhu_subjektu" . "</th>";
        $tabulka .= "<th>" . "pravo_poptavat" . "</th>";
        $tabulka .= "<th>" . "pravo_nabizet" . "</th>";
    $tabulka .= "</tr>";
    foreach ($informace_o_ekonomikach->get_pole_druhu_subjektu() as $aktualni_druh_subjektu) {
        foreach ($informace_o_ekonomikach->get_pole_id_ekonomik() as $aktualni_id_ekonomiky => $aktualni_id_integracniho_celku) {
            if ($id_integracniho_celku != $aktualni_id_integracniho_celku) {
                continue;
            }
            $tabulka .= "<tr>";
            $value_checkboxu = $aktualni_druh_subjektu . ";" . $aktualni_id_ekonomiky;
            $tabulka .= "<td>" . $aktualni_druh_subjektu . " (" . $aktualni_id_ekonomiky . ")" . "</td>";
            $tabulka .= "<td style=\"text-align:center;\"><input type=\"checkbox\" value=\"" . $value_checkboxu . "\" name=\"prava_poptavat[]\"" ;
            if (is_numeric($poptavky[$id_druhu_trhu][$aktualni_druh_subjektu]) == true) {
                if ($poptavky[$id_druhu_trhu][$aktualni_druh_subjektu] == 1) {
                    $tabulka .= " checked=\"checked\" ";
                }
            }
            $tabulka .= " /></td>";
            $tabulka .= "<td style=\"text-align:center;\"><input type=\"checkbox\" value=\"" . $value_checkboxu . "\" name=\"prava_nabizet[]\"" ;
            if (is_numeric($nabidky[$id_druhu_trhu][$aktualni_druh_subjektu]) == true) {
                if ($nabidky[$id_druhu_trhu][$aktualni_druh_subjektu] == 1) {
                    $tabulka .= " checked=\"checked\" ";
                }
            }
            $tabulka .= " /></td>";
            $tabulka .= "</tr>";
        }
    }
    $tabulka .= "</table>";
    echo $tabulka;

?>
