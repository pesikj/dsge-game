<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Tato třída definuje tržní nabídku. Umí na základě definovaných individálních
 * nabídek vytvořit tržní nabídku, která je agregací individuálních nabídek.
 * Individuální nabídky si umí načíst z databáze.
 *
 * @author pike
 */
class trzni_nabidka {
    //put your code here

    function __construct($kolo, $nazev_tabulky) {
        $this->kolo = $kolo;
        $this->nazev_tabulky = $nazev_tabulky;
    }

    private $pole_individualnich_nabidek;
    private $pole_trzni_nabidky;
    private $kolo;
    private $nazev_tabulky;

    public function getPole_individualnich_nabidek() {
        return $this->pole_individualnich_nabidek;
    }

    
    public function getPole_trzni_nabidky() {
        return $this->pole_trzni_nabidky;
    }

    /*
     * Načte data o individuálních nabídkách z databáze a ukládá je do pole,
     * aby bylo umožněno jejich pozdější zpracování.
     */
    public function nacti_data_nabidek_z_databaze() {
        $nazev_tabulky = $this->nazev_tabulky;
        require_once 'tridy/individualni_nabidka.php';
        require_once('inc/mysql_connect.php'); //připojení k databázi
        $mysql_connection = new mysql_connection();
        $spojeni = $mysql_connection->otevri_pripojeni();

        $dotaz = "SELECT * FROM " . $nazev_tabulky . " WHERE kolo = " . $this->kolo . "; ";
        $vysledek = mysql_query($dotaz);
        $pole_individualnich_nabidek = array();

        while ($radek = mysql_fetch_array($vysledek)) {
            $individualni_nabidka = new individualni_nabidka($radek['login'], $this->kolo);
            $individualni_nabidka->pridej_parametr_nabidky($radek['cena1'], $radek['mnozstvi1']);
            $individualni_nabidka->pridej_parametr_nabidky($radek['cena2'], $radek['mnozstvi2']);
            $individualni_nabidka->pridej_parametr_nabidky($radek['cena3'], $radek['mnozstvi3']);
            $individualni_nabidka->pridej_parametr_nabidky($radek['cena4'], $radek['mnozstvi4']);
            $individualni_nabidka->vypocti_nabidku();
            $pole_individualnich_nabidek[] = $individualni_nabidka;
        }
        $this->pole_individualnich_nabidek = $pole_individualnich_nabidek; //toto pole později zpracujeme
    }


    /*
     * Vypočítá tržní poptávku na základě tabulek individuálních poptávek. Jedná
     * se o pouhý součet nabídek, postup je tedy v principu podobný grafickému řešení
     * podobné téže úlohy.
     */
    public function vytvor_trzni_nabidku() {
        $pole = $this->pole_individualnich_nabidek;
        $pole_trzni_nabidky[0] = 0;

        $nejvyssi_mezni_cena = 0; //ta nám určí velikost pole
        foreach ($pole as $individualni_nabidka) {
            $pole_meznich_cen = $individualni_nabidka->getPole_meznich_cen();
            end($pole_meznich_cen);
            if (key($pole_meznich_cen) > $nejvyssi_mezni_cena) {
                $nejvyssi_mezni_cena = key($pole_meznich_cen); //velikost pole je daná nejvyšší mezní cenou
            }
        }

        //echo "<br>nejvyšší mezní cena - tržní nabídka: " . $nejvyssi_mezni_cena;

        foreach ($pole as $index => $individualni_nabidka) {
            $pole_individualni_nabidky = $individualni_nabidka->getPole_individualni_nabidky();

            $pole_meznich_cen = $individualni_nabidka->getPole_meznich_cen();
            end($pole_meznich_cen); //z pole mezních cen určíme nejvyšší mezní cenu dané nabídky

            for ($i = 1; $i <= $nejvyssi_mezni_cena; $i++) {
                if ($i < key($pole_meznich_cen)) { //pole individuální nabídky pokračuje jen
                    $pole_trzni_nabidky[$i] += $pole_individualni_nabidky[$i]; //sečteme pole
                } else {
                    //pokud se dostaneme za hranici pole individuální nabídky
                    //dosazujeme první hodnotu pole
                    //tady pozor: algoritmus uspořádá pole nabídky obráceně než
                    //pole poptávky (oba analyzují data opačným směrem), proto zde není příkaz end!
                    $pole_trzni_nabidky[$i] += current($pole_individualni_nabidky);
                }
                
            }
        }

        //print_r($pole_trzni_nabidky);
        $this->pole_trzni_nabidky = $pole_trzni_nabidky;
    }
}
?>
