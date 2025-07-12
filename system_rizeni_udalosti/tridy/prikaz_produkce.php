<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of prikaz_produkce
 *
 * @author pike
 */
class prikaz_produkce {
    //put your code here

    private $hodin_prace;
    private $druh_zbozi;

    function __construct($hodin_prace, $druh_zbozi) {
        $this->hodin_prace = $hodin_prace;
        $this->druh_zbozi = $druh_zbozi;
    }


    public function getHodin_prace() {
        return $this->hodin_prace;
    }

    public function setHodin_prace($hodin_prace) {
        $this->hodin_prace = $hodin_prace;
    }

    public function getDruh_zbozi() {
        return $this->druh_zbozi;
    }

    public function setDruh_zbozi($druh_zbozi) {
        $this->druh_zbozi = $druh_zbozi;
    }

    public function vytvor_SQL_definici($hrac, $kolo, $vkladani) {
        if ($vkladani == true) {
            $dotaz = "INSERT INTO prikazy_produkce VALUES ('" . $hrac . "', " . $kolo .", " . $this->hodin_prace .
            ", ";
            if ($this->druh_zbozi == 1) {
                $dotaz .= "1";
            } else {
                $dotaz .= "2";
            }
            $dotaz .= ");";
        } else {
            $dotaz = "UPDATE prikazy_produkce SET hodin_prace = " . $this->hodin_prace .
            ", druh_zbozi = ";
            if ($this->druh_zbozi == 1) {
                $dotaz .= "1";
            } else {
                $dotaz .= "2";
            }
            $dotaz .= " WHERE login='" . $hrac . "' AND kolo = ". $kolo .";";
        }
        return $dotaz;
    }

}
?>
