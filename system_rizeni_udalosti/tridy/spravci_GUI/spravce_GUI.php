<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of spravce_gui
 *
 * @author pike
 */
class spravce_GUI extends Trida_reagujici_na_udalosti {
    //put your code here

    private $cislo_aktualniho_kola;
    private $hrac;
    private $zpracovana_data_z_odeslanych_formularu;
    private $prekladac;
    private $spravce_konfigurace;

    function __construct() {
        $this->pole_identifikatoru_udalosti['ZADOST_O_STRANKU'] = 'zpracuj_zadost_o_uzivatelske_rozhrani';
        $this->pole_identifikatoru_udalosti['ODESLANI_FORMULARE_POPTAVKY'] = 'uloz_data_z_odeslaneho_formulare_poptavky';
        $this->pole_identifikatoru_udalosti['ODESLANI_FORMULARE_NABIDKY'] = 'uloz_data_z_odeslaneho_formulare_nabidky';
        $this->pole_identifikatoru_udalosti['ODESLANI_FORMULARE_PRODUKCE'] = 'uloz_data_z_odeslaneho_formulare_produkce';
        parent::__construct();

        $this->prekladac = new Prekladac();

        require_once('inc/mysql_connect.php');
        $mysql_connection = new mysql_connection();
        $spojeni = $mysql_connection->otevri_pripojeni();

        require_once('inc/config.php');
        $this->cislo_aktualniho_kola = $GLOBALS['spravce_konfigurace']->get_cislo_aktualniho_kola();
        $this->spravce_konfigurace = $GLOBALS['spravce_konfigurace'];

    }

    public function uloz_data_z_odeslaneho_formulare_nabidky($odesilatel, $parametry) {
        $this->zpracovana_data_z_odeslanych_formularu['formular_nabidky']['pole_meznich_cen'] = $parametry['pole_meznich_cen'];
        $this->zpracovana_data_z_odeslanych_formularu['formular_nabidky']['pole_chyb'] = $parametry['pole_chyb'];
    }

    public function uloz_data_z_odeslaneho_formulare_poptavky($odesilatel, $parametry) {
        $this->zpracovana_data_z_odeslanych_formularu['formular_poptavky']['pole_meznich_cen'] = $parametry['pole_meznich_cen'];
    }

    public function uloz_data_z_odeslaneho_formulare_produkce ($odesilatel, $parametry) {
        $this->zpracovana_data_z_odeslanych_formularu['formular_produkce']['default_data'] = $parametry['default_data'];
        $this->zpracovana_data_z_odeslanych_formularu['formular_produkce']['pole_chyb'] = $parametry['pole_chyb'];
    }

    public function zpracuj_zadost_o_uzivatelske_rozhrani($odesilatel, $parametry) {
        require_once 'html/html_struktura.php';
        html_struktura::vloz_xhtml_hlavicku();
        echo "<div id=\"wrap\">";
            echo "<div id=\"header\"><h1><a href=\"index.php\">DSGE Game</a></h1></div>";

            if ( ($_SESSION['auth'] != 1) && (getenv("REMOTE_USER") == false) ) {
                echo "<div id=\"main\">";
                    include 'formulare/prihlasovaci_formular.php';
                echo "</div>";
            } else {
                $hrac = $GLOBALS['hrac'];
                $this->hrac = $hrac;
                html_struktura::vloz_horizontalni_pruh($hrac, $this->cislo_aktualniho_kola);
                
                echo "<div id=\"main\">";
                    $this->vygeneruj_a_vloz_zadane_uzivatelske_rozhrani($parametry);
                echo "</div>";
                html_struktura::vloz_menu($hrac);
            }
        html_struktura::vloz_paticku_stranky();
    }


    private function vygeneruj_a_vloz_zadane_uzivatelske_rozhrani($parametry) {
        $dotaz_na_zjisteni_administratorských_prav = "SELECT * FROM administratorska_prava WHERE login = '" . $this->hrac->getLogin() ."'; ";
        $vysledek = mysql_query($dotaz_na_zjisteni_administratorských_prav);
        if (mysql_num_rows($vysledek) == 1) {
            $radek = mysql_fetch_array($vysledek);
            extract ($radek);
        }

        if (isset ($parametry['id_stranky']) == false) {
            $spravce_clanku = new Spravce_clanku();
            echo $spravce_clanku->get_uvodni_clanek();
        }
        if ($parametry['id_stranky'] == 'prehled') {
            $this->vloz_obsah_stranky_prehled();
        } else if ($parametry['id_stranky'] == 'trh') {
            $this->vloz_obsah_stranky_trh($parametry['id_trhu']);
        } else if ($parametry['id_stranky'] == 'produkce') {
            $this->vloz_obsah_stranky_produkce();
        } else if ($parametry['id_stranky'] == 'aktualni_stav') {

        } else if ($parametry['id_stranky'] == 'prognoza') {
            $this->vloz_obsah_stranky_prognoza($parametry);
        } else if ($parametry['id_stranky'] == 'administrace') {
            if ($this->hrac->get_pravo_prohlizeni_konfigurace_hry() == true) {
                $formular_administrace = new Formular_administrace($this->hrac);
            }
        } else if ($parametry['id_stranky'] == 'administrace_editor') {
            if ($this->hrac->get_pravo_prohlizeni_konfigurace_hry() == true) {
                $formular_editor = new Formular_editor();
                $spravce_clanku = new Spravce_clanku();
                $formular_editor->generuj_formular_editor($spravce_clanku->get_uvodni_clanek());
            }
        } else if ($parametry['id_stranky'] == 'seznam_hracu') {
            if ($this->hrac->over_opravneni_k_akci('prohlizet_seznam_hracu') == 1) {
                $this->vloz_obsah_stranky_seznam_hracu($parametry);
            }
        }
    }

    private function zjisti_a_vloz_trzni_cenu_z_minuleho_kola($id_trhu) {
        $dotaz_na_trzni_cenu_z_minuleho_kola = "SELECT * FROM vyvoj_trznich_cen WHERE kolo = " .
            ($this->cislo_aktualniho_kola - 1) . " AND nazev_trhu = '" . $id_trhu . "'; ";
        $vysledek = mysql_query($dotaz_na_trzni_cenu_z_minuleho_kola);
        if (mysql_num_rows($vysledek) != 1) {
            return;
        }
        $radek = mysql_fetch_array($vysledek);
        $trzni_cena_v_minulem_kole = $radek[cena];
        echo "Tržní cena v minulém kole byla: " . $trzni_cena_v_minulem_kole . "<br />";
    }

    private function vloz_obsah_stranky_trh($id_trhu) {
        $hrac = $this->hrac;
        if ($id_trhu == 'trh_spotrebniho_zbozi') {
            echo '<h3>Trh spotřebního zboží</h3>';
            $this->zjisti_a_vloz_trzni_cenu_z_minuleho_kola($id_trhu);
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_spotrebniho_zbozi_nabidka', "cena");
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_spotrebniho_zbozi_poptavka', "cena");
            $this->vloz_graf_vyvoje_trzni_ceny($id_trhu);
        } else if ($_GET['id_trhu'] == 'trh_kapitaloveho_zbozi') {
            echo '<h3>Trh kapitálového zboží</h3>';
            $this->zjisti_a_vloz_trzni_cenu_z_minuleho_kola($id_trhu);
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_kapitaloveho_zbozi_nabidka', "cena");
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_kapitaloveho_zbozi_poptavka', "cena");
            $this->vloz_graf_vyvoje_trzni_ceny($id_trhu);
        } else if ($_GET['id_trhu'] == 'trh_prace') {
            echo '<h3>Trh práce</h3>';

            $dotaz = "SELECT hodin_prace FROM prikazy_produkce WHERE login='" . $hrac->getLogin() .
                "' AND kolo= " . $this->cislo_aktualniho_kola . ";";
            $vysledek = mysql_query($dotaz);
            $radek = mysql_fetch_array($vysledek);
            if ($radek != false) {
                extract($radek);
                echo "Maximálně můžete nabízet " . (24 - $hodin_prace) . " hodin práce. <br/>";
            } else {
                echo "Maximálně můžete nabízet 24 hodin práce. <br/>";
            }

            $this->zjisti_a_vloz_trzni_cenu_z_minuleho_kola($id_trhu);
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_prace_nabidka', "hodinová mzda");
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_prace_poptavka', "hodinová mzda");
            $this->vloz_graf_vyvoje_trzni_ceny($id_trhu);

        } else if ($_GET['id_trhu'] == 'trh_kapitalu_2_obdobi') {
            echo '<h3>Trh kapitálu, 2 období</h3>';

            $this->zjisti_a_vloz_trzni_cenu_z_minuleho_kola($id_trhu);
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_kapitalu_2_obdobi_nabidka', "úroková míra");
            $this->formular_trhu_zajisteni_dat_a_vygenerovani('trh_kapitalu_2_obdobi_poptavka', "úroková míra");
            $this->vloz_graf_vyvoje_trzni_ceny($id_trhu);
        } else {
            echo "Tento trh neexistuje";
        }
    }

    private function vloz_obsah_stranky_produkce() {
        $formular_produkce = new formular_produkce();
        $default_data = $this->zjisti_vychozi_data_pro_formular_produkce();

        $formular_produkce->generuj_formular_produkce($default_data,
            $this->zpracovana_data_z_odeslanych_formularu['formular_produkce']['pole_chyb']);
        $this->vytvor_a_vloz_rozhodovaci_graf();
    }

    private function formular_trhu_zajisteni_dat_a_vygenerovani($nazev_tabulky, $oznaceni_P) {
        if (strstr($nazev_tabulky, 'nabidka')) {
            $formular = new formular_nabidka();
            $formular->generuj_formular_nabidky($this->zjisti_vychozi_data_pro_formular_nabidky($nazev_tabulky),
                $nazev_tabulky, $this->zpracovana_data_z_odeslanych_formularu['formular_nabidky']['pole_chyb'], $oznaceni_P);
        } else {
            $formular = new formular_poptavka();
            $formular->generuj_formular_poptavky($this->zjisti_vychozi_data_pro_formular_poptavky($nazev_tabulky), $nazev_tabulky, $oznaceni_P);
        }

    }

    private function zjisti_vychozi_data_pro_formular_poptavky ($nazev_tabulky) {
        $default_data = array (10 => 0, 20 => 0, 30 => 0, 40 => 0);

        $dotaz_na_mezni_ceny_z_predchozich_kol = "SELECT * FROM " . $nazev_tabulky . " WHERE login = '" . $this->hrac->getLogin() .
            "' ORDER BY kolo DESC; ";
        $vysledek_mezni_ceny_z_predchozich_kol = mysql_query($dotaz_na_mezni_ceny_z_predchozich_kol) or die ($vysledek_mezni_ceny_z_predchozich_kol);

        if (mysql_num_rows($vysledek_mezni_ceny_z_predchozich_kol) > 0) {
            $radek_mezni_ceny_z_predchozich_kol = mysql_fetch_array($vysledek_mezni_ceny_z_predchozich_kol);
            $default_data = array ($radek_mezni_ceny_z_predchozich_kol['cena1'] => 0, $radek_mezni_ceny_z_predchozich_kol['cena2'] => 0,
                $radek_mezni_ceny_z_predchozich_kol['cena3'] => 0, $radek_mezni_ceny_z_predchozich_kol['cena4'] => 0);
        }
        

        if (isset ($this->zpracovana_data_z_odeslanych_formularu['formular_poptavky']['pole_meznich_cen']) == false) {
            if ($this->zjisti_existenci_nabidky_nebo_poptavky_v_databazi($nazev_tabulky) != false) {
                $default_data = $this->zjisti_existenci_nabidky_nebo_poptavky_v_databazi($nazev_tabulky);
            }
        } else {
            $default_data = $this->zpracovana_data_z_odeslanych_formularu['formular_poptavky']['pole_meznich_cen'];
        }

        return $default_data;
    }



    private function zjisti_vychozi_data_pro_formular_nabidky ($nazev_tabulky) {
        $default_data = array (10 => 0, 20 => 0, 30 => 0, 40 => 0);

        $dotaz_na_mezni_ceny_z_predchozich_kol = "SELECT * FROM " . $nazev_tabulky . " WHERE login = '" . $this->hrac->getLogin() .
            "' ORDER BY kolo DESC; ";
        $vysledek_mezni_ceny_z_predchozich_kol = mysql_query($dotaz_na_mezni_ceny_z_predchozich_kol) or die ($vysledek_mezni_ceny_z_predchozich_kol);

        if (mysql_num_rows($vysledek_mezni_ceny_z_predchozich_kol) > 0) {
            $radek_mezni_ceny_z_predchozich_kol = mysql_fetch_array($vysledek_mezni_ceny_z_predchozich_kol);
            $default_data = array ($radek_mezni_ceny_z_predchozich_kol['cena1'] => 0, $radek_mezni_ceny_z_predchozich_kol['cena2'] => 0,
                $radek_mezni_ceny_z_predchozich_kol['cena3'] => 0, $radek_mezni_ceny_z_predchozich_kol['cena4'] => 0);
        }

        if (isset ($this->zpracovana_data_z_odeslanych_formularu['formular_nabidky']['pole_meznich_cen']) == false) {
            if ($this->zjisti_existenci_nabidky_nebo_poptavky_v_databazi($nazev_tabulky) != false) {
                $default_data = $this->zjisti_existenci_nabidky_nebo_poptavky_v_databazi($nazev_tabulky);
            }
        } else {
            $default_data = $this->zpracovana_data_z_odeslanych_formularu['formular_nabidky']['pole_meznich_cen'];
        }

        return $default_data;
    }

    private function zjisti_vychozi_data_pro_formular_produkce() {
        $default_data = array('hodin_prace' => 0, 'druh_zbozi' => 1);

        if (isset ($this->zpracovana_data_z_odeslanych_formularu['formular_produkce']['default_data']) == false) {
            if ($this->zjisti_existenci_prikazu_produkce_v_databazi() != false) {
                $default_data = $this->zjisti_existenci_prikazu_produkce_v_databazi();
            }
        } else {
            $default_data = $this->zpracovana_data_z_odeslanych_formularu['formular_produkce']['default_data'];
        }
        return $default_data;
    }

    public function zjisti_existenci_nabidky_nebo_poptavky_v_databazi($nazev_tabulky) {
        $dotaz = "SELECT * FROM ". $nazev_tabulky .
            " WHERE kolo = " . $this->cislo_aktualniho_kola . " AND login = '" . $this->hrac->getLogin() ."'; ";
        $vysledek = mysql_query($dotaz) or die ($dotaz);

        $default_data == array();
        if (mysql_num_rows($vysledek) > 0) {
            $radek = mysql_fetch_array($vysledek);
            $default_data = array();

            foreach ($radek as $aktualni_klic_hledani_ceny => $aktualni_hodnota_hledani_ceny) {
                if (strstr($aktualni_klic_hledani_ceny, 'cena') != false) {
                    $cislo_aktualni_polozky = preg_replace("/\D/", "", $aktualni_klic_hledani_ceny);
                    foreach ($radek as $aktualni_klic_hledani_mnozstvi => $aktualni_hodnota_hledani_mnozstvi) {
                        if (strstr($aktualni_klic_hledani_mnozstvi, 'mnozstvi' . $cislo_aktualni_polozky) != false) {
                            $default_data[$aktualni_hodnota_hledani_ceny] = $aktualni_hodnota_hledani_mnozstvi;
                        }
                    }
                }
            }
            return $default_data;
        }
        return false;
    }

    function zjisti_existenci_prikazu_produkce_v_databazi() {
        $dotaz = "SELECT * FROM prikazy_produkce WHERE login = '" . $this->hrac->getLogin() . "' AND kolo = " . $this->cislo_aktualniho_kola;
        $vysledek = mysql_query($dotaz);
        if (mysql_num_rows($vysledek) == 1) {
            $radek = mysql_fetch_array($vysledek);
            extract($radek);
            $default_data = array();
            $default_data['hodin_prace'] = $hodin_prace;
            $default_data['druh_zbozi'] = $druh_zbozi;
            return $default_data;
        }
        return false;
    }

    private function vytvor_a_vloz_rozhodovaci_graf () {
        include_once 'formulare/formular_grafu_produkce.php';

        $hrac = $this->hrac;
        $dotaz_na_mnozstvi_kapitaloveho_zbozi = "SELECT mnozstvi_kapitaloveho_zbozi FROM kapitalove_zbozi_ve_vyrobe " .
            "WHERE login='" . $this->hrac->getLogin() . "' AND kolo = " . $this->cislo_aktualniho_kola . ";";

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
            echo "<img src=\"grafy/individualni_produkcni_funkce.php?variabilni_vyrobni_faktor=".
                trim($default_data_pro_formular_grafu['variabilni_vyrobni_faktor']) .
                "&amp;mnozstvi_fixniho_faktoru=". trim($default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru'])  .
                "&amp;osa_x_max=" . trim($default_data_pro_formular_grafu['osa_x_max'])  .  "\" alt=\"Průběh produkční funkce\" />";
        echo "</p>";

    }

    private function vloz_graf_vyvoje_trzni_ceny($id_trhu) {
        if ($this->cislo_aktualniho_kola > 1) {
            echo "<p style=\"text-align:center\">";
                echo "<img src=\"grafy/vyvoj_trzni_ceny.php?id_trhu=" . $id_trhu . "\" alt=\"Průběh produkční funkce\" />";
            echo "</p>";
        }
    }

    private function vloz_obsah_stranky_prehled() {
        $spravce_informaci_pro_prehled = new Spravce_informaci_pro_prehled($this->hrac, $this->cislo_aktualniho_kola);

        if ($this->cislo_aktualniho_kola > 1) {
            $data_o_nakupech = $spravce_informaci_pro_prehled->get_data_o_nakupech();

            echo "<h3>" . $this->prekladac->vloz_retezec("nadpis_prehled", array()) . "</h3>";
            echo "<p> V minulém kole jste</p>";
            echo "<ul>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_spotreba", $data_o_nakupech['spotrebni_zbozi']) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_nakup_kapitalove_zbozi", $data_o_nakupech['kapitalove_zbozi']) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_nakup_prace", $data_o_nakupech['prace']) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_nakup_kapital_2_obdobi", $data_o_nakupech['kapital_2_obdobi']) . "</li>";
            echo "</ul>";

            $data_o_prodejich = $spravce_informaci_pro_prehled->get_data_o_prodejich();
            echo "<ul>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_prodej_spotrebni_zbozi", $data_o_prodejich['spotrebni_zbozi']) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_prodej_kapitalove_zbozi", $data_o_prodejich['kapitalove_zbozi']) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_prodej_prace", $data_o_prodejich['prace']) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_prodej_kapital_2_obdobi", $data_o_prodejich['kapital_2_obdobi']) . "</li>";
            echo "</ul>";

            $data_o_vyrobe = $spravce_informaci_pro_prehled->get_data_o_vyrobe();
            echo "<ul>";
                echo "<li>" . $this->prekladac->vloz_retezec("produkce", $data_o_vyrobe) . "</li>";
            echo "</ul>";

            $data_o_kapitalu = $spravce_informaci_pro_prehled->get_data_o_kapitalu(false);
            echo "<ul>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_kapital_celkem", $data_o_kapitalu) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_kapital_vyplacene_uroky", $data_o_kapitalu) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_kapital_prijate_uroky", $data_o_kapitalu) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_kapital_vyplacene_uvery", $data_o_kapitalu) . "</li>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_kapital_vracene_uspory", $data_o_kapitalu) . "</li>";
            echo "</ul>";

            $data_o_hodnoceni = $spravce_informaci_pro_prehled->get_data_o_hodnoceni();
            echo "<ul>";
                echo "<li>" . $this->prekladac->vloz_retezec("prehled_hodnoceni_ziskane_body", $data_o_hodnoceni) . "</li>";
            echo "</ul>";
        }
    }

    private function vloz_obsah_stranky_prognoza($parametry) {
        $dotaz_na_trzni_ceny_v_minulem_kole = "SELECT * FROM vyvoj_trznich_cen WHERE kolo = " . ($this->cislo_aktualniho_kola - 1) . "; ";
        $vysledek_trzni_ceny_v_minulem_kole = mysql_query($dotaz_na_trzni_ceny_v_minulem_kole) or die ($dotaz_na_trzni_ceny_v_minulem_kole);

        $trzni_ceny = array();
        while ($radek_trzni_cena = mysql_fetch_array($vysledek_trzni_ceny_v_minulem_kole)) {
            $trzni_ceny[$radek_trzni_cena['nazev_trhu']] = $radek_trzni_cena['cena'];

            if (isset ($parametry[$radek_trzni_cena['nazev_trhu']])) {
                $trzni_ceny[$radek_trzni_cena['nazev_trhu']] = $parametry[$radek_trzni_cena['nazev_trhu']];
            }
        }

        $seznam_trhu = $this->spravce_konfigurace->get_aktivni_trhy();
        $mnozstvi_kapitalu = $this->hrac->getMnozstvi_kapitalu();
        $mnozstvi_spotrebovanych_statku = 0;
        echo $this->prekladac->vloz_retezec("prognoza_popis", array());


        echo "<table>";
        echo "<tr><th>" . $this->prekladac->vloz_retezec("trh", array()) . "</th><th>" . $this->prekladac->vloz_retezec("trzni_cena", array()) . "</th><th>" .
            $this->prekladac->vloz_retezec("prijmy", array()) . "</th><th>" . $this->prekladac->vloz_retezec("vydaje", array()) . " </th></tr>";

        $mnozstvi_volneho_casu = $this->hrac->get_mnozstvi_volneho_casu();
        foreach ($seznam_trhu as $aktualni_trh) {
            $individualni_nabidka = new individualni_nabidka($this->hrac->getLogin(), $this->cislo_aktualniho_kola);
            $individualni_nabidka->nacti_data_nabidky_z_databaze($aktualni_trh . "_nabidka");
            $prijem = $individualni_nabidka->urci_nabizene_mnozstvi_pri_dane_cene($trzni_ceny[$aktualni_trh]) * $trzni_ceny[$aktualni_trh];


            $individualni_poptavka = new individualni_poptavka($this->hrac->getLogin(), $this->cislo_aktualniho_kola);
            $individualni_poptavka->nacti_data_poptavky_z_databaze($aktualni_trh . "_poptavka");
            $vydaj = $individualni_poptavka->urci_poptavane_mnozstvi_pri_dane_cene($trzni_ceny[$aktualni_trh]) * $trzni_ceny[$aktualni_trh];

            if (strstr($aktualni_trh, "spotrebni")) {
                $mnozstvi_spotrebovanych_statku += $individualni_poptavka->urci_poptavane_mnozstvi_pri_dane_cene($trzni_ceny[$aktualni_trh]);
            }

            if (strstr($aktualni_trh, "prace")) {
                $mnozstvi_volneho_casu -= $individualni_nabidka->urci_nabizene_mnozstvi_pri_dane_cene($trzni_ceny[$aktualni_trh]);
            }

            if (strstr($aktualni_trh, "kapitalu")) {
                if ($trzni_ceny[$aktualni_trh] > 0) {
                    $prijem /= $trzni_ceny[$aktualni_trh];
                    $vydaj /= $trzni_ceny[$aktualni_trh];
                }
                

                $pom = $prijem;
                $prijem = $vydaj;
                $vydaj = $pom;
            }

            $mnozstvi_kapitalu += $prijem;
            $mnozstvi_kapitalu -= $vydaj;

            echo "<tr><td>" . $this->prekladac->vloz_retezec($aktualni_trh, array()) . "</td><td style=\"text-align:center\">" .
                $trzni_ceny[$aktualni_trh] . "</td><td style=\"text-align:center\">" . $prijem . "</td><td style=\"text-align:center\">" .
                $vydaj . "</td></tr>";
        }

        echo "</table>";
        
        $spravce_informaci_pro_prehled = new Spravce_informaci_pro_prehled($this->hrac, $this->cislo_aktualniho_kola);
        $data_o_kapitalu = $spravce_informaci_pro_prehled->get_data_o_kapitalu(true);

        echo "<p>" . $this->prekladac->vloz_retezec("prognoza_dalsi_transakce", $data_o_kapitalu) . "</p>";
        echo "<ul>";
            echo "<li>" . $this->prekladac->vloz_retezec("prognoza_kapital_vyplacene_uroky", $data_o_kapitalu) . "</li>";
            echo "<li>" . $this->prekladac->vloz_retezec("prognoza_kapital_prijate_uroky", $data_o_kapitalu) . "</li>";
            echo "<li>" . $this->prekladac->vloz_retezec("prognoza_kapital_vyplacene_uvery", $data_o_kapitalu) . "</li>";
            echo "<li>" . $this->prekladac->vloz_retezec("prognoza_kapital_vracene_uspory", $data_o_kapitalu) . "</li>";
        echo "</ul>";

        $mnozstvi_kapitalu = $mnozstvi_kapitalu - $data_o_kapitalu['vyplacene_uroky'] + $data_o_kapitalu['prijate_uroky']
            + $data_o_kapitalu['vracene_uspory'] - $data_o_kapitalu['vyplacene_uvery'];

        echo "<p>" . $this->prekladac->vloz_retezec("prognoza_celkem", array("mnozstvi_kapitalu" => $mnozstvi_kapitalu)) . "</p>";

        if ($mnozstvi_kapitalu < 0) {
            $zaporne_mnozstvi_kapitalu = 1;
        } else {
            $zaporne_mnozstvi_kapitalu = 0;
        }

        if ($this->hrac->get_aktivita_v_aktualnim_kole() == 1) {
            $neaktivni = 0;
        } else {
            $neaktivni = 1;
        }

        $mnozstvi_bodu = Spravce_hodnoceni::hodnotici_funkce($this->cislo_aktualniho_kola, $mnozstvi_volneho_casu, $mnozstvi_spotrebovanych_statku,
            $zaporne_mnozstvi_kapitalu, $neaktivni);
        echo "<p>" . $this->prekladac->vloz_retezec("prognoza_celkem_body", array("mnozstvi_bodu" => $mnozstvi_bodu)) . "</p>";

        $formular_prognoza = new Formular_prognoza();
        $formular_prognoza->generuj_formular_prognozy($trzni_ceny, $this->spravce_konfigurace, $this->prekladac);
    }

    private function vloz_obsah_stranky_seznam_hracu($parametry) {
        if (isset($parametry['login']) == false) {
            $dotaz_na_seznam_hracu = "SELECT * FROM hraci;";
            $vysledek_seznam_hracu = mysql_query($dotaz_na_seznam_hracu) or die ($dotaz_na_seznam_hracu);

            echo "<table style=\"width: 100%\">";
            while ($radek_seznam_hracu = mysql_fetch_array($vysledek_seznam_hracu)) {
                if ($this->hrac->over_opravneni_k_akci('rozsirene_prohlizeni_seznamu_hracu') == false) {
                    $aktualni_hrac = new hrac($radek_seznam_hracu['login']);
                    echo "<tr>";
                        echo "<td>";
                            echo "<a href=\"index.php?id_stranky=seznam_hracu&amp;login=". $aktualni_hrac->getLogin() . "\">" .
                                $aktualni_hrac->getLogin() . "</a>";
                        echo "</td>";
                        echo "<td>";
                            echo $aktualni_hrac->get_pocet_bodu();
                        echo "</td>";
                    echo "</tr>";
                }
            }

            echo "</table>";
        } else {
            echo "Hráč: " . $parametry['login'];
        }
    }

}
?>
