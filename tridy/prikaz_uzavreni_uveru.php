<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of prikaz_uzavreni_uveru
 *
 * @author pike
 */
class prikaz_uzavreni_uveru {
    //put your code here

    private $dluznik;
    private $veritel;
    private $delka_uveru;
    private $zapujcena_castka;
    private $uver_poskytnut_v_kole;
    private $urokova_mira_v_procentech;

    function __construct($dluznik, $veritel, $delka_uveru, $zapujcena_castka, $uver_poskytnut_v_kole, $urokova_mira_v_procentech) {
        $this->dluznik = $dluznik;
        $this->veritel = $veritel;
        $this->delka_uveru = $delka_uveru;
        $this->zapujcena_castka = $zapujcena_castka;
        $this->uver_poskytnut_v_kole = $uver_poskytnut_v_kole;
        $this->urokova_mira_v_procentech = $urokova_mira_v_procentech;
    }


    public function vygeneruj_prikaz_pro_upravu_databaze() {
        $dotaz_na_zaznam_do_tabulky_uveru = "INSERT INTO uvery VALUES ('" .
            $this->dluznik . "', '" . $this->veritel . "', " . $this->delka_uveru .
            ", " . $this->zapujcena_castka . ", " . $this->uver_poskytnut_v_kole . ", " .
            $this->urokova_mira_v_procentech . "); ";
        $dotaz_na_snizeni_mnozstvi_kapitalu_veritele = "UPDATE hraci SET " .
                " mnozstvi_kapitalu = mnozstvi_kapitalu - " . $this->zapujcena_castka .
                " WHERE login ='" . $this->veritel . "'; ";
        $dotaz_na_zvyseni_mnozstvi_kapitalu_dluznika= "UPDATE hraci SET " .
                " mnozstvi_kapitalu = mnozstvi_kapitalu + " . $this->zapujcena_castka .
                " WHERE login ='" . $this->dluznik . "'; ";

        $dotaz = $dotaz_na_zaznam_do_tabulky_uveru . $dotaz_na_snizeni_mnozstvi_kapitalu_veritele .
            $dotaz_na_zvyseni_mnozstvi_kapitalu_dluznika;
        return $dotaz;
    }
}
?>
