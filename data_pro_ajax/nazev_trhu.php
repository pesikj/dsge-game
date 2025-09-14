<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    $pole_nazvu = array('trh_spotrebniho_zbozi' => 'Trh spotřebního zboží',
                    'trh_kapitaloveho_zbozi' => 'Trh kapitálového zboží',
                    'trh_prace' => 'Trh práce',
                    'trh_uspor' => 'Trh úspor',
                    'trh_uveru' => 'Trh úvěrů',
                    'trh_uveru_a_uspor' => 'Trh úvěrů a úspor',
                    'trh_obligaci' => 'Trh obligací',
                    'devizovy_trh' => 'Devizový trh');

    $id_meny = $_GET['id_meny'];
    $id_druhu_trhu = $_GET['id_druhu_trhu'];

    echo $pole_nazvu[$id_druhu_trhu] . " " . $id_meny;
?>
