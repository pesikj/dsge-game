<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of prikaz_prodeje
 *
 * @author pike
 */
class prikaz_prodeje {
    //put your code here

    private $nazev_trhu;
    private $kolo;
    private $mnozstvi = 0;
    private $trzni_jednotkova_cena = 0;
    private $prodavajici;
    private $hrac;


    function __construct($nazev_trhu, $kolo, $mnozstvi, $trzni_jednotkova_cena, $prodavajici) {
        $this->nazev_trhu = $nazev_trhu;
        $this->kolo = $kolo;
        $this->mnozstvi = $mnozstvi;
        $this->trzni_jednotkova_cena = $trzni_jednotkova_cena;
        $this->prodavajici = $prodavajici;
        $this->hrac = new hrac($prodavajici);
    }

    public function zvys_prodane_mnozství_o_jednotku() {
        $this->mnozstvi++;
    }

    /**
     * Chápeme i jako poskytnutí úvěru.
     */
    public function vygeneruj_a_proved_prikaz_pro_upravu_databaze() {
        $dotaz = "";
        if ($this->nazev_trhu == 'trh_spotrebniho_zbozi') {
            $this->hrac->sniz_mnozstvi_spotrebniho_zbozi($this->mnozstvi);
            $this->hrac->zvys_mnozstvi_kapitalu($this->mnozstvi * $this->trzni_jednotkova_cena);
            $dotaz_na_zaznamenani_prodaneho_spotrebniho_zbozi = "INSERT INTO prodane_spotrebni_zbozi VALUES ( '" .
                $this->prodavajici . "', " . $this->kolo . ", " . $this->mnozstvi . ") ;";
            mysql_query($dotaz_na_zaznamenani_prodaneho_spotrebniho_zbozi);
        } else if ($this->nazev_trhu == 'trh_kapitaloveho_zbozi') {
            $this->hrac->sniz_mnozstvi_kapitaloveho_zbozi($this->mnozstvi);
            $this->hrac->zvys_mnozstvi_kapitalu($this->mnozstvi * $this->trzni_jednotkova_cena);
            $dotaz_na_zaznamenani_prodaneho_kapitaloveho_zbozi = "INSERT INTO prodane_kapitalove_zbozi VALUES ( '" .
                $this->prodavajici . "', " . $this->kolo . ", " . $this->mnozstvi . ") ;";
            mysql_query($dotaz_na_zaznamenani_prodaneho_kapitaloveho_zbozi);
        } else if ($this->nazev_trhu == 'trh_prace') {
            $this->hrac->zvys_mnozstvi_kapitalu($this->mnozstvi * $this->trzni_jednotkova_cena);
            $dotaz_na_zaznamenani_odpracovanych_hodin = "INSERT INTO prodana_prace VALUES ( '" .
                $this->prodavajici . "', " . $this->kolo . ", " . $this->mnozstvi . ") ;";
            mysql_query($dotaz_na_zaznamenani_odpracovanych_hodin);
        } else if ($this->nazev_trhu == 'trh_kapitalu_2_obdobi') {
            $this->hrac->sniz_mnozstvi_kapitalu($this->mnozstvi);
            echo "Množství: " . $this->mnozstvi;
            $dotaz_na_pridani_zaznamu_o_zapujcenem_kapitalu = "INSERT INTO uspory_zapujcene_2_obdobi VALUES ( '" .
                $this->prodavajici . "', " . $this->kolo . ", " . $this->mnozstvi . "); ";
            mysql_query($dotaz_na_pridani_zaznamu_o_zapujcenem_kapitalu) or die ($dotaz_na_pridani_zaznamu_o_zapujcenem_kapitalu);
        }
    }


}
?>
