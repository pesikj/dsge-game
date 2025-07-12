<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of prikaz_nakupu
 *
 * @author pike
 */
class prikaz_nakupu {
    //put your code here

    private $nazev_trhu;
    private $mnozstvi;
    private $trzni_jednotkova_cena;
    private $hrac;
    private $kupujici;
    private $kolo;

    function __construct($nazev_trhu, $kolo, $mnozstvi, $trzni_jednotkova_cena, $kupujici) {
        $this->nazev_trhu = $nazev_trhu;
        $this->mnozstvi = $mnozstvi;
        $this->trzni_jednotkova_cena = $trzni_jednotkova_cena;
        $this->kupujici = $kupujici;
        $this->kolo = $kolo;
        $this->hrac = new hrac($kupujici);
    }

    public function zvys_nakoupene_mnozství_o_jednotku() {
        $this->mnozstvi++;
    }

    public function vygeneruj_a_proved_prikaz_pro_upravu_databaze() {
        if ($this->nazev_trhu == 'trh_spotrebniho_zbozi') {
            $this->vygeneruj_a_proved_prikaz_pro_snizeni_mnozstvi_kapitalu();
            $dotaz_na_zaznam_spotreby = "INSERT INTO spotreba VALUES ( '" . $this->kupujici .
                "', " . $this->kolo . ", " . $this->mnozstvi . "); ";
            mysql_query($dotaz_na_zaznam_spotreby);
        } else if ($this->nazev_trhu == 'trh_kapitaloveho_zbozi') {
            $this->vygeneruj_a_proved_prikaz_pro_snizeni_mnozstvi_kapitalu();
            $dotaz_na_zaznam_kapitaloveho_zbozi_ve_vyrobe = "UPDATE kapitalove_zbozi_ve_vyrobe SET " .
                "mnozstvi_kapitaloveho_zbozi = mnozstvi_kapitaloveho_zbozi + " . $this->mnozstvi . " WHERE login ='" .
                $this->kupujici . "' AND kolo = " . $this->kolo . " ; ";
            mysql_query($dotaz_na_zaznam_kapitaloveho_zbozi_ve_vyrobe) or die ($dotaz_na_zaznam_kapitaloveho_zbozi_ve_vyrobe);

            $dotaz_na_zaznam_o_nakupu_zbozi = "INSERT INTO nakupy_kapitaloveho_zbozi VALUES ( '" . $this->kupujici .
                "', " . $this->kolo . ", " . $this->mnozstvi . "); ";
            mysql_query($dotaz_na_zaznam_o_nakupu_zbozi);
        } else if ($this->nazev_trhu == 'trh_prace') {
            $this->vygeneruj_a_proved_prikaz_pro_snizeni_mnozstvi_kapitalu();
            $dotaz_na_pridani_zaznamu_o_nakoupene_praci = "INSERT INTO nakoupena_prace VALUES ('" . $this->kupujici .
                "', " . $this->kolo . ", " . $this->mnozstvi . ") ;";
            mysql_query($dotaz_na_pridani_zaznamu_o_nakoupene_praci);
        } else if ($this->nazev_trhu == 'trh_kapitalu_2_obdobi') {
            $this->hrac->zvys_mnozstvi_kapitalu($this->mnozstvi);
            $dotaz_na_pridani_zaznamu_o_zapujcenem_kapitalu = "INSERT INTO uvery_ziskane_2_obdobi VALUES ( '" .
                $this->kupujici . "', " . $this->kolo . ", " . $this->mnozstvi . "); ";
            mysql_query($dotaz_na_pridani_zaznamu_o_zapujcenem_kapitalu);
        }
    }

    private function vygeneruj_a_proved_prikaz_pro_snizeni_mnozstvi_kapitalu() {
        $this->hrac->sniz_mnozstvi_kapitalu($this->mnozstvi * $this->trzni_jednotkova_cena);
        mysql_query($dotaz_na_snizeni_mnozstvi_kapitalu);
    }

}
?>
