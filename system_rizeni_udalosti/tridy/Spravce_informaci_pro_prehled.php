<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_informaci_pro_prehled
 *
 * @author pike
 */
class Spravce_informaci_pro_prehled {
    //put your code here

    private $hrac;
    private $cislo_aktualniho_kola;
    
    function __construct(hrac $hrac, $cislo_aktualniho_kola) {
        $this->hrac = $hrac;
        $this->cislo_aktualniho_kola = $cislo_aktualniho_kola;
    }

    public function get_data_o_nakupech() {
        $login = $this->hrac->getLogin();
        $cislo_minuleho_kola = $this->cislo_aktualniho_kola - 1;

        $data_o_nakupech == array();

        $mnozstvi_spotrebovaneho_zbozi = 0;
        $mnozstvi_nakoupeneho_kapitaloveho_zbozi = 0;
        $mnozstvi_nakoupene_prace = 0;
        $velikost_ziskaneho_uveru_2_obdobi = 0;

        $dotaz_na_spotrebu = "SELECT mnozstvi_statku FROM spotreba WHERE login = '" . $login . "' " .
            "AND kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_spotreba = mysql_query($dotaz_na_spotrebu) or die ($dotaz_na_spotrebu);
        if (mysql_num_rows($vysledek_spotreba) == 1) {
            $radek = mysql_fetch_array($vysledek_spotreba);
            $mnozstvi_spotrebovaneho_zbozi = $radek['mnozstvi_statku'];
        }

        $dotaz_na_nakup_kapitaloveho_zbozi = "SELECT mnozstvi_kapitaloveho_zbozi FROM nakupy_kapitaloveho_zbozi WHERE login = '" . $login . "' " .
            "AND kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_nakup_kapitaloveho_zbozi = mysql_query($dotaz_na_nakup_kapitaloveho_zbozi) or die ($dotaz_na_nakup_kapitaloveho_zbozi);
        if (mysql_num_rows($vysledek_nakup_kapitaloveho_zbozi) == 1) {
            $radek = mysql_fetch_array($vysledek_nakup_kapitaloveho_zbozi);
            $mnozstvi_nakoupeneho_kapitaloveho_zbozi = $radek['mnozstvi_kapitaloveho_zbozi'];
        }

        $dotaz_na_nakup_prace = "SELECT hodin_prace FROM nakoupena_prace WHERE login = '" . $login . "' " .
            "AND kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_nakup_prace = mysql_query($dotaz_na_nakup_prace) or die ($dotaz_na_nakup_prace);
        if (mysql_num_rows($vysledek_nakup_prace) == 1) {
            $radek = mysql_fetch_array($vysledek_nakup_prace);
            $mnozstvi_nakoupene_prace = $radek['hodin_prace'];
        }

        $dotaz_na_ziskane_uvery = "SELECT castka FROM uvery_ziskane_2_obdobi WHERE login = '" . $login . "' " .
            "AND kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_ziskane_uvery = mysql_query($dotaz_na_ziskane_uvery) or die ($dotaz_na_ziskane_uvery);
        if (mysql_num_rows($vysledek_ziskane_uvery) == 1) {
            $radek = mysql_fetch_array($vysledek_ziskane_uvery);
            $velikost_ziskaneho_uveru_2_obdobi = $radek['castka'];
        }

        $dotaz_na_vyvoj_trznich_cen = "SELECT * FROM vyvoj_trznich_cen WHERE kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_vyvoj_trznich_cen = mysql_query($dotaz_na_vyvoj_trznich_cen) or die ($dotaz_na_vyvoj_trznich_cen);
        while ($radek = mysql_fetch_array($vysledek_vyvoj_trznich_cen)) {
            if ($radek['nazev_trhu'] == 'trh_spotrebniho_zbozi') {
                $data_spotreba = array("mnozstvi" => $mnozstvi_spotrebovaneho_zbozi, "cena" => $radek['cena']);
                $data_o_nakupech['spotrebni_zbozi'] = $data_spotreba;
            }
            if ($radek['nazev_trhu'] == 'trh_kapitaloveho_zbozi') {
                $data_kapitalove_zbozi = array("mnozstvi" => $mnozstvi_nakoupeneho_kapitaloveho_zbozi, "cena" => $radek['cena']);
                $data_o_nakupech['kapitalove_zbozi'] = $data_kapitalove_zbozi;
            }
            if ($radek['nazev_trhu'] == 'trh_prace') {
                $data_prace = array("mnozstvi" => $mnozstvi_nakoupene_prace, "cena" => $radek['cena']);
                $data_o_nakupech['prace'] = $data_prace;
            }
            if ($radek['nazev_trhu'] == 'trh_kapitalu_2_obdobi') {
                $data_kapital_2_obdobi = array("mnozstvi" => $velikost_ziskaneho_uveru_2_obdobi, "cena" => $radek['cena']);
                $data_o_nakupech['kapital_2_obdobi'] = $data_kapital_2_obdobi;
            }
        }

        return $data_o_nakupech;
    }

    public function get_data_o_prodejich() {
        $login = $this->hrac->getLogin();
        $cislo_minuleho_kola = $this->cislo_aktualniho_kola - 1;

        $mnozstvi_prodaneho_spotrebniho_zbozi = 0;
        $mnozstvi_prodaneho_kapitaloveho_zbozi = 0;
        $mnozstvi_prodane_prace = 0;
        $velikost_zapujcenych_uspor_2_obdobi = 0;

        $dotaz_na_prodane_spotrebni_zbozi = "SELECT mnozstvi_spotrebniho_zbozi FROM prodane_spotrebni_zbozi WHERE " .
            "login = '" . $login . "' AND kolo = " .  $cislo_minuleho_kola . "; ";
        $vysledek_spotrebni_zbozi = mysql_query($dotaz_na_prodane_spotrebni_zbozi) or die ($dotaz_na_prodane_spotrebni_zbozi);
        if (mysql_num_rows($vysledek_spotrebni_zbozi) == 1) {
            $radek = mysql_fetch_array($vysledek_spotrebni_zbozi);
            $mnozstvi_prodaneho_spotrebniho_zbozi = $radek['mnozstvi_spotrebniho_zbozi'];
            unset ($radek);
        }

        $dotaz_na_prodane_kapitalove_zbozi = "SELECT mnozstvi_kapitaloveho_zbozi FROM prodane_kapitalove_zbozi WHERE " .
            "login = '" . $login . "' AND kolo = " .  $cislo_minuleho_kola . "; ";
        $vysledek_kapitalove_zbozi = mysql_query($dotaz_na_prodane_kapitalove_zbozi) or die ($dotaz_na_prodane_kapitalove_zbozi);
        if (mysql_num_rows($vysledek_kapitalove_zbozi) == 1) {
            $radek = mysql_fetch_array($vysledek_kapitalove_zbozi);
            $mnozstvi_prodaneho_kapitaloveho_zbozi = $radek['mnozstvi_kapitaloveho_zbozi'];
            unset ($radek);
        }

        $dotaz_na_prodanou_praci = "SELECT hodin_prace FROM prodana_prace WHERE " .
            "login = '" . $login . "' AND kolo = " .  $cislo_minuleho_kola . "; ";
        $vysledek_prace = mysql_query($dotaz_na_prodanou_praci) or die ($dotaz_na_prodanou_praci);
        if (mysql_num_rows($vysledek_prace) == 1) {
            $radek = mysql_fetch_array($vysledek_prace);
            $mnozstvi_prodane_prace = $radek['hodin_prace'];
            unset ($radek);
        }

        $dotaz_na_zapujcene_uspory = "SELECT castka FROM uspory_zapujcene_2_obdobi WHERE " .
            "login = '" . $login . "' AND kolo = " .  $cislo_minuleho_kola . "; ";
        $vysledek_uspory = mysql_query($dotaz_na_zapujcene_uspory) or die ($dotaz_na_zapujcene_uspory);
        if (mysql_num_rows($vysledek_uspory) == 1) {
            $radek = mysql_fetch_array($vysledek_uspory);
            $velikost_zapujcenych_uspor_2_obdobi = $radek['castka'];
            unset ($radek);
        }

        $dotaz_na_vyvoj_trznich_cen = "SELECT * FROM vyvoj_trznich_cen WHERE kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_vyvoj_trznich_cen = mysql_query($dotaz_na_vyvoj_trznich_cen) or die ($dotaz_na_vyvoj_trznich_cen);
        while ($radek = mysql_fetch_array($vysledek_vyvoj_trznich_cen)) {
            if ($radek['nazev_trhu'] == 'trh_spotrebniho_zbozi') {
                $data_spotrebni_zbozi = array("mnozstvi" => $mnozstvi_prodaneho_spotrebniho_zbozi, "cena" => $radek['cena']);
                $data_o_prodejich['spotrebni_zbozi'] = $data_spotrebni_zbozi;
            }
            if ($radek['nazev_trhu'] == 'trh_kapitaloveho_zbozi') {
                $data_kapitalove_zbozi = array("mnozstvi" => $mnozstvi_prodaneho_kapitaloveho_zbozi, "cena" => $radek['cena']);
                $data_o_prodejich['kapitalove_zbozi'] = $data_kapitalove_zbozi;
            }
            if ($radek['nazev_trhu'] == 'trh_prace') {
                $data_prace = array("mnozstvi" => $mnozstvi_prodane_prace, "cena" => $radek['cena']);
                $data_o_prodejich['prace'] = $data_prace;
            }
            if ($radek['nazev_trhu'] == 'trh_kapitalu_2_obdobi') {
                $data_kapital_2_obdobi = array("mnozstvi" => $velikost_zapujcenych_uspor_2_obdobi, "cena" => $radek['cena']);
                $data_o_prodejich['kapital_2_obdobi'] = $data_kapital_2_obdobi;
            }
        }

        return $data_o_prodejich;
    }

    public function get_data_o_vyrobe() {
        $data_o_produkci = array();
        $cislo_minuleho_kola = $this->cislo_aktualniho_kola - 1;
        $login = $this->hrac->getLogin();

        $data_o_produkci['mnozstvi_spotrebni_zbozi'] = 0;
        $data_o_produkci['mnozstvi_kapitalove_zbozi'] = 0;

        $dotaz_na_zaznam_o_produkci = "SELECT * FROM zaznamy_o_produkci WHERE login = '" . $login . "' AND " .
            " kolo = " .  $cislo_minuleho_kola . "; ";
        $vysledek_zaznam_o_produkci = mysql_query($dotaz_na_zaznam_o_produkci) or die ($dotaz_na_zaznam_o_produkci);
        if (mysql_num_rows($vysledek_zaznam_o_produkci) == 1) {
            $radek = mysql_fetch_array($vysledek_zaznam_o_produkci);
            $data_o_produkci['mnozstvi_spotrebni_zbozi'] = $radek['spotrebni_zbozi'];
            $data_o_produkci['mnozstvi_kapitalove_zbozi'] = $radek['kapitalove_zbozi'];
        }

        return $data_o_produkci;
    }

    public function get_data_o_kapitalu($data_pro_prognozu) {
        if ($data_pro_prognozu == false) {
            $cislo_minuleho_kola = $this->cislo_aktualniho_kola - 1;
        } else {
            $cislo_minuleho_kola = $this->cislo_aktualniho_kola;
        }
        
        $login = $this->hrac->getLogin();

        $data_o_kapitalu = array();
        
        $dotaz_na_zmenu_hotovosti = "SELECT (SELECT mnozstvi_kapitalu FROM skladovane_polozky WHERE " .
            "login = '" . $login . "' AND kolo = " .  $cislo_minuleho_kola . ") -
            (SELECT mnozstvi_kapitalu FROM skladovane_polozky WHERE " .
            "login = '" . $login . "' AND kolo = " .  ($cislo_minuleho_kola - 1) . ") AS pohyb_hotovosti;";

        $vysledek_zmena_hotovosti = mysql_query($dotaz_na_zmenu_hotovosti) or die ($dotaz_na_zmenu_hotovosti);
        $radek = mysql_fetch_array($vysledek_zmena_hotovosti);
        $data_o_kapitalu['zmena_hotovosti'] = floor ($radek['pohyb_hotovosti']);

        $data_o_kapitalu['vyplacene_uroky'] = 0;
        $data_o_kapitalu['prijate_uroky'] = 0;
        $data_o_kapitalu['vyplacene_uvery'] = 0;
        $data_o_kapitalu['vracene_uspory'] = 0;

        $dotaz_na_uvery = "SELECT * FROM uvery_ziskane_2_obdobi WHERE login = '" . $login . "';";
        $vysledek_uvery = mysql_query($dotaz_na_uvery) or die ($dotaz_na_uvery);

        $nazev_trhu = 'trh_kapitalu_2_obdobi';

        $delka_uveru = 2;

        while ($radek = mysql_fetch_array($vysledek_uvery)) {
            extract($radek);

            $dotaz_na_urokovou_miru = "SELECT cena FROM vyvoj_trznich_cen WHERE kolo = ". $kolo . " AND nazev_trhu = '" . $nazev_trhu . "'; ";

            $vysledek = mysql_query($dotaz_na_urokovou_miru) or die($dotaz_na_urokovou_miru);
            $radek_mira = mysql_fetch_array($vysledek);
            $urokova_mira = $radek_mira['cena'];
            unset ($radek_mira);
            $urok = 0;

            
            
            if (($kolo < $cislo_minuleho_kola) && (($kolo + $delka_uveru) >= $cislo_minuleho_kola)) {
                $urok = ($urokova_mira / 100) * $castka;
                $data_o_kapitalu['vyplacene_uroky'] += floor($urok);
            }
            if ($kolo + $delka_uveru == $cislo_minuleho_kola) {
                $data_o_kapitalu['vyplacene_uvery'] += $castka;
            }
        }

        $dotaz_na_uspory = "SELECT * FROM uspory_zapujcene_2_obdobi WHERE login = '" . $login . "';";
        $vysledek_uspory = mysql_query($dotaz_na_uspory);

        while ($radek = mysql_fetch_array($vysledek_uspory)) {
            extract ($radek);

            $dotaz_na_urokovou_miru = "SELECT cena FROM vyvoj_trznich_cen WHERE kolo = " . $kolo . " AND nazev_trhu = 'trh_kapitalu_2_obdobi'; ";
            $vysledek = mysql_query($dotaz_na_urokovou_miru) or die ($dotaz_na_urokovou_miru);
            $radek_mira = mysql_fetch_array($vysledek);
            $urokova_mira = $radek_mira['cena'];
            $urok = 0;

            if (($kolo < $cislo_minuleho_kola) && (($kolo + $delka_uveru) >= $cislo_minuleho_kola)) {
                $urok = ($urokova_mira / 100) * $castka;
                $data_o_kapitalu['prijate_uroky'] += floor($urok);
            }
            if ($kolo + $delka_uveru == $cislo_minuleho_kola) {
                $data_o_kapitalu['vracene_uspory'] += $castka;
            }
        }

        return $data_o_kapitalu;
    }

    public function get_data_o_hodnoceni() {
        $cislo_minuleho_kola = $this->cislo_aktualniho_kola - 1;
        $login = $this->hrac->getLogin();

        $data_o_hodnoceni = array();

        $dotaz_na_zjisteni_poctu_ziskanych_bodu = "SELECT hodnoceni FROM hodnoceni_hracu WHERE login = '" . $login . "' " .
            "AND kolo = " . $cislo_minuleho_kola . "; ";
        $vysledek_zjisteni_poctu_ziskanych_bodu = mysql_query($dotaz_na_zjisteni_poctu_ziskanych_bodu) or die($dotaz_na_zjisteni_poctu_ziskanych_bodu);
        $radek = mysql_fetch_array($vysledek_zjisteni_poctu_ziskanych_bodu);

        $data_o_hodnoceni['ziskane_body'] = $radek['hodnoceni'];

        return $data_o_hodnoceni;
    }

}
?>
