<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_xls_export_dat
 *
 * @author pike
 */
class Formular_export_dat extends Formular_administratora {
    //put your code here
    public function vygeneruj_a_vloz_formular() {
        $prekladac = $this->prekladac;
        echo "<form action=\"" . $this->pageURL . "\" method=\"post\">";
        ?>
            <input type="radio" value="spotreba" name="list" checked="checked">Spotřeba</input><br />
            <input type="radio" value="odpracovano" name="list">Odpracovaný čas</input><br />
            <input type="radio" value="kapital_ve_vyrobe" name="list">Kapitál ve výrobě</input><br />
            <input type="radio" value="trhy" name="list">Trhy</input><br />
            <?php
            echo "<select id=\"id_druhu_trhu\" name=\"id_druhu_trhu\">";
                echo "<option />" . $this->vygeneruj_vyber_druhu_trhu();
            echo "</select>";
            ?>
            <input type="radio" value="vyrobene_zbozi" name="list">Vyrobené zboží</input><br />
            <input type="radio" value="rezidenti" name="list">Rezidenti</input><br />
            <input type="radio" value="aktivita" name="list">Aktivita</input><br />
            <input type="radio" value="penize" name="list">Peníze</input><br />
            <input type="radio" value="ziskane_body" name="list">Získané body</input><br />
        <?php
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"xls_export_dat\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec('generuj_xls_export_dat') . "\" />";
            echo "</p>";
        echo "</form>";
    }

    public function __construct() {

        parent::__construct();
    }
}
?>
