<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of spravce_GUI_produkce
 *
 * @author pike
 */
class spravce_GUI_produkce {
    //put your code here

    private $cislo_aktualniho_kola;
    private $hrac;
    private $default_data;
    private $pole_chyb;

    function __construct($cislo_aktualniho_kola, $hrac) {
        require_once ('formulare/formular_produkce.php');
        require_once ('tridy/prikaz_produkce.php');
        $this->cislo_aktualniho_kola = $cislo_aktualniho_kola;
        $this->hrac = $hrac;

        $default_data = array('hodin_prace' => 0, 'druh_zbozi' => 1);
        $pole_chyb = array();
        $this->default_data = $default_data;
        $this->pole_chyb = $pole_chyb;

        if (isset ($_POST['vyrabene_zbozi'])) {
            $this->zpracuj_vysledek_formulare();
        }

        if (sizeof($this->pole_chyb) == 0) {
            $this->zkontroluj_existenci_prikazu_produkce();
        }

        $this->vloz_formular_pro_zadani_prikazu_produkce();
        $this->vytvor_a_vloz_rozhodovaci_graf();

    }

    function vloz_formular_pro_zadani_prikazu_produkce() {
        
        $formular_produkce = new formular_produkce();
        $formular_produkce->generuj_formular_produkce($this->default_data, $this->pole_chyb);
    }

//    function zkontroluj_existenci_prikazu_produkce() {
//        $dotaz = "SELECT * FROM prikazy_produkce WHERE login = '" . $this->hrac->getLogin() . "' AND kolo = " . $this->cislo_aktualniho_kola;
//        $vysledek = mysql_query($dotaz);
//        if (mysql_num_rows($vysledek) == 1) {
//            $radek = mysql_fetch_array($vysledek);
//            extract($radek);
//            $default_data = array();
//            $default_data['hodin_prace'] = $hodin_prace;
//            $default_data['druh_zbozi'] = $druh_zbozi;
//            $this->default_data = $default_data;
//        }
//    }

    //nejprve pracujeme jenom se změnou množství práce
    function vytvor_a_vloz_rozhodovaci_graf () {
        include_once 'formulare/formular_grafu_produkce.php';

        $hrac = $this->hrac;
        $dotaz_na_mnozstvi_kapitaloveho_zbozi = "SELECT mnozstvi_kapitaloveho_zbozi FROM kapitalove_zbozi_ve_vyrobe " .
            "WHERE login='" . $hrac->getLogin()  . "';";

        $mnozstvi_kapitaloveho_zbozi = 0;

        $vysledek_mnozstvi_kapitaloveho_zbozi = mysql_query($dotaz_na_mnozstvi_kapitaloveho_zbozi);
        if (mysql_num_rows($vysledek_mnozstvi_kapitaloveho_zbozi) > 0) {
            $radek_mnozstvi_kapitaloveho_zbozi = mysql_fetch_array($vysledek_mnozstvi_kapitaloveho_zbozi);
            extract($radek_mnozstvi_kapitaloveho_zbozi);
        }

        echo "V současné době je do výroby zapojeno " . $mnozstvi_kapitaloveho_zbozi . " kapitálových statků<br />";

        $default_data_pro_formular_grafu = array('variabilni_vyrobni_faktor' => 'prace',
            'mnozstvi_fixniho_faktoru' => $mnozstvi_kapitaloveho_zbozi, 'osa_x_max' => 100);

        if (isset ($_GET['mnozstvi_fixniho_faktoru'])) {
            $default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru'] = $_GET['mnozstvi_fixniho_faktoru'];
        }

        if (isset ($_GET['graf_variabilni_vyrobni_faktor'])) {
            $default_data_pro_formular_grafu['variabilni_vyrobni_faktor'] = $_GET['graf_variabilni_vyrobni_faktor'];
        }

        if (isset ($_GET['osa_x_max'])) {
            $default_data_pro_formular_grafu['osa_x_max'] = $_GET['osa_x_max'];
        }

        $formular_grafu_produkce = new formular_grafu_produkce();
        $formular_grafu_produkce->generuj_formular_grafu_produkce($default_data_pro_formular_grafu);

        $prace = array();
        $produkce = array();


        echo "<p style=\"text-align:center\">";
            echo "<img src=\"grafy/individualni_produkcni_funkce.php?variabilni_vyrobni_faktor=". trim($default_data_pro_formular_grafu['variabilni_vyrobni_faktor']) .
                "&amp;mnozstvi_fixniho_faktoru=". trim($default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru'])  .
                "&amp;osa_x_max=" . trim($default_data_pro_formular_grafu['osa_x_max'])  .  "\" alt=\"Průběh produkční funkce\" />";
        echo "</p>";

    }



    //chyba E - překročení limitu 24 hodin, příkaz není uložen (E - error)
    //chyba S - nesoulad mezi nejvyšší nabízenou dobou práce a práci pro vlastní potřebu (S - chyba sumace)
//    function zpracuj_vysledek_formulare() {
//        $default_data = array();
//
//        if ($this->zkontroluj_zadane_hodiny_prace() == false) {
//            return;
//        }
//
//        $prikaz_produkce = new prikaz_produkce();
//        $prikaz_produkce->setHodin_prace($_POST['hodin_prace']);
//        $druh_zbozi = 0;
//        if ($_POST['vyrabene_zbozi'] == 'kapitalove_zbozi') {
//            $druh_zbozi = 2;
//        } else {
//            $druh_zbozi = 1;
//        }
//        $prikaz_produkce->setDruh_zbozi($druh_zbozi);
//
//
//        $dotaz = "SELECT * FROM prikazy_produkce WHERE login = '" . $this->hrac->getLogin() . "' AND kolo = ". $this->cislo_aktualniho_kola;
//        $vysledek = mysql_query($dotaz);
//
//        $dotaz = "";
//        if (mysql_num_rows($vysledek) == 0) {
//            $dotaz = $prikaz_produkce->vytvor_SQL_definici($this->hrac->getLogin(), $this->cislo_aktualniho_kola, true);
//        } else {
//            $dotaz = $prikaz_produkce->vytvor_SQL_definici($this->hrac->getLogin(), $this->cislo_aktualniho_kola, false);
//        }
//
//        mysql_query($dotaz);
//    }

//    function zkontroluj_zadane_hodiny_prace() {
//        $hodin_prace = $_POST['hodin_prace'];
//        $pole_chyb = array();
//        $default_data = array();
//
//        if ($hodin_prace > 24) {
//            $pole_chyb[1] = "E";
//            $this->nastav_default_data_pri_chybe();
//            $this->pole_chyb = $pole_chyb;
//            return false;
//        }
//
//        $dotaz = "SELECT mnozstvi4, mnozstvi3, mnozstvi2, mnozstvi1 FROM trh_prace_nabidka " .
//            " WHERE login='" . $this->hrac->getLogin() . "' AND kolo = " . $this->cislo_aktualniho_kola ."; ";
//        $vysledek = mysql_query($dotaz);
//        if (mysql_num_rows($vysledek) > 0) {
//            $radek = mysql_fetch_array($vysledek);
//            extract($radek);
//
//            if (($hodin_prace + $mnozstvi4) > 24) {
//                $pole_chyb[1] = "S";
//                $this->pole_chyb = $pole_chyb;
//                $this->nastav_default_data_pri_chybe();
//                return false;
//            }
//            if (($hodin_prace + $mnozstvi3) > 24) {
//                $pole_chyb[1] = "S";
//                $this->pole_chyb = $pole_chyb;
//                $this->nastav_default_data_pri_chybe();
//                return false;
//            }
//            if (($hodin_prace + $mnozstvi2) > 24) {
//                $pole_chyb[1] = "S";
//                $this->pole_chyb = $pole_chyb;
//                $this->nastav_default_data_pri_chybe();
//                return false;
//            }
//            if (($hodin_prace + $mnozstvi1) > 24) {
//                $pole_chyb[1] = "S";
//                $this->pole_chyb = $pole_chyb;
//                $this->nastav_default_data_pri_chybe();
//                return false;
//            }
//        }
//
//        return true;
//    }
//
//    function nastav_default_data_pri_chybe() {
//        $hodin_prace = $_POST['hodin_prace'];
//        $pole_chyb = array();
//        $default_data = array();
//
//        $default_data['hodin_prace'] = $hodin_prace;
//
//        $druh_zbozi = 0;
//        if ($_POST['vyrabene_zbozi'] == 'kapitalove_zbozi') {
//            $druh_zbozi = 2;
//        } else {
//            $druh_zbozi = 1;
//        }
//        $default_data['druh_zbozi'] = $druh_zbozi;
//
//        $this->default_data = $default_data;
//    }
}
?>
