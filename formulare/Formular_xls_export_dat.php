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
class Formular_xls_export_dat extends Formular {
    //put your code here
    public function vygeneruj_a_vloz_formular() {
        $prekladac = $this->prekladac;
        echo "<form action=\"" . $this->pageURL . "\" method=\"post\">";
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
