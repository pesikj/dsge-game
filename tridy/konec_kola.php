<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of konec_kola
 *
 * @author pike
 */
class konec_kola {
    //put your code here

    function __construct($kolo) {
        require_once 'trh.php';
        require_once 'inc/mysql_connect.php';
        require_once 'spravce_uveru.php';
        require_once 'inc/config.php';
        require_once 'spravce_produkce.php';

        $this->kolo = $kolo->get_cislo_kola();
        $this->odkaz_na_ridici_instanci_kolo = $kolo;
    }

    private $kolo;
    private $odkaz_na_ridici_instanci_kolo;


    /**
     * Dodělat funkci!
     */
    function proved_transakce_na_vsech_trzich() {
        echo "Výpočet konce kola začal v " . date("H:i:s");

        $tabulka_trhu = array();

        $trh_spotrebniho_zbozi = new trh("trh_spotrebniho_zbozi", $this->kolo);
        $trh_kapitaloveho_zbozi = new trh("trh_kapitaloveho_zbozi", $this->kolo);
        $trh_prace = new trh("trh_prace", $this->kolo);
        $trh_kapitalu_2_obdobi = new trh("trh_kapitalu_2_obdobi", $this->kolo);

        $tabulka_trhu[] = $trh_spotrebniho_zbozi;
        $tabulka_trhu[] = $trh_kapitaloveho_zbozi;
        $tabulka_trhu[] = $trh_prace;
        $tabulka_trhu[] = $trh_kapitalu_2_obdobi;

        foreach ($tabulka_trhu as $trh) {
            $trh->zobchoduj_polozky_na_trhu();
        }

        $spravce_uveru = new spravce_uveru("trh_kapitalu_2_obdobi", $this->kolo);
        $spravce_uveru->zuctuj_existujici_uvery();

        $spravce_produkce = new spravce_produkce($this->kolo);
        $spravce_produkce->pripis_vysledky_produkce_vyrobcum();

        $dotaz_na_zaznam_trznich_cen = "INSERT INTO vyvoj_trznich_cen VALUES (" .
            $this->kolo . ", " .  $trh_spotrebniho_zbozi->getTrzni_cena() . ", " .
            $trh_kapitaloveho_zbozi->getTrzni_cena() . ", " . $trh_prace->getTrzni_cena() . ", " .
            $trh_kapitalu_2_obdobi->getTrzni_cena() . ");";
        mysql_query($dotaz_na_zaznam_trznich_cen);

        $dotaz_na_uzavreni_kola_v_databazi = "UPDATE statistika_kola SET " .
            " datum_a_cas_konce = CURRENT_TIMESTAMP WHERE kolo = " . $this->kolo . "; ";

        $dotaz_na_zaznam_o_aktivite_hracu = "UPDATE statistika_kola SET " .
            " aktivnich_hracu = " . $this->odkaz_na_ridici_instanci_kolo->getAktivnich_hracu() .
            ", registrovanych_hracu = " . $this->odkaz_na_ridici_instanci_kolo->getRegistrovanych_hracu() . " WHERE kolo = " . $this->kolo . "; ";

        
        mysql_query($dotaz_na_zaznam_o_aktivite_hracu);

        mysql_query($dotaz_na_uzavreni_kola_v_databazi);

        $dotaz_na_otervreni_noveho_kola = "INSERT INTO statistika_kola VALUES (" .
            ($this->kolo + 1) . ", CURRENT_TIMESTAMP , '0000-00-00 00:00:00' , " .
            "0, 0);";
        mysql_query($dotaz_na_otervreni_noveho_kola);

        echo "Výpočet konce kola skončil v " . date("H:i:s");
    }
}

?>
