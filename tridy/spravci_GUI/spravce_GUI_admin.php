<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of spravce_GUI_admin_stav_na_trhu
 *
 * @author pike
 */
class spravce_GUI_admin_stav_na_trhu {
    //put your code here
    
    private $default_data;
    private $cislo_aktualniho_kola;

    function __construct($cislo_aktualniho_kola) {
        $default_data = array('hodin_prace' => 0, 'druh_zbozi' => 1);
        $pole_chyb = array();

        require_once('tridy/kolo.php');
        require_once('formulare/formular_trideni_aktualnich_informaci.php');

        $default_data = array ('zobrazovana_informace' => 'trh_spotrebniho_zbozi', 'vybrane_kolo' => $cislo_aktualniho_kola);

        if (isset ($_GET['zobrazovana_informace'])) {
            $default_data['zobrazovana_informace'] = $_GET['zobrazovana_informace'];
        }

        if (isset ($_GET['vybrane_kolo'])) {
            $default_data['vybrane_kolo'] = $_GET['vybrane_kolo'];
        }

        $formular_trideni_aktualnich_informaci = new formular_trideni_aktualnich_informaci();
        $formular_trideni_aktualnich_informaci->generuj_formular_aktualnich_informaci($default_data);


        $zobrazovana_informace = $default_data['zobrazovana_informace'];
        $vybrane_kolo = $default_data['vybrane_kolo'];

        $kolo = new kolo($vybrane_kolo);
        $kolo->zjisti_konkretni_aktualni_informaci($zobrazovana_informace);

        $this->default_data = $default_data;
        $this->cislo_aktualniho_kola = $cislo_aktualniho_kola;
    }

}
?>
