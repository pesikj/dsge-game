<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of individualniPoptavka
 *
 * @author Jiří Pešík
 */
class individualni_poptavka {

    //put your code here

    private $pole_meznich_cen = array();
    private $pole_individualni_poptavky;
    private $login;
    private $kolo;

    function __construct($login, $kolo) {
        $this->login = $login;
        $this->kolo = $kolo;
    }

    public function getLogin() {
        return $this->login;
    }

    

    /*
     * Rozšíří pole mezních cen o další parametr. Klíčem v tomto poli je
     * mezní cena, od které dochází ke změně poptávaného množství, a
     * nové poptávané množství.
     */
    public function pridej_parametr_poptavky($mezni_cena, $mnozstvi) {
        $pole = $this->pole_meznich_cen;
        $pole[$mezni_cena] = $mnozstvi;
        $this->pole_meznich_cen = $pole;
        ksort($this->pole_meznich_cen);
    }

    
    public function getPole_individualni_poptavky() {
        return $this->pole_individualni_poptavky;
    }

    public function getPole_meznich_cen() {
        return $this->pole_meznich_cen;
    }

    /*
     * Kontroluje, zda je poptávka klesající. Racionálně jednající hráč nebo
     * výrobce má vždy klesající křivku poptávky.
     */
    public function je_klesajici() {
        $klesajici = true;
        $predchozi_mnozstvi = 9000;
        foreach ($this->pole_meznich_cen as $mnozstvi) {
            if ($predchozi_mnozstvi <= $mnozstvi) {
                $klesajici = false;
            }
            $predchozi_mnozstvi = $mnozstvi;
        }
        return $klesajici;
    }



    /*
     * Z tabulky mezních cen vytvoří pole, jehož klíčem jsou tržní ceny, položkou
     * poptávané množství a velikost je definována nejvyšší mezní cenou, od které
     * již je dále poptáváno nulové množství.
     */
    public function vypoctiPoptavku() {
        //první číslo horní limit ceny, druhé poptávané množstí
        $pole_meznich_cen = $this->pole_meznich_cen;
        //zjistíme horní limit ceny, od kterého již nebudeme nic poptávat
        //využijeme funkci end, která posune "iterátor" na konec pole
        //efektivnější než cyklus
        end($pole_meznich_cen);
        $konec_cyklu = key($pole_meznich_cen);
        /* Vytvoří se nám "křivka" individuální poptávky, která je složena
         * z diskrétních bodů. To umožní snadné vytvoření tržní poptávky.
         * Cena bude vždy celé číslo - to odpovídá i výchozím předpokladům hry.
         * Uživatelé mají možnost zadávat ceny pouze jako celá čísla.
         * Po poli s mezními cenami se posunujeme pomocí iterátoru.
         */
        $pole_individualni_poptavky = array();
        reset($pole_meznich_cen);
        for ($i = 1; $i <= $konec_cyklu; $i++) {
            if ($i == key($pole_meznich_cen)) {
                next($pole_meznich_cen);
            }
            $pole_individualni_poptavky[$i] = current($pole_meznich_cen);
        }
        $this->pole_individualni_poptavky = $pole_individualni_poptavky;
    }

    public function urci_poptavane_mnozstvi_pri_dane_cene($cena) {
        $pole_individualni_poptavky = $this->pole_individualni_poptavky;
        $mnozstvi = 0;

        if ($cena < sizeof($pole_individualni_poptavky)) {
            $mnozstvi = $pole_individualni_poptavky[$cena];
        }

        return $mnozstvi;
    }

    /*
     * Musíme poptávku pokrátit, pokud dochází k převisu poptávky nad nabídkou.
     */
    public function urci_pokracene_poptavane_mnozstvi_na_trhu ($cena, $prepocitavaci_koeficient) {
        $poptavane_mnozstvi_pri_dane_cene = $this->urci_poptavane_mnozstvi_pri_dane_cene($cena);
        $pokracene_poptavane_mnozstvi = floor($poptavane_mnozstvi_pri_dane_cene * $prepocitavaci_koeficient);
        return $pokracene_poptavane_mnozstvi;
    }

    public function nacti_data_poptavky_z_databaze($nazev_tabulky) {
        $dotaz = "SELECT * FROM " . $nazev_tabulky . " WHERE kolo = " . $this->kolo . " AND login =  '" .
            $this->login . "' ; ";
        $vysledek = mysql_query($dotaz) or die ($dotaz);

        $radek = mysql_fetch_array($vysledek);
        $this->pridej_parametr_poptavky($radek['cena1'], $radek['mnozstvi1']);
        $this->pridej_parametr_poptavky($radek['cena2'], $radek['mnozstvi2']);
        $this->pridej_parametr_poptavky($radek['cena3'], $radek['mnozstvi3']);
        $this->pridej_parametr_poptavky($radek['cena4'], $radek['mnozstvi4']);
        $this->vypoctiPoptavku();
    }


    /*
     * Vytvoří SQL definici dotazu v závislosti na trhu, na tom, zda půjde o vložení
     * nové položky nebo změnu existující, na aktuálním kole hru a na loginu hráče.
     */
    public function vytvor_SQL_definici($nazev_tabulky, $hrac, $vkladani) {
        if ($vkladani == true) {
            reset($this->pole_meznich_cen);
            $definice = "INSERT INTO " . $nazev_tabulky ." VALUES ( "; //tady upravit!
            $definice .= "'" . $this->login . "', ";
            $definice .= $this->kolo . " ,";

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


            $definice .= "WHERE login='" . $this->login . "' AND ";
            $definice .= "kolo = " . $this->kolo;

            $definice .= ";";
            return $definice;
        }
    }
}
?>
