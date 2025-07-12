<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Tato třída reprezentuje individuální nabídku. J
 *
 * @author pike
 */
class individualni_nabidka {
    //put your code here

    private $pole_meznich_cen = array();
    private $pole_individualni_nabidky;
    private $login;
    private $kolo;

    function __construct($login, $kolo) {
        $this->login = $login;
        $this->kolo = $kolo;
    }

    public function getLogin() {
        return $this->login;
    }

    public function pridej_parametr_nabidky($mezni_cena, $mnozstvi) {
        $pole = $this->pole_meznich_cen;
        $pole[$mezni_cena] = $mnozstvi;
        $this->pole_meznich_cen = $pole;
        ksort($this->pole_meznich_cen);
    }


    public function getPole_individualni_nabidky() {
        return $this->pole_individualni_nabidky;
    }
    public function getPole_meznich_cen() {
        return $this->pole_meznich_cen;
    }


    /*
     * Výpočet nabídky funguje obráceně než výpočet nabídky. Zatímco poptávka
     * měla horní limit, od kterého poté byla nulová, nabídka má dolní limit.
     * Jinak řečeno, zatímco spotřebitel se rozhodne, že za velmi vysokou cenu
     * zboží už nekoupí, obchodník se rozhodne, že za velmi nízkou cenu zboží
     * už neprodá.
     */
    public function vypocti_nabidku() {
        $pole_meznich_cen = $this->pole_meznich_cen;
        reset($pole_meznich_cen);
        $konec_cyklu = key($pole_meznich_cen);
        end($pole_meznich_cen);
        $zacatek_cyklu = key($pole_meznich_cen);

        $pole_individualni_nabidky = array();


        //postupujeme směrem dolů
        for ($i = $zacatek_cyklu + 1; $i >= $konec_cyklu; $i--) {
            $pole_individualni_nabidky[$i] = current($pole_meznich_cen);

            if ($i == key($pole_meznich_cen)) {
                prev($pole_meznich_cen);
            }

        }
        //print_r($pole_individualni_nabidky);
        $this->pole_individualni_nabidky = $pole_individualni_nabidky;
    }

    public function urci_nabizene_mnozstvi_pri_dane_cene ($cena) {
        $pole_individualni_nabidky = $this->pole_individualni_nabidky;

        reset($pole_individualni_nabidky);

        if ($cena >= key($pole_individualni_nabidky)) {
            $mnozstvi = current($pole_individualni_nabidky);
        } else {
            if (isset ($pole_individualni_nabidky[$cena])) {
                $mnozstvi = $pole_individualni_nabidky[$cena];
            } else {
                $mnozstvi = 0;
            }

        }

        return $mnozstvi;
    }

    /*
     * Musíme poptávku pokrátit, pokud dochází k převisu poptávky nad nabídkou.
     */
    public function urci_pokracene_nabizene_mnozstvi_na_trhu ($cena, $prepocitavaci_koeficient) {
        $nabizene_mnozstvi_pri_dane_cene = $this->urci_nabizene_mnozstvi_pri_dane_cene($cena);
        $pokracene_nabizene_mnozstvi = floor($nabizene_mnozstvi_pri_dane_cene * $prepocitavaci_koeficient);
        return $pokracene_nabizene_mnozstvi;
    }

    public function nacti_data_nabidky_z_databaze ($nazev_tabulky) {
        $dotaz = "SELECT * FROM " . $nazev_tabulky . " WHERE kolo = " . $this->kolo . " AND login =  '" .
            $this->login . "' ; ";
        $vysledek = mysql_query($dotaz) or die ($dotaz);

        $radek = mysql_fetch_array($vysledek);
        $this->pridej_parametr_nabidky($radek['cena1'], $radek['mnozstvi1']);
        $this->pridej_parametr_nabidky($radek['cena2'], $radek['mnozstvi2']);
        $this->pridej_parametr_nabidky($radek['cena3'], $radek['mnozstvi3']);
        $this->pridej_parametr_nabidky($radek['cena4'], $radek['mnozstvi4']);
        $this->vypocti_nabidku();
    }


    /*
     * SQL definici poptávky - umí vygenerovat příkazy pro vložení i pro
     * aktualizaci záznamu v databázi.
     */
    public function vytvor_SQL_definici($nazev_tabulky, $hrac, $vkladani) {
        if ($vkladani == true) {
            reset($this->pole_meznich_cen);
            $definice = "INSERT INTO " . $nazev_tabulky . " VALUES (";
            $definice .= "'" . $hrac . "', ";
            $definice .= $this->kolo . ",";

            
            $definice .= key($this->pole_meznich_cen) . ", ";
            $definice .= current($this->pole_meznich_cen) . ", ";
            next($this->pole_meznich_cen);

            $definice .= key($this->pole_meznich_cen) . ", ";
            $definice .= current($this->pole_meznich_cen) . ", ";
            next($this->pole_meznich_cen);

            $definice .= key($this->pole_meznich_cen) . ", ";
            $definice .= current($this->pole_meznich_cen) . ", ";
            next($this->pole_meznich_cen);

            $definice .= key($this->pole_meznich_cen) . ", ";
            $definice .= current($this->pole_meznich_cen) . " ";
            next($this->pole_meznich_cen);

            $definice .= ");";
            return $definice;
        } else {
            reset($this->pole_meznich_cen);
            $definice = "UPDATE " . $nazev_tabulky . " SET ";

            $definice .= " cena1 = " . key($this->pole_meznich_cen) . ", ";
            $definice .= " mnozstvi1 = " . current($this->pole_meznich_cen) . ", ";
            next($this->pole_meznich_cen);

            $definice .= " cena2 = " . key($this->pole_meznich_cen) . ", ";
            $definice .= " mnozstvi2 = " . current($this->pole_meznich_cen) . ", ";
            next($this->pole_meznich_cen);

            $definice .= " cena3 = " . key($this->pole_meznich_cen) . ", ";
            $definice .= " mnozstvi3 = " . current($this->pole_meznich_cen) . ", ";
            next($this->pole_meznich_cen);

            $definice .= " cena4 = " . key($this->pole_meznich_cen) . ", ";
            $definice .= " mnozstvi4 = " . current($this->pole_meznich_cen) . " ";
            next($this->pole_meznich_cen);


            $definice .= "WHERE login='" . $hrac . "' AND ";
            $definice .= "kolo= " . $this->kolo;

            $definice .= ";";
            return $definice;
        }
    }
}
?>
