<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of kolo
 *
 * @author pike
 */
class kolo {
    //put your code here

    private $kolo;
    private $aktivnich_hracu;
    private $registrovanych_hracu;

    function __construct($kolo) {
        require_once 'trh.php';
        require_once 'inc/mysql_connect.php';
        require_once 'spravce_uveru.php';
        require_once 'inc/config.php';
        require_once 'spravce_produkce.php';
        require_once 'konec_kola.php';

        $mysql_connection = new mysql_connection();
        $spojeni = $mysql_connection->otevri_pripojeni();

        $this->kolo = $kolo;
    }

//    public function zaznamenej_zacatek_kola() {
//        $dotaz_na_zaznam_zacatku_kola = "INSERT INTO statistika_kola VALUES (" .
//            $this->kolo . ", CURRENT_TIMESTAMP, 0000-00-00 00:00:00) ;";
//        mysql_query($dotaz_na_zaznam_zacatku_kola);
//    }

    public function get_cislo_kola() {
        return $this->kolo;
    }

    public function getAktivnich_hracu() {
        $this->zjisti_konkretni_aktualni_informaci('aktivita_hracu');
        return $this->aktivnich_hracu;
    }

    public function getRegistrovanych_hracu() {
        $this->zjisti_konkretni_aktualni_informaci('aktivita_hracu');
        return $this->registrovanych_hracu;
    }
}
?>
