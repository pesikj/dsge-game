<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_zaznamu_o_aktivite
 *
 * @author pike
 */
class Spravce_zaznamu_o_aktivite extends Trida_reagujici_na_udalosti {
    //put your code here

    private $hrac;
    private $cislo_aktualniho_kola;

    public function __construct() {
        $this->pole_identifikatoru_udalosti['ODESLANI_FORMULARE_POPTAVKY'] = 'zaznamenej_aktivitu_formular_poptavky';
        parent::__construct();

        $this->hrac = $GLOBALS['hrac'];

        $spravce_konfigurace = new spravce_konfigurace();
        $this->cislo_aktualniho_kola = $spravce_konfigurace->get_cislo_aktualniho_kola();
    }

    public function zaznamenej_aktivitu_formular_poptavky ($odesilate, $parametry) {
        $dotaz_na_zjisteni_existence_zaznamu_o_aktivite = "SELECT * FROM aktivita_hracu WHERE " .
            "login ='" . $this->hrac->getLogin() . "' AND kolo = " . $this->cislo_aktualniho_kola . "; ";

        $vysledek_existence_zaznamu_o_aktivite = mysql_query($dotaz_na_zjisteni_existence_zaznamu_o_aktivite)
            or die ($dotaz_na_zjisteni_existence_zaznamu_o_aktivite);
        if (mysql_num_rows($vysledek_existence_zaznamu_o_aktivite) == 0) {
            $dotaz_na_zaznam_o_aktivite = "INSERT INTO aktivita_hracu VALUES ( '" . $this->hrac->getLogin() . "', " .
                $this->cislo_aktualniho_kola . ", 1, CURRENT_TIMESTAMP );";
            mysql_query($dotaz_na_zaznam_o_aktivite) or die ($dotaz_na_zaznam_o_aktivite);
        }
    }

}
?>
