<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    require_once '../inc/mysql_connect.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();

    $id_ekonomiky = $_GET['id_ekonomiky'];

    $dotaz_na_clanek = "SELECT * FROM clanky WHERE id_ekonomiky = '" . $id_ekonomiky . "'";
    $vysledek_clanek = mysql_query($dotaz_na_clanek) or die($dotaz_na_clanek);

    if (mysql_num_rows($vysledek_clanek) == 1) {
        $radek_clanek = mysql_fetch_assoc($vysledek_clanek);
        echo $radek_clanek['clanek'];
    }

?>
