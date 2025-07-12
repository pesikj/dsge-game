<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// $spojeni = mysql_connect("localhost", "root","" );
// mysql_select_db("simulacnihra");

class mysql_connection {
    private $pole_udaju_pro_pristup;

    function __construct() {
        if (file_exists('inc/config.php')) {
            require_once 'inc/config.php';
        } else if (file_exists('../inc/config.php')) {
            require_once '../inc/config.php';
        }
        
        $spravce_konfigurace = new spravce_konfigurace();
        $pole_udaju_pro_pristup = $spravce_konfigurace->get_pristup_k_databazi();

        if ($pole_udaju_pro_pristup['uzamceni_databaze'] != 0) {
            return false;
        } else {
            $this->pole_udaju_pro_pristup = $pole_udaju_pro_pristup;
            return true;
        }
    }
    public function otevri_pripojeni() {
        if ($this->pole_udaju_pro_pristup['uzamceni_databaze'] != 0) {
            return false;
        }
        $spojeni = mysql_connect($this->pole_udaju_pro_pristup['adresa_databazoveho_serveru'],
            $this->pole_udaju_pro_pristup['login'], $this->pole_udaju_pro_pristup['passwd']);
        mysql_select_db($this->pole_udaju_pro_pristup['jmeno_databaze']);
        return $spojeni;
    }
}
?>
