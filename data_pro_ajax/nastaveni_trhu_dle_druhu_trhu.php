<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    //require_once '../../inc/mysql_connect.php';
    //$mysql_connection = new mysql_connection();
    //$mysql_connection->otevri_pripojeni();

    require_once '../inc/mysql_connect.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();

    $id_druhu_trhu = $_GET['id_druhu_trhu'];

    $dotaz_na_polozky_nastaveni_u_trhu = "SELECT * FROM polozky_nastaveni_u_trhu WHERE id_druhu_trhu = '" . $id_druhu_trhu . "'; ";
    $vysledek_polozky_nastaveni_u_trhu = mysql_query($dotaz_na_polozky_nastaveni_u_trhu) or die($dotaz_na_polozky_nastaveni_u_trhu);
    echo "<table>";

    while ($radek_polozka_nastaveni = mysql_fetch_assoc($vysledek_polozky_nastaveni_u_trhu)) {
        echo "<tr><td>" . $radek_polozka_nastaveni['id_polozky_nastaveni'] . "</td>";
        if ($radek_polozka_nastaveni['specifikace_polozky'] == null) {
            if ($radek_polozka_nastaveni['datovy_typ_polozky'] == 'int') {
                echo "<td><input type=\"text\" name=\"" . $radek_polozka_nastaveni['id_polozky_nastaveni'] . "\" /></td>";
            } else if ($radek_polozka_nastaveni['datovy_typ_polozky'] == 'string') {
                echo "<td><input type=\"text\" name=\"" . $radek_polozka_nastaveni['id_polozky_nastaveni'] . "\" /></td>";
            }
        } else if ($radek_polozka_nastaveni['specifikace_polozky'] == 'id_meny') {
            $dotaz_na_meny = "SELECT * FROM meny; ";
            $vysledek_meny = mysql_query($dotaz_na_meny) or die($dotaz_na_meny);
            echo "<td><select name=\"" . $radek_polozka_nastaveni['id_polozky_nastaveni'] . "\">";
            while ($radek_meny = mysql_fetch_assoc($vysledek_meny)) {
                echo "<option value=\"" . $radek_meny['id_meny'] . "\">" . $radek_meny['nazev_meny'] . "</option>";
            }
            echo "</select>";
        }
        echo "</tr>";
    }

    echo "</table>";
?>
