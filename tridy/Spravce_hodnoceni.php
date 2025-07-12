<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_hodnoceni
 *
 * @author pike
 */
class Spravce_hodnoceni extends Trida_reagujici_na_udalosti {
    //put your code here

    private $kolo;
    private $spravce_konfigurace;

    function __construct() {
        $this->pole_identifikatoru_udalosti['KONEC_KOLA'] = 'ohodnot_hrace';
        $this->kolo = $GLOBALS['spravce_konfigurace']->get_cislo_aktualniho_kola();
        $this->spravce_konfigurace = $GLOBALS['spravce_konfigurace'];
        parent::__construct();
    }

    public function ohodnot_hrace($odesilatel, $parametry) {
        $dotaz_na_seznam_hracu = "SELECT login FROM hraci";
        $vysledek = mysql_query($dotaz_na_seznam_hracu);

        while ($radek = mysql_fetch_array($vysledek)) {
            $login = $radek['login'];
            $hrac = new hrac($login);

            if ($hrac->getMnozstvi_kapitalu() < 0) {
                $zaporny_penezni_zustatek = 1;
            } else {
                $zaporny_penezni_zustatek = 0;
            }

            if ($hrac->get_aktivita_v_aktualnim_kole() == 0) {
                $neaktivni = 1;
            } else {
                $neaktivni = 0;
            }

            $mnozstvi_bodu = self::hodnotici_funkce($this->kolo,
                $hrac->get_mnozstvi_volneho_casu(), $hrac->get_mnozstvi_spotrebovanych_statku(), $zaporny_penezni_zustatek, $neaktivni);
            $dotaz_na_hodnoceni_hrace = "INSERT INTO hodnoceni_hracu VALUES ( '" . $login . "', " .
                $this->kolo . ", " . $mnozstvi_bodu . ");";
            mysql_query($dotaz_na_hodnoceni_hrace);
        }
    }

    public static function hodnotici_funkce($kolo, $mnozstvi_volneho_casu, $mnozstvi_spotrebovanych_statku, $zaporny_penezni_zustatek, $neaktivni) {
        $rovnice = new EvalMath();
        $retezec_rovnice = $GLOBALS['spravce_konfigurace']->get_hodnotici_funkce();
        $retezec_rovnice = str_replace("kolo", $kolo, $retezec_rovnice);
        $retezec_rovnice = str_replace("mnozstvi_volneho_casu", $mnozstvi_volneho_casu, $retezec_rovnice);
        $retezec_rovnice = str_replace("mnozstvi_spotrebovanych_statku", $mnozstvi_spotrebovanych_statku, $retezec_rovnice);
        $retezec_rovnice = str_replace("zaporny_penezni_zustatek", $zaporny_penezni_zustatek, $retezec_rovnice);
        $retezec_rovnice = str_replace("neaktivni", $neaktivni, $retezec_rovnice);

        $vysledek = $rovnice->evaluate($retezec_rovnice);
        return $vysledek;
    }

}
?>
