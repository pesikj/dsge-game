<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_administratora
 *
 * @author pike
 */
class Formular_administratora extends Formular{
    //put your code here

    public static $pole_formularu_k_dispozici = array('vytvoreni_integracniho_celku', 'vytvoreni_ekonomiky', 'vytvoreni_trhu', 'nastaveni_dani',
                                                        'nahrani_prikazu_ze_souboru', 'export_dat', 'hodnotici_funkce', 'pridani_koncu_kol',
                                                        'nastaveni_pocatecnich_hodnot', 'nastaveni_produkcnich_funkci', 'vytvoreni_meny');
    public static $prefix_stranek_formular_administratora = "administrace";

    public function  __construct() {
        parent::__construct();
    }

    public static function get_objekt_zadaneho_formulare($id_formulare) {
        switch($id_formulare) {
            case "vytvoreni_integracniho_celku":
                $zadany_formular = new Formular_vytvoreni_integracniho_celku();
                break;
            case "vytvoreni_ekonomiky":
                $zadany_formular = new Formular_vytvoreni_ekonomiky();
                break;
            case "vytvoreni_trhu":
                $zadany_formular = new Formular_vytvoreni_trhu();
                break;
            case "nastaveni_dani":
                $zadany_formular = new Formular_nastaveni_dani();
                break;
            case "nahrani_prikazu_ze_souboru":
                $zadany_formular = new Formular_nahrani_prikazu_ze_souboru();
                break;
            case "export_dat":
                $zadany_formular = new Formular_export_dat();
                break;
            case "hodnotici_funkce":
                $zadany_formular = new Formular_hodnoticich_funkci();
                break;
            case "pridani_koncu_kol":
                $zadany_formular = new Formular_pridani_koncu_kol();
                break;
            case 'nastaveni_pocatecnich_hodnot':
                $zadany_formular = new Formular_nastaveni_pocatecnich_hodnot();
                break;
            case 'nastaveni_produkcnich_funkci':
                $zadany_formular = new Formular_nastaveni_produkcnich_funkci();
                break;
            case 'vytvoreni_meny':
                $zadany_formular = new Formular_vytvoreni_meny();
                break;
            default:
                $zadany_formular = new Formular_administratora();
                break;
        }

        return $zadany_formular;
    }

    public function vygeneruj_a_vloz_formular() {
        ;
    }


    protected function vygeneruj_tabulku_druhu_subjektu_a_prav_obchodovani_na_trhu() {
        $prekladac = $this->prekladac;

        $tabulka = "<table border=\"0\" style=\"width: 100%\">";
        $tabulka .= "<tr>";
            $tabulka .= "<th>" . $prekladac->vloz_retezec("id_druhu_subjektu") . "</th>";
            $tabulka .= "<th>" . $prekladac->vloz_retezec("pravo_poptavat") . "</th>";
            $tabulka .= "<th>" . $prekladac->vloz_retezec("pravo_nabizet") . "</th>";
        $tabulka .= "</tr>";
        foreach ($this->informace_o_ekonomikach->get_pole_druhu_subjektu() as $aktualni_druh_subjektu) {
            foreach ($this->informace_o_ekonomikach->get_pole_id_ekonomik() as $aktualni_id_ekonomiky => $aktualni_id_integracniho_celku) {
                $tabulka .= "<tr>";
                $value_checkboxu = $aktualni_druh_subjektu . ";" . $aktualni_id_ekonomiky;
                $tabulka .= "<td>" . $prekladac->vloz_retezec("druhy_subjektu_" . $aktualni_druh_subjektu) . " (" . $aktualni_id_ekonomiky . ")" . "</td>";
                $tabulka .= "<td style=\"text-align:center;\"><input type=\"checkbox\" value=\"" . $value_checkboxu . "\" name=\"prava_poptavat[]\" /></td>";
                $tabulka .= "<td style=\"text-align:center;\"><input type=\"checkbox\" value=\"" . $value_checkboxu . "\" name=\"prava_nabizet[]\" /></td>";
                $tabulka .= "</tr>";
            }
        }
        $tabulka .= "</table>";
        return $tabulka;
    }
}
?>
