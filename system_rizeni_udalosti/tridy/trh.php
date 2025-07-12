<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Tato třída definuje trh. Trh si umí sestavit svoje tržní a tím i individuální
 * nabídky a poptávky na základě dat v databázi a z nich určit tržní cenu. Klíčový
 * je atribut nazev_trhu, který jednotlivé trhy indetifikuje.
 *
 * @author pike
 */
class trh {
    //put your code here
    public $pole_individualnich_poptavek;
    public $pole_individualnich_nabidek;
    private $nazev_trhu; //klíčový atribut!!!
    private $trzni_poptavka;
    private $trzni_nabidka;
    private $trzni_cena;
    private $kolo;
    private $nabizene_mnozstvi_pri_trzni_cene;
    private $poptavane_mnozstvi_pri_trzni_cene;

    function __construct($nazev_trhu, $kolo) {

        require_once 'individualni_poptavka.php';
        require_once 'trzni_poptavka.php';
        require_once 'individualni_nabidka.php';
        require_once 'trzni_nabidka.php';
        $this->nazev_trhu = $nazev_trhu;
        $this->kolo = $kolo;

    }

    public function getNazev_trhu() {
        return $this->nazev_trhu;
    }

    public function getTrzni_cena() {
        return $this->trzni_cena;
    }


    /**
     * Metoda maximálního obratu.
     * Nejprve si vytvoříme tržní nabídku a poptávku. Tím získáme tabulku tržní
     * nabídky a poptávky. Poté hledáme minimální převis mezi nabízeným a
     * poptávaným množstvím. Uprostřed intervalu s tímto minimálním převisem
     * máme tržní cenu.
     */
    public function urci_trzni_cenu() {
        $trzni_poptavka = new trzni_poptavka($this->kolo);
        $trzni_poptavka->nacti_data_poptavek_z_databaze($this->nazev_trhu . "_poptavka");
        $trzni_poptavka->vytvor_trzni_poptavku();
        
        $trzni_nabidka = new trzni_nabidka($this->kolo, $this->nazev_trhu . "_nabidka");
        $trzni_nabidka->nacti_data_nabidek_z_databaze();
        $trzni_nabidka->vytvor_trzni_nabidku();

        $this->trzni_poptavka = $trzni_poptavka;
        $this->trzni_nabidka = $trzni_nabidka;

        $this->vypocti_trzni_cenu();
    }
    /*
     * Navazuje na předchozí metodu.
     */
    private function vypocti_trzni_cenu() {
        $trzni_poptavka = $this->trzni_poptavka;
        $trzni_nabidka = $this->trzni_nabidka;

        $pole_poptavky = $trzni_poptavka->getPole_trzni_poptavky();
        end ($pole_poptavky);
        $konec = key($pole_poptavky); //algoritmus končí tam, kde trh poptává 0
        $pole_nabidky = $trzni_nabidka->getPole_trzni_nabidky();
        $minimalni_rozdil = 1000000; //odstranit - hledat maximalni rozdil!!!
        $trzni_cena = 0;
        $delka_intervalu = 0;

        end ($pole_nabidky);
        for ($i = 1; $i <= $konec; $i++) {
            $poptavane_mnozstvi = $pole_poptavky[$i];
            $nabizene_mnozstvi = 0;
            /*
             * Pole nabídky může být menší než poptávky. Jelikož nabídka je shora
             * neomezená (výrobci jsou od dané hranice ochotni prodávat za jakoukoli
             * cenu), je určujeme převis na základě rozdílu mezi údajem v posledním
             * řádku databáze a poptávaným množstvím při dané ceně.
             */
            if ($i > sizeof($pole_nabidky)) {
                $nabizene_mnozstvi = current($pole_nabidky);
            } else {
                $nabizene_mnozstvi = $pole_nabidky[$i];
            }

            //převis řešíme jako absolutní hodnotu
            $rozdil = abs($poptavane_mnozstvi - $nabizene_mnozstvi);
            //porovnáváme současný převis s minimálním
            if ($minimalni_rozdil >  $rozdil) {
                $minimalni_rozdil = $rozdil;
                $zacatek_minimalniho_rozdilu = $i;
                $delka_intervalu = -1; //Protože v další podmínce se to změní na nulu
            }

            //měříme délku intervalu, abychom nalezli polovinu intervalu
            if ($minimalni_rozdil ==  $rozdil) {
                $delka_intervalu++;
            }

        }

        //cenu zaokrouhlujeme.
        $trzni_cena = round(($zacatek_minimalniho_rozdilu +
                $zacatek_minimalniho_rozdilu + $delka_intervalu) / 2);

        // Opět problém s nabídkou - ta není shora omezená, proto na konci pole nabídky
        // musíme začít dosazovat poslední položku.
        end($pole_nabidky);
        if ($trzni_cena > key($pole_nabidky)) {
            $nabizene_mnozstvi = current($pole_nabidky);
        } else {
            $nabizene_mnozstvi = $pole_nabidky[$trzni_cena];
        }
//        echo "Tržní cena - " . $this->nazev_trhu . ": " . $trzni_cena . "<br />";
//        echo "Nabízeno: " . $nabizene_mnozstvi .", poptáváno: " . $pole_poptavky[$trzni_cena] . "<br />";
        //KONTROLNÍ VÝPIS
        $this->trzni_cena = $trzni_cena;
        $this->nabizene_mnozstvi_pri_trzni_cene = $nabizene_mnozstvi;
        $this->poptavane_mnozstvi_pri_trzni_cene = $pole_poptavky[$trzni_cena];
    }

    public function zobchoduj_polozky_na_trhu() {
        $this->urci_trzni_cenu();

        $nabizene_mnozstvi_pri_trzni_cene = $this->nabizene_mnozstvi_pri_trzni_cene;
        $poptavane_mnozstvi_pri_trzni_cene = $this->poptavane_mnozstvi_pri_trzni_cene;

        $trzni_cena = $this->trzni_cena;

        if ($trzni_cena == 0) {
            return;
        }
        $pole_prikazu_prodeje = array();
        $pole_prikazu_nakupu = array();
        require_once 'tridy/prikaz_prodeje.php';
        require_once 'tridy/prikaz_nakupu.php';
        //Převis poptávky nad nabídkou, budeme krátit poptávku

        $pole_individualnich_nabidek = $this->trzni_nabidka->getPole_individualnich_nabidek();
        $pole_individualnich_poptavek = $this->trzni_poptavka->getPole_individualnich_poptavek();
        $prodano = 0;
        $nakoupeno = 0;

        if ($poptavane_mnozstvi_pri_trzni_cene > $nabizene_mnozstvi_pri_trzni_cene) {
            //krátíme poměrně
            $prepocitavaci_koeficient = $nabizene_mnozstvi_pri_trzni_cene / $poptavane_mnozstvi_pri_trzni_cene;

            foreach ($pole_individualnich_nabidek as $individualni_nabidka) {
                $prikaz_prodeje = new prikaz_prodeje($this->nazev_trhu, $this->kolo,
                    $individualni_nabidka->urci_nabizene_mnozstvi_pri_dane_cene($trzni_cena),
                    $trzni_cena, $individualni_nabidka->getLogin());

                $pole_prikazu_prodeje[] = $prikaz_prodeje;
                $prodano += $individualni_nabidka->urci_nabizene_mnozstvi_pri_dane_cene($trzni_cena);
            }

            
            foreach ($pole_individualnich_poptavek as $individualni_poptavka) {
                $prikaz_nakupu = new prikaz_nakupu($this->nazev_trhu, $this->kolo,
                    $individualni_poptavka->urci_pokracene_poptavane_mnozstvi_na_trhu($trzni_cena, $prepocitavaci_koeficient),
                    $trzni_cena, $individualni_poptavka->getLogin());
                $prikaz_nakupu->vygeneruj_a_proved_prikaz_pro_upravu_databaze();
                $nakoupeno += $individualni_poptavka->urci_pokracene_poptavane_mnozstvi_na_trhu($trzni_cena, $prepocitavaci_koeficient);
            }

            //Náhodně přidělujeme dosud nerozdělené komodity
            while ($nakoupeno < $nabizene_mnozstvi_pri_trzni_cene) {
                $nahodny_index = rand(0, sizeof($pole_prikazu_nakupu) -1);
                $pole_prikazu_nakupu[$nahodny_index]->zvys_nakoupene_mnozství_o_jednotku();
                $nakoupeno++;
            }

            foreach ($pole_prikazu_prodeje as $prikaz_prodeje) {
                $prikaz_prodeje->vygeneruj_a_proved_prikaz_pro_upravu_databaze();
            }

        } else {
            //krátit nabídky!!! - při rovnosti (vyčištění trhů) bude koeficient 1, nemá vliv na funkci!
            $prepocitavaci_koeficient = $poptavane_mnozstvi_pri_trzni_cene / $nabizene_mnozstvi_pri_trzni_cene;

            foreach ($pole_individualnich_poptavek as $individualni_poptavka) {
                $prikaz_nakupu = new prikaz_nakupu($this->nazev_trhu, $this->kolo,
                    $individualni_poptavka->urci_poptavane_mnozstvi_pri_dane_cene($trzni_cena),
                    $trzni_cena, $individualni_poptavka->getLogin());

                $pole_prikazu_nakupu[] = $prikaz_nakupu;
                $nakoupeno += $individualni_poptavka->urci_poptavane_mnozstvi_pri_dane_cene($trzni_cena);
            }

            foreach ($pole_individualnich_nabidek as $individualni_nabidka) {
                $prikaz_prodeje = new prikaz_prodeje($this->nazev_trhu, $this->kolo,
                    $individualni_nabidka->urci_pokracene_nabizene_mnozstvi_na_trhu($trzni_cena, $prepocitavaci_koeficient),
                    $trzni_cena, $individualni_nabidka->getLogin());
                $prikaz_prodeje->vygeneruj_a_proved_prikaz_pro_upravu_databaze();
                $pole_prikazu_prodeje[] = $prikaz_prodeje;
                $prodano += $individualni_nabidka->urci_pokracene_nabizene_mnozstvi_na_trhu($trzni_cena, $prepocitavaci_koeficient);
            }

            while ($prodano < $poptavane_mnozstvi_pri_trzni_cene) {
                $nahodny_index = rand(0, sizeof($pole_prikazu_nakupu) -1);
                $pole_prikazu_nakupu[$nahodny_index]->zvys_prodane_mnozstvi_o_jednotku();
                $prodano++;
            }

            foreach ($pole_prikazu_nakupu as $prikaz_nakupu) {
                $prikaz_nakupu->vygeneruj_a_proved_prikaz_pro_upravu_databaze();
            }
            
        }
    }

    /*
     * Kontrolní výpis tabulky, ve finální verzi kódu nebude.
     */
    public function vypis_tabulku() {
        $trzni_poptavka = $this->trzni_poptavka;
        $trzni_nabidka = $this->trzni_nabidka;

        $pole_poptavky = $trzni_poptavka->getPole_trzni_poptavky();
        end ($pole_poptavky);
        $konec = key($pole_poptavky);
        $pole_nabidky = $trzni_nabidka->getPole_trzni_nabidky();
        end ($pole_nabidky);
        echo '<table>';
        echo '<tr><th>P</th><th>Qpoptávané</th><th>Qnabízené</th></tr>';
        for ($i = 1; $i <= $konec; $i++) {
            echo '<tr ';
                if ($this->trzni_cena == $i) {
                    echo 'style="background-color: yellow"';
                }
            echo '>';
                echo '<td>';
                    echo $i;
                echo '</td>';
                echo '<td>';
                    echo $pole_poptavky[$i];
                echo '</td>';
                echo '<td>';
                    if ($i >= sizeof($pole_nabidky)) {
                        echo current($pole_nabidky);
                    } else {
                        echo $pole_nabidky[$i];
                    }
                    
                echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }


}
?>
