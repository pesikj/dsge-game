<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once '../inc/mysql_connect.php';
    require_once '../tridy/komponenty_danove_soustavy/Daneny_tok.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();
    if ($_GET['akce'] == 'pridani') {
        $daneny_tok = new Daneny_tok($_GET['ppdt'], $_GET['idt'], $_GET['koef']);
        $daneny_tok->zapis_daneny_tok_pro_dany_druh_dane($_GET['druh_dane']);
    }

    $dotaz_na_toky_v_databazi = "SELECT * FROM danene_toky WHERE id_druhu_dane = '" . $_GET['druh_dane'] . "';";
    $vysledek = mysql_query($dotaz_na_toky_v_databazi) or die($dotaz_na_toky_v_databazi);
    echo "<table>";
    while ($radek = mysql_fetch_assoc($vysledek)) {
        echo "<td>";
            echo "<td>" . $radek['popis_puvodu_daneneho_toku'] . "</td>";
            echo "<td>" . $radek['id_druhu_trhu'] . "</td>";
            echo "<td>" . $radek['koeficient_toku'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
