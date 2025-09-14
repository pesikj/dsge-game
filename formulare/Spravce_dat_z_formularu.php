<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_formularu
 *
 * @author pike
 */
class Spravce_dat_z_formularu extends Trida_generujici_udalosti {
    //put your code here

    private $data_ke_zpracovani;
    private $hrac;
    private $subjekt;
    private $cislo_aktualniho_kola;
    private $spravce_konfigurace;
    private $informace_o_ekonomikach;

    public function __construct($data_ke_zpracovani) {
        $this->informace_o_ekonomikach = Informace_o_ekonomikach::get_informace_o_ekonomikach();
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_NABIDKY';
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_POPTAVKY';
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_VYROBA';
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_REGISTRACE';
        $this->pole_identifikatoru_udalosti[] = 'ZADOST_NOVEHO_HRACE_O_ZAPOJENI_DO_HRY';
        $this->pole_identifikatoru_udalosti[] = 'ODESLANI_FORMULARE_NASTAVENI_HRACE';

        $this->data_ke_zpracovani = $data_ke_zpracovani;
        $this->hrac = $GLOBALS['hrac'];
        if (isset ($GLOBALS['subjekt']) == true) {
            $this->subjekt = $GLOBALS['subjekt'];
        }
        $this->cislo_aktualniho_kola = $GLOBALS['cislo_aktualniho_kola'];
        $this->spravce_konfigurace= $GLOBALS['spravce_konfigurace'];

        parent::__construct();
    }

    public function spust_zpracovani_dat() {
        if ($this->data_ke_zpracovani['typ_formulare'] == 'zapojeni') {
            $this->zpracovani_formulare_zapojeni();
        } else if ($this->data_ke_zpracovani['typ_formulare'] == 'registrace') {
            $this->zpracovani_formulare_registrace();
        }

        switch ($this->data_ke_zpracovani['typ_formulare']) {
            case 'vytvoreni_subjektu':
                $this->zpracovani_formulare_vytvoreni_subjektu();
                break;
            case 'nabidka':
                $this->zpracovani_formulare_trh_nabidka();
                break;
            case 'poptavka':
                $this->zpracovani_formulare_trh_poptavka();
                break;
            case 'vyroba':
                $this->zpracovani_formulare_vyroby();
                 break;
            case 'investice_do_lidskeho_kapitalu':
                $this->zpracovani_formulare_investice_do_lidskeho_kapitalu();
                break;
            case 'xls_vyhodnoceni_kola':
                $xls_vyhodnoceni_kola = new XLS_vyhodnoceni_kola($this->subjekt->get_id_subjektu());
                $xls_vyhodnoceni_kola->vygeneruj_soubor_pro_prohlizec();
                break;
            case 'volba_existujiciho_subjektu':
                $id_subjektu = $this->data_ke_zpracovani['id_subjektu'];
                $_SESSION['id_subjektu'] = $id_subjektu;
                $subjekt = Subjekt::vytvor_objekt_reprezentujici_subjekt($id_subjektu);
                $GLOBALS['subjekt'] = $subjekt;
                $ekonomika = new Ekonomika($subjekt->get_id_ekonomiky());
                $GLOBALS['ekonomika'] = $ekonomika;
                $GLOBALS['cislo_aktualniho_kola'] = $ekonomika->get_cislo_aktualniho_kola();
                $GLOBALS['integracni_celek'] = new Integracni_celek($ekonomika->get_id_integracniho_celku());
                break;
            case 'volba_jazyka':
                $dotaz_na_zmenu_jazyka = "UPDATE hraci SET id_jazyka = " . $this->data_ke_zpracovani['id_jazyka'] . " WHERE login_hrace = '" . $this->hrac->get_login_hrace() . "'; ";
                mysql_query($dotaz_na_zmenu_jazyka) or die($dotaz_na_zmenu_jazyka);
                break;
        }

        if ($this->data_ke_zpracovani['typ_formulare'] == 'nastaveni_hrace') {
            $this->zpracovani_formulare_nastaveni_hrace();
        }

        if ($this->hrac->get_superadministratorska_prava() == 1) {
            switch ($this->data_ke_zpracovani['typ_formulare']) {
                case 'vytvoreni_integracniho_celku':
                    $this->zpracovani_formulare_vytvoreni_integracniho_celku();
                    break;
                case 'vytvoreni_ekonomiky':
                    $this->zpracovani_formulare_vytvoreni_ekonomiky();
                    break;
                case 'vytvoreni_trhu':
                    $this->zpracovani_formulare_vytvoreni_trhu();
                    break;
                case 'editor_clanku':
                    $this->zpracovani_formulare_editoru();
                    break;
                case 'xls_export_dat':
                    $xls_export_dat = new XLS_export_dat($GLOBALS['integracni_celek']->get_id_integracniho_celku(),
                            $this->data_ke_zpracovani['list'], $this->data_ke_zpracovani['id_druhu_trhu']);
                    $xls_export_dat->vygeneruj_soubor_pro_prohlizec();
                    break;
                case 'nahrani_trznich_prikazu_ze_souboru':
                    $this->zpracovani_formulare_nahrani_trznich_prikazu_ze_souboru();
                    break;
                case 'pridani_druhu_dane':
                    $this->zpracovani_prikazu_pridani_druhu_dane();
                    break;
                case 'nastaveni_dane_pro_ekonomiku':
                    $this->zpracovani_formulare_nasteveni_dane_pro_ekonomiku();
                    break;
                case 'hodnotici_funkce':
                    $this->zpracovani_formulare_hodnotici_funkce();
                    break;
                case 'pridani_koncu_kol':
                    $this->zpracovani_formulare_pridani_koncu_kol();
                    break;
                case 'nastaveni_pocatecnich_hodnot':
                    $this->zpracovani_formulare_nastaveni_pocatecnich_hodnot();
                    break;
                case 'nastaveni_pocatecnich_cen':
                    $this->zpracovani_formulare_nastaveni_pocatecnich_cen();
                    break;
                case 'nastaveni_produkcnich_funkci':
                    $this->zpracovani_formulare_nastaveni_produkcnich_funkci();
                    break;
                case 'vytvoreni_meny':
                     $this->zpracovani_formulare_vytvoreni_meny();
                     break;
                default:
                    break;
            }
        }
    }

    private function zpracovani_formulare_zapojeni() {
        $emailova_adresa = Spravce_dat_z_formularu::zjisteni_emailu_ldap($_SESSION['login']);
        $login_hrace = $_SESSION['login'];
        hrac::zapis_hrace_do_databaze($login_hrace, sha1(rand(0, 1000000)), 1, $emailova_adresa);

        $hrac = new hrac($_SESSION['login']);
        $GLOBALS['hrac'] = $hrac;
        $_SESSION['auth'] = 1;
        $GLOBALS['hrac']->registruj_ovladac($GLOBALS['spravce_GUI']->generuj_ovladac_na_udalost('ZADOST_O_STRANKU'));
    }

    public static function zjisteni_emailu_ldap ($login_hrace) {
        $ds=ldap_connect("ldap.zcu.cz");

        if ($ds) {
            $r=ldap_bind($ds);
            $sr=ldap_search($ds, "ou=rfc2307, o=zcu, c=cz", "uid=". $login_hrace ."");
            $info = ldap_get_entries($ds, $sr);
            for ($i=0; $i<$info["count"]; $i++) {
                $emailova_adresa = $info[$i]["mail"][0];
            }
            ldap_close($ds);
        } else {
            die ("Nelze se připojit k LDAP serveru.");
        }
        return $emailova_adresa;
    }

    private function zpracovani_formulare_trh_poptavka() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $id_trhu = $data_ke_zpracovani['id_trhu'];
        
        $nova_poptavka = new individualni_poptavka($GLOBALS['cislo_aktualniho_kola'], $GLOBALS['subjekt']->get_id_subjektu(), $id_trhu);

        foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_ceny => $aktualni_hodnota_hledani_ceny) {
            if (strlen(trim($aktualni_hodnota_hledani_ceny)) == 0) {
                continue;
            }
            if (strstr($aktualni_klic_hledani_ceny, 'cena') != false) {
                preg_match("/\d*$/", $aktualni_klic_hledani_ceny, $cislo_aktualni_polozky_cena);
                foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_mnozstvi => $aktualni_hodnota_hledani_mnozstvi) {
                    preg_match("/\d*$/", $aktualni_klic_hledani_mnozstvi, $cislo_aktualni_polozky_mnozstvi);
                    if ((strstr($aktualni_klic_hledani_mnozstvi, 'mnozstvi') != false) && ($cislo_aktualni_polozky_cena[0] == $cislo_aktualni_polozky_mnozstvi[0])){
                        $nova_poptavka->pridej_parametr_poptavky(Konvertor_a_kontrolor_promennych::uprava_celociselne_polozky($aktualni_hodnota_hledani_ceny),
                            Konvertor_a_kontrolor_promennych::uprava_celociselne_polozky($aktualni_hodnota_hledani_mnozstvi));
                    }
                }
            }
        }

        $vysledek_overeni_platnosti_poptavky = $GLOBALS['subjekt']->over_platnost_poptavky($nova_poptavka);

        if (is_bool($vysledek_overeni_platnosti_poptavky) == true) {
            if ($vysledek_overeni_platnosti_poptavky == true) {
                $navratova_hodnota_zapisu = $nova_poptavka->zapis_prikaz_do_databaze();
                if ($navratova_hodnota_zapisu == true) {
                    $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare(null);
                } else {
                    $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('neznama_chyba');
                }
            } else {
                $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('prilis_velke_poptavane_mnozstvi');
            }
        } else {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare($vysledek_overeni_platnosti_poptavky);
        }
        
        $parametry = array();
        $parametry['hlaseni_o_ulozeni_dat_z_formulare'] = $hlaseni_o_ulozeni_dat;
        $parametry['id_subjektu'] = $this->subjekt->get_id_subjektu();
        $parametry['cislo_kola'] = $this->cislo_aktualniho_kola;

        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_POPTAVKY', $this, $parametry);
        if (isset ($data_ke_zpracovani['potvrdit_a_zobrazit']) == true) {
            header("Location: grafy/individualni_nabidka_a_poptavka.php?cislo_kola=" . $this->cislo_aktualniho_kola .
                    "&id_trhu=" . $id_trhu .
                    "&druh_prikazu=poptavka");
        }
    }

    private function zpracovani_formulare_trh_nabidka() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $id_trhu = $data_ke_zpracovani['id_trhu'];

        $nova_nabidka = new individualni_nabidka($GLOBALS['cislo_aktualniho_kola'], $GLOBALS['subjekt']->get_id_subjektu(), $id_trhu);
        foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_ceny => $aktualni_hodnota_hledani_ceny) {
            if (strlen(trim($aktualni_hodnota_hledani_ceny)) == 0){
                continue;
            }
            if (strstr($aktualni_klic_hledani_ceny, 'cena') != false) {
                preg_match("/\d*$/", $aktualni_klic_hledani_ceny, $cislo_aktualni_polozky_cena);
                foreach ($this->data_ke_zpracovani as $aktualni_klic_hledani_mnozstvi => $aktualni_hodnota_hledani_mnozstvi) {
                    preg_match("/\d*$/", $aktualni_klic_hledani_mnozstvi, $cislo_aktualni_polozky_mnozstvi);
                    if ((strstr($aktualni_klic_hledani_mnozstvi, 'mnozstvi') != false) && ($cislo_aktualni_polozky_mnozstvi[0] == $cislo_aktualni_polozky_cena[0])){
                        $nova_nabidka->pridej_parametr_nabidky(Konvertor_a_kontrolor_promennych::uprava_celociselne_polozky($aktualni_hodnota_hledani_ceny),
                            Konvertor_a_kontrolor_promennych::uprava_celociselne_polozky($aktualni_hodnota_hledani_mnozstvi));
                    }
                }
            }
        }
        
        $vysledek_overeni_platnosti_nabidky = $GLOBALS['subjekt']->over_platnost_nabidky($nova_nabidka);

        if (is_bool($vysledek_overeni_platnosti_nabidky) == true) {
            if ($vysledek_overeni_platnosti_nabidky == true) {
                $navratova_hodnota_zapisu = $nova_nabidka->zapis_prikaz_do_databaze();
                $parametry = array();
                if ($navratova_hodnota_zapisu == true) {
                    $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare(null);
                } else {
                    $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('neznama_chyba');
                }
            } else {
                $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('prilis_velke_nabizene_mnozstvi');
            }
        } else {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare($vysledek_overeni_platnosti_nabidky);
        }



        $parametry['hlaseni_o_ulozeni_dat_z_formulare'] = $hlaseni_o_ulozeni_dat;
        $parametry['id_subjektu'] = $this->subjekt->get_id_subjektu();
        $parametry['cislo_kola'] = $this->cislo_aktualniho_kola;
        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_NABIDKY', $this, $parametry);
        if (isset ($data_ke_zpracovani['potvrdit_a_zobrazit']) == true) {
            header("Location: grafy/individualni_nabidka_a_poptavka.php?cislo_kola=" . $this->cislo_aktualniho_kola .
                    "&id_trhu=" . $id_trhu .
                    "&druh_prikazu=nabidka");
        }
    }

    private function zpracovani_formulare_vyroby() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $relativni_podil_spotrebniho_zbozi = Konvertor_a_kontrolor_promennych::uprava_polozky_desetinne_cislo($data_ke_zpracovani['relativni_podil_spotrebni_zbozi']);

        if (($relativni_podil_spotrebniho_zbozi > 1) || ($relativni_podil_spotrebniho_zbozi < 0)) {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('relativni_pomer_mimo_rozsah');
        } else {
            $prikaz_vyroby_zbozi = new Prikaz_vyroby($this->cislo_aktualniho_kola, $this->subjekt->get_id_subjektu(), null, null,
                $relativni_podil_spotrebniho_zbozi, null,
                Konvertor_a_kontrolor_promennych::uprava_celociselne_polozky($data_ke_zpracovani['mnozstvi_vlastni_prace_ve_vyrobe']));
            $parametry = array();

            if ($GLOBALS['subjekt']->over_platnost_prikazu_vyroby($prikaz_vyroby_zbozi) == true) {
                if ($prikaz_vyroby_zbozi->uloz_prikaz_do_databaze() == true) {
                    $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare(null);
                } else {
                    $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('neznama_chyba');
                }
            } else {
                $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('prilis_velke_mnozstvi_prace_ve_vyrobe');
            }
        }

        $parametry['hlaseni_o_ulozeni_dat_z_formulare'] = $hlaseni_o_ulozeni_dat;
        $parametry['id_subjektu'] = $this->subjekt->get_id_subjektu();
        $parametry['cislo_kola'] = $this->cislo_aktualniho_kola;
        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_VYROBA', $this, $parametry);
    }

    private function zpracovani_formulare_investice_do_lidskeho_kapitalu() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $investovany_cas = Konvertor_a_kontrolor_promennych::uprava_celociselne_polozky($data_ke_zpracovani['investovany_cas']);
        $prikaz_investice_do_lidskeho_kapitalu = new Prikaz_investice_do_lidskeho_kapitalu($this->cislo_aktualniho_kola, $this->subjekt->get_id_subjektu(), null, 0, $data_ke_zpracovani['investovany_cas']);
        if ($this->subjekt->over_platnost_prikazu_investice_do_lidskeho_kapitalu($prikaz_investice_do_lidskeho_kapitalu) == true) {
            $prikaz_investice_do_lidskeho_kapitalu->uloz_prikaz_do_databaze();
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare(null);
        } else {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('prilis_velke_mnozstvi_investovaneho_casu');
        }

        $parametry['hlaseni_o_ulozeni_dat_z_formulare'] = $hlaseni_o_ulozeni_dat;
        $parametry['id_subjektu'] = $this->subjekt->get_id_subjektu();
        $parametry['cislo_kola'] = $this->cislo_aktualniho_kola;
        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_VYROBA', $this, $parametry);
    }

    private function zpracovani_formulare_vytvoreni_integracniho_celku() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $id_integracniho_celku = $data_ke_zpracovani['id_integracniho_celku'];
        $povolena_migrace = Konvertor_a_kontrolor_promennych::konverze_checkboxu($data_ke_zpracovani['povolena_migrace']);
        $automaticky_vyvazeny_menovy_kurz = Konvertor_a_kontrolor_promennych::konverze_checkboxu($data_ke_zpracovani['automaticky_vyvazeny_menovy_kurz']);
        $popis = $data_ke_zpracovani['popis'];

        $dotaz_na_vytvoreni_integracniho_celku = "INSERT INTO integracni_celky ".
            "(id_integracniho_celku, povolena_migrace, popis) " .
            " VALUES ( '" . $id_integracniho_celku . "', " .
            $povolena_migrace . ", '" . $popis . "');";
        mysql_query($dotaz_na_vytvoreni_integracniho_celku) or die($dotaz_na_vytvoreni_integracniho_celku);
    }

    private function zpracovani_formulare_vytvoreni_ekonomiky() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $id_ekonomiky = $data_ke_zpracovani['id_ekonomiky'];
        $pristupovy_kod = $data_ke_zpracovani['pristupovy_kod'];
        $id_integracniho_celku = $data_ke_zpracovani['id_integracniho_celku'];
        $nazev_meny = $data_ke_zpracovani['nazev_meny'];
        $vychozi_id_druhu_subjektu = $data_ke_zpracovani['vychozi_id_druhu_subjektu'];
        $popis = $data_ke_zpracovani['popis'];

        $dotaz_na_vytvoreni_ekonomiky = "INSERT INTO ekonomiky " .
            "(id_ekonomiky, pristupovy_kod, id_integracniho_celku, id_meny, vychozi_id_druhu_subjektu, popis) VALUES " .
            "( '" . $id_ekonomiky . "', '" . $pristupovy_kod . "', '" . $id_integracniho_celku . "', '" .
            $nazev_meny . "','" . $vychozi_id_druhu_subjektu . "','" . $popis . "');" ;
        mysql_query($dotaz_na_vytvoreni_ekonomiky) or die($dotaz_na_vytvoreni_ekonomiky);
    }

    private function zpracovani_formulare_vytvoreni_subjektu() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $id_ekonomiky = $data_ke_zpracovani['id_ekonomiky'];
        if (isset ($data_ke_zpracovani['id_subjektu']) == true) {
            $id_subjektu = $data_ke_zpracovani['id_subjektu'];
        } else {
            $id_subjektu = $GLOBALS['hrac']->get_login();
        }
        if (strlen($id_subjektu) == 0) {
            return;
        }
        $login_hrace = $GLOBALS['hrac']->get_login();
        $pristupovy_kod = $data_ke_zpracovani['pristupovy_kod'];

        $dotaz_na_ekonomiku = "SELECT * FROM ekonomiky WHERE id_ekonomiky = '" . $id_ekonomiky . "'; ";
        $vysledek_ekonomika = mysql_query($dotaz_na_ekonomiku) or die($dotaz_na_ekonomiku);
        if (mysql_num_rows($vysledek_ekonomika) == 0) {
            echo "Nesprávný název ekonomiky";
            exit;
        }
        $radek_ekonomika = mysql_fetch_assoc($vysledek_ekonomika);
        $vychozi_id_druhu_subjektu = $radek_ekonomika['vychozi_id_druhu_subjektu'];
        $id_integracniho_celku = $radek_ekonomika ['id_integracniho_celku'];

        if ($radek_ekonomika['pristupovy_kod'] == $pristupovy_kod) {
            if (isset ($data_ke_zpracovani['id_druhu_subjektu']) == true) {
                $id_druhu_subjektu = $data_ke_zpracovani['id_druhu_subjektu'];
            } else {
                $id_druhu_subjektu = $vychozi_id_druhu_subjektu;
            }
            $_SESSION['id_subjektu'] = $id_subjektu;
            Subjekt::zapis_subjekt_do_databaze($id_subjektu, $id_druhu_subjektu, $id_ekonomiky, $login_hrace);
            $subjekt = Subjekt::vytvor_objekt_reprezentujici_subjekt($id_subjektu);
            $GLOBALS['subjekt'] = $subjekt;
            $subjekt->zapis_pocatecni_hodnoty();

            $ekonomika = new Ekonomika($subjekt->get_id_ekonomiky());
            $GLOBALS['ekonomika'] = $ekonomika;

            $GLOBALS['cislo_aktualniho_kola'] = $ekonomika->get_cislo_aktualniho_kola();
            $GLOBALS['integracni_celek'] = new Integracni_celek($ekonomika->get_id_integracniho_celku());
        } else {
            echo "Nesprávný přístupový kód.";
        }
    }

    private function zpracovani_formulare_vytvoreni_trhu() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;

        $id_druhu_trhu = $data_ke_zpracovani['id_druhu_trhu'];
        $id_meny = $data_ke_zpracovani['id_meny'];
        $nazev_trhu = $data_ke_zpracovani['nazev_trhu'];
        $id_integracniho_celku = $data_ke_zpracovani['id_integracniho_celku'];

        $dotaz_na_vytvoreni_trhu = "INSERT INTO trhy (id_druhu_trhu, id_meny, nazev_trhu, id_integracniho_celku) 
            VALUES ( '" . $id_druhu_trhu . "', '" .
            $id_meny . "', '" . $nazev_trhu . "', '"  . $id_integracniho_celku . "'); ";
        mysql_query($dotaz_na_vytvoreni_trhu) or die($dotaz_na_vytvoreni_trhu);

        $dotaz_na_pridelene_id_trhu = "SELECT LAST_INSERT_ID(); ";
        $vysledek_pridelene_id_trhu = mysql_query($dotaz_na_pridelene_id_trhu) or die($dotaz_na_pridelene_id_trhu);
        $radek_pridelene_id_trhu = mysql_fetch_row($vysledek_pridelene_id_trhu);
        $id_trhu = $radek_pridelene_id_trhu[0];

        if ($id_druhu_trhu == 'trh_uveru_a_uspor' || $id_druhu_trhu == 'trh_uveru'|| $id_druhu_trhu == 'trh_uspor') {
            $dotaz_na_vlozeni_puvodu_toku = "INSERT INTO puvody_toku (popis_puvodu, id_trhu) VALUES ('nakup', " . $id_trhu . "), " .
                "('prodej', " . $id_trhu . "), ('prijate_uroky', " . $id_trhu . "), ('prijate_jistiny', " . $id_trhu . ")," .
                "('vyplacene_uroky', " . $id_trhu . "), ('vyplacene_jistiny', " . $id_trhu . ");";
        } else if ($id_druhu_trhu == 'trh_obligaci')  {
            $dotaz_na_vlozeni_puvodu_toku = "INSERT INTO puvody_toku (popis_puvodu, id_trhu) VALUES ('nakup', " . $id_trhu . "), " .
                "('prodej', " . $id_trhu . "), ('emise_obligaci', " . $id_trhu . "); ";
        } else {
            $dotaz_na_vlozeni_puvodu_toku = "INSERT INTO puvody_toku (popis_puvodu, id_trhu) VALUES ('nakup', " . $id_trhu . "), " .
                "('prodej', " . $id_trhu . ");";
        }
        mysql_query($dotaz_na_vlozeni_puvodu_toku) or die($dotaz_na_vlozeni_puvodu_toku);

        $prava_poptavat = $data_ke_zpracovani['prava_poptavat'];
        $dotaz_na_vlozeni_prav_poptavat = "INSERT INTO prava_poptavat (id_trhu, id_druhu_subjektu, puvod_hrace_id_ekonomiky) VALUES ";
        $pocet_polozek_prav_poptavat = sizeof($prava_poptavat);
        $cislo_aktualniho_prava = 0;
        foreach ($prava_poptavat as $aktualni_pravo_poptavat) {
            $pole_aktualni_pravo_poptavat = explode(";", $aktualni_pravo_poptavat);
            $dotaz_na_vlozeni_prav_poptavat .= "( " . $id_trhu . ", '" . $pole_aktualni_pravo_poptavat[0] . "', '" . $pole_aktualni_pravo_poptavat[1] . "')";
            if ($cislo_aktualniho_prava < ($pocet_polozek_prav_poptavat - 1)) {
                $dotaz_na_vlozeni_prav_poptavat .= ", ";
            } else {
                $dotaz_na_vlozeni_prav_poptavat .= "; ";
            }
            $cislo_aktualniho_prava++;
        }
        mysql_query($dotaz_na_vlozeni_prav_poptavat) or die($dotaz_na_vlozeni_prav_poptavat);

        $prava_nabizet = $data_ke_zpracovani['prava_nabizet'];
        $dotaz_na_vlozeni_prav_nabizet = "INSERT INTO prava_nabizet (id_trhu, id_druhu_subjektu, puvod_hrace_id_ekonomiky) VALUES ";
        $pocet_polozek_prav_nabizet = sizeof($prava_nabizet);
        $cislo_aktualniho_prava = 0;
        foreach ($prava_nabizet as $aktualni_pravo_nabizet) {
            $pole_aktualni_pravo_nabizet = explode(";", $aktualni_pravo_nabizet);
            $dotaz_na_vlozeni_prav_nabizet .= "( " . $id_trhu . ", '" . $pole_aktualni_pravo_nabizet[0] . "', '" . $pole_aktualni_pravo_nabizet[1] . "')";
            if ($cislo_aktualniho_prava < ($pocet_polozek_prav_nabizet - 1)) {
                $dotaz_na_vlozeni_prav_nabizet .= ", ";
            } else {
                $dotaz_na_vlozeni_prav_nabizet .= "; ";
            }
            $cislo_aktualniho_prava++;
        }
        mysql_query($dotaz_na_vlozeni_prav_nabizet) or die($dotaz_na_vlozeni_prav_nabizet);

        $dotaz_na_polozky_nastaveni_u_trhu = "SELECT * FROM polozky_nastaveni_u_trhu WHERE id_druhu_trhu = '" . $id_druhu_trhu . "'; ";
        $vysledek_polozky_nastaveni_u_trhu = mysql_query($dotaz_na_polozky_nastaveni_u_trhu) or die($dotaz_na_polozky_nastaveni_u_trhu);
        while ($radek_polozka_nastaveni = mysql_fetch_assoc($vysledek_polozky_nastaveni_u_trhu)) {
            if (isset ($data_ke_zpracovani[$radek_polozka_nastaveni['id_polozky_nastaveni']]) == true) {
                $dotaz_na_vlozeni_polozky_nastaveni = "INSERT INTO nastaveni_trhu (id_trhu, id_polozky_nastaveni, hodnota) VALUES (" .
                    $id_trhu . ", '" . $radek_polozka_nastaveni['id_polozky_nastaveni'] . "', '" . $data_ke_zpracovani[$radek_polozka_nastaveni['id_polozky_nastaveni']] . "'); ";
                mysql_query($dotaz_na_vlozeni_polozky_nastaveni) or die($dotaz_na_vlozeni_polozky_nastaveni);
            }
        }
    }

    private function zpracovani_formulare_editoru() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $allowedTags='<p><strong><em><u><h1><h2><h3><h4><h5><h6><img>';
        $allowedTags.='<li><ol><ul><span><div><br><ins><del>';
        $clanek = strip_tags(stripslashes($data_ke_zpracovani['clanek']),$allowedTags);
        $id_ekonomiky = $data_ke_zpracovani['id_ekonomiky'];

        $dotaz_na_vlozeni_clanku = "REPLACE INTO clanky (id_ekonomiky, clanek) VALUES ('" . $id_ekonomiky . "', '" . $clanek . "'); ";
        mysql_query($dotaz_na_vlozeni_clanku) or die($dotaz_na_vlozeni_clanku);
    }


    private function zpracovani_formulare_registrace() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $login_hrace = $data_ke_zpracovani['login_hrace'];
        $heslo = sha1($data_ke_zpracovani['heslo']);
        $heslo_2 = sha1($data_ke_zpracovani['heslo_2']);
        $id_jazyka = $data_ke_zpracovani['id_jazyka'];
        $emailova_adresa = Konvertor_a_kontrolor_promennych::kontrola_emailove_adresy($data_ke_zpracovani['emailova_adresa']);
        $existence_loginu_ldap = $this->existence_loginu_ldap($login_hrace);
        $platna = true;
        $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare();
        $data_z_formulare_registrace = array();
        if ($heslo != $heslo_2) {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('shoda_hesla');
            $data_z_formulare_registrace['login_hrace'] = $login_hrace;
            $data_z_formulare_registrace['emailova_adresa'] = $emailova_adresa;
            $platna = false;
        }
        if ($existence_loginu_ldap == true) {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('login_pouzivan');
            $data_z_formulare_registrace['emailova_adresa'] = $emailova_adresa;
            $platna = false;
        }
        if ($_SESSION['CAPTCHAString'] != $_POST['captchastring']) {
            $data_z_formulare_registrace['login_hrace'] = $login_hrace;
            $data_z_formulare_registrace['emailova_adresa'] = $emailova_adresa;
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('spatne_opsany_obrazek');
            $platna = false;
        }
        $dotaz_na_existenci_loginu_v_databazi = "SELECT * FROM hraci WHERE login_hrace = '" . $login_hrace . "'; ";
        $vysledek_existence_loginu = mysql_query($dotaz_na_existenci_loginu_v_databazi) or die($dotaz_na_existenci_loginu_v_databazi);
        if (mysql_num_rows($vysledek_existence_loginu) > 0) {
            $hlaseni_o_ulozeni_dat = new Hlaseni_o_ulozeni_dat_z_formulare('login_pouzivan');
            $platna = false;
        }

        if ($platna == true) {
            $data_z_formulare_registrace['login_hrace'] = $login_hrace;
            $data_z_formulare_registrace['emailova_adresa'] = $emailova_adresa;
            $id_jazyka = $data_ke_zpracovani['id_jazyka'];
            hrac::zapis_hrace_do_databaze($login_hrace, $heslo, $id_jazyka, $emailova_adresa);
            if ($vysledek_vlozeni_hrace != false) {
                session_start();
                $_SESSION['login'] = $login_hrace;
                $hrac = new hrac($_SESSION['login']);
                $GLOBALS['hrac'] = $hrac;
                $_SESSION['auth'] = 1;
                $GLOBALS['hrac']->registruj_ovladac($GLOBALS['spravce_GUI']->generuj_ovladac_na_udalost('ZADOST_O_STRANKU'));
            }
        }

        $parametry = array();
        $data_z_formulare_registrace['platna'] = $platna;
        $parametry['hlaseni_o_ulozeni_dat_z_formulare'] = $hlaseni_o_ulozeni_dat;
        $parametry['data_z_formulare_registrace'] = $data_z_formulare_registrace;
        $this->fronta_udalosti->vyvolani_udalosti_ve_fronte('ODESLANI_FORMULARE_REGISTRACE', $this, $parametry);

    }

    function existence_loginu_ldap ($login_hrace) {
        $ds=ldap_connect("ldap.zcu.cz");  // must be a valid LDAP server!
        if ($ds) {
            $r=ldap_bind($ds);     // this is an "anonymous" bind, typically
            $sr=ldap_search($ds, "ou=rfc2307, o=zcu, c=cz", "uid=". $login_hrace ."");
            $info = ldap_get_entries($ds, $sr);
            ldap_close($ds);
            if ($info["count"] > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            die ("Nelze se připojit k LDAP serveru.");
        }
    }

    /**
     * 0 - typ příkazu
     * 1 - číslo kola
     * 2 - id subjektu
     * 3 - id trhu
     * 4 - měna ekonomiky
     * 5 - mezní cena
     * 6 - množství ...
     */
    private function zpracovani_formulare_nahrani_trznich_prikazu_ze_souboru() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $nazev_souboru = $data_ke_zpracovani['nazev_souboru'];
        $handle = fopen("csv/" . $nazev_souboru, "r");
        while ($radek_csv = fgetcsv($handle, 1000000, ";")) {
            $pocet_sloupcu = count($radek_csv);
            if ($radek_csv[0] == 'nabidka') {
                $nabidka = new individualni_nabidka($radek_csv[1], $radek_csv[2], $radek_csv[3], $radek_csv[4]);
                for ($i = 5; $i < $pocet_sloupcu; $i += 2) {
                    $nabidka->pridej_parametr_nabidky($radek_csv[$i], $radek_csv[$i + 1]);
                }
                $nabidka->zapis_prikaz_do_databaze();
                unset ($nabidka);
            } else if ($radek_csv[0] == 'poptavka') {
                $poptavka = new individualni_poptavka($radek_csv[1], $radek_csv[2], $radek_csv[3], $radek_csv[4]);
                for ($i = 5; $i < $pocet_sloupcu; $i += 2) {
                    $poptavka->pridej_parametr_poptavky($radek_csv[$i], $radek_csv[$i + 1]);
                }
                $poptavka->zapis_prikaz_do_databaze();
                unset ($poptavka);
            }
        }
    }

    /**
     *
     */
    private function zpracovani_formulare_pridani_hracu_a_subjektu_ze_souboru() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $nazev_souboru = $data_ke_zpracovani['nazev_souboru'];
        $handle = fopen("csv/" . $nazev_souboru, "r");
        while ($radek_csv = fgetcsv($handle, 1000000, ";")) {
            $login_hrace = $radek_csv[0];
            $id_ekonomiky = $radek_csv[1];
            $id_druhu_subjektu = $radek_csv[2];
            $id_subjektu = $radek_csv[3];
            $emailova_adresa = Spravce_dat_z_formularu::zjisteni_emailu_ldap($login_hrace);
            hrac::zapis_hrace_do_databaze($login_hrace, null, 1, $emailova_adresa);
            Subjekt::zapis_subjekt_do_databaze($id_subjektu, $id_druhu_subjektu, $id_ekonomiky, $login_hrace);
        }
    }

    private function zpracovani_prikazu_pridani_druhu_dane() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_pridani_druhu_dane = "REPLACE INTO druhy_dani_a_transferu (id_druhu_dane, id_druhu_subjektu, transfer) VALUES ('" .
            $data_ke_zpracovani['id_druhu_dane'] . "', '" . $data_ke_zpracovani['id_druhu_subjektu'] . "', " .
            Konvertor_a_kontrolor_promennych::konverze_checkboxu($data_ke_zpracovani['transfer']) . ");";
        mysql_query($dotaz_na_pridani_druhu_dane) or die($dotaz_na_pridani_druhu_dane);

        $dotaz_na_pridani_druhu_popisu_puvodu_toku = "REPLACE INTO  druhy_popisu_puvodu_toku VALUES ('" . $data_ke_zpracovani['id_druhu_dane'] ."', 1) ;";
        mysql_query($dotaz_na_pridani_druhu_popisu_puvodu_toku) or die($dotaz_na_pridani_druhu_popisu_puvodu_toku);

    }

    private function zpracovani_formulare_nasteveni_dane_pro_ekonomiku() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_pridani_puvodu_toku = "REPLACE INTO puvody_toku (popis_puvodu, id_ekonomiky) VALUES ( '" .
            $data_ke_zpracovani['id_druhu_dane'] . "', '" . $data_ke_zpracovani['id_ekonomiky'] . "'); ";
        mysql_query($dotaz_na_pridani_puvodu_toku) or die($dotaz_na_pridani_puvodu_toku);

        $dotaz_na_last_id = "SELECT LAST_INSERT_ID() AS id_puvodu_toku; ";
        $vysledek_last_id = mysql_query($dotaz_na_last_id) or die($dotaz_na_last_id);
        $radek_last_id = mysql_fetch_assoc($vysledek_last_id);
        $id_puvodu_toku = $radek_last_id['id_puvodu_toku'];

        $dotaz_na_nastaveni_dane = "REPLACE INTO dane_a_transfery VALUES( '" . $data_ke_zpracovani['id_ekonomiky'] . "', '" .
            $data_ke_zpracovani['id_druhu_dane'] . "', '" . $data_ke_zpracovani['rovnice_dane'] . "', " . $id_puvodu_toku . ") ;";
        mysql_query($dotaz_na_nastaveni_dane) or die($dotaz_na_nastaveni_dane);
    }

    private function zpracovani_formulare_hodnotici_funkce() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_ulozeni_hodnotici_funkce = "REPLACE INTO hodnotici_funkce (id_ekonomiky, id_druhu_subjektu, hodnotici_funkce) VALUES ( '" .
            $data_ke_zpracovani['id_ekonomiky'] . "', '" . $data_ke_zpracovani['id_druhu_subjektu'] . "', '" .
            $data_ke_zpracovani['hodnotici_funkce'] . "'); ";
        mysql_query($dotaz_na_ulozeni_hodnotici_funkce) or die($dotaz_na_ulozeni_hodnotici_funkce);
    }

    private function zpracovani_formulare_pridani_koncu_kol() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        if ($data_ke_zpracovani['den_v_tydnu'] == '*') {
            $pole_dny_v_tydnu = array ('Mon', 'Tue', 'Thu', 'Fri', 'Sat', 'Sun');
        } else {
            $pole_dny_v_tydnu = array ($data_ke_zpracovani['den_v_tydnu']);
        }
        $hodina = Konvertor_a_kontrolor_promennych::odstraneni_mezer($data_ke_zpracovani['hodina']);
        $pole_hodiny = $this->rozdel_pole_casu($data_ke_zpracovani['hodina'], 23);
        $pole_minuty = $this->rozdel_pole_casu($data_ke_zpracovani['minuta'], 59);

        foreach ($pole_dny_v_tydnu as $den_v_tydnu) {
            foreach ($pole_hodiny as $hodina) {
                foreach ($pole_minuty as $minuta) {
                    $cas_konce_kola = new Cas_konce_kola($data_ke_zpracovani['id_integracniho_celku'], $den_v_tydnu, $hodina, $minuta);
                    $cas_konce_kola->zapis_cas_konce_kola_do_databaze();
                }
            }
        }
    }

    private function rozdel_pole_casu($vstupni_data, $limit_for) {
        $vystupni_pole = array();
        $krok = 1;
        if (strpos($vstupni_data, '*') === false) {
            if (strpos($vstupni_data, ',') === false) {
                if (is_numeric($vstupni_data) == true) {
                    $vystupni_pole[] = $vstupni_data;
                } else {
                    return;
                }
            } else {
                $rozdeleni = explode(',', $vstupni_data);
                foreach ($rozdeleni as $aktualni_polozka) {
                    $vystupni_pole[] = $aktualni_polozka;
                }
            }
        } else {
            for ($i = 0; $i <= $limit_for; $i += $krok) {
                $vystupni_pole[] = $i;
            }
        }
        return $vystupni_pole;
    }

    private function zpracovani_formulare_nastaveni_pocatecnich_hodnot() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_ulozeni_pocatecnich_hodnot = "REPLACE INTO pocatecni_hodnoty VALUES ( '" . $data_ke_zpracovani['id_ekonomiky'] . "', '" .
            $data_ke_zpracovani['id_druhu_subjektu'] . "', '" . $data_ke_zpracovani['id_komodity'] . "', " . $data_ke_zpracovani['pocatecni_hodnota'] . "); ";
        mysql_query($dotaz_na_ulozeni_pocatecnich_hodnot) or die($dotaz_na_ulozeni_pocatecnich_hodnot);
    }

    private function zpracovani_formulare_nastaveni_pocatecnich_cen() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_zapsani_ceny = "REPLACE INTO vyvoj_trznich_cen (id_trhu, cislo_kola, trzni_cena) VALUES (" . $data_ke_zpracovani['id_trhu'] . ", 0, " .
            $data_ke_zpracovani['trzni_cena'] . ") ;";
        mysql_query($dotaz_na_zapsani_ceny) or die($dotaz_na_zapsani_ceny);
    }

    private function zpracovani_formulare_nastaveni_produkcnich_funkci() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_nastaveni_produkcnich_funkci = "UPDATE ekonomiky SET produkcni_funkce_spotrebni_zbozi = '" . $data_ke_zpracovani['produkcni_funkce_spotrebni_zbozi'] . "'
            , produkcni_funkce_kapitalove_zbozi = '" . $data_ke_zpracovani['produkcni_funkce_kapitalove_zbozi'] . "' , produkcni_funkce_lidsky_kapital = '" .
            $data_ke_zpracovani['produkcni_funkce_lidsky_kapital'] . "' WHERE id_ekonomiky = '" . $data_ke_zpracovani['id_ekonomiky'] . "'; ";
        mysql_query($dotaz_na_nastaveni_produkcnich_funkci) or die($dotaz_na_nastaveni_produkcnich_funkci);
    }

    private function zpracovani_formulare_vytvoreni_meny() {
        $data_ke_zpracovani = $this->data_ke_zpracovani;
        $dotaz_na_vytvoreni_meny = "INSERT INTO meny VALUES ('" . $data_ke_zpracovani['id_meny'] . "', '" . $data_ke_zpracovani['nazev_meny'] . "') ;";
        mysql_query($dotaz_na_vytvoreni_meny) or die($dotaz_na_vytvoreni_meny);
    }

}
?>
