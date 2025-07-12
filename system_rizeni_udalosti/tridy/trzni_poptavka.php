<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Reprezentuje tržní poptávku jako agregaci všech individuálních poptávek.
 *
 * @author pike
 */
class trzni_poptavka {
    //put your code here

        function __construct($kolo) {
            $this->kolo = $kolo;
        }


    private $pole_individualnich_poptavek;
    private $pole_trzni_poptavky;
    private $kolo;

    public function getPole_trzni_poptavky() {
        return $this->pole_trzni_poptavky;
    }

    public function getPole_individualnich_poptavek() {
        return $this->pole_individualnich_poptavek;
    }

    

    /*
     * Načte z databáze data týkající se všech tržních poptávek.
     */
    public function nacti_data_poptavek_z_databaze($nazev_tabulky) {
        require_once 'tridy/individualni_poptavka.php';
        require_once('inc/mysql_connect.php');
        $mysql_connection = new mysql_connection();
        $spojeni = $mysql_connection->otevri_pripojeni();
        
        $dotaz = "SELECT * FROM " . $nazev_tabulky . " WHERE kolo = " . $this->kolo . "; ";
        $vysledek = mysql_query($dotaz);
        $pole_individualnich_poptavek = array();

        //vytvoříme jednotlivé individuální poptávky a umístíme je do pole
        while ($radek = mysql_fetch_array($vysledek)) {
            $individualni_poptavka = new individualni_poptavka($radek['login'], $this->kolo);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena1'], $radek['mnozstvi1']);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena2'], $radek['mnozstvi2']);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena3'], $radek['mnozstvi3']);
            $individualni_poptavka->pridej_parametr_poptavky($radek['cena4'], $radek['mnozstvi4']);
            $individualni_poptavka->vypoctiPoptavku();
            $pole_individualnich_poptavek[] = $individualni_poptavka;
        }
        $this->pole_individualnich_poptavek = $pole_individualnich_poptavek;
    }

    /*
     * Na základě dat o individuálních poptávkách vypočte tržní poptávku.
     */
    public function vytvor_trzni_poptavku() {
        $pole = $this->pole_individualnich_poptavek;

        //pole tržní poptávek, je zpracováno na základě dat z databáze
        $pole_trzni_poptavky = array();
        $pole_trzni_poptavky[0] = 0;

        /*
         * Algoritmus je zde jednodušší, jde o pouhý součet polí poptávek,
         * nemusíme zde řešit překročení velikosti pole (zde je poptávané
         * množství nulové)
         */
        foreach ($pole as $index => $individualni_poptavka) {
            $pole_individualni_poptavky = $individualni_poptavka->getPole_individualni_poptavky();
            for ($i = 1; $i < count($pole_individualni_poptavky); $i++) {
                $pole_trzni_poptavky[$i] += $pole_individualni_poptavky[$i];
            }
        }

        $this->pole_trzni_poptavky = $pole_trzni_poptavky;
    }
}
?>
