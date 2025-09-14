<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_volby_jazyka
 *
 * @author pike
 */
class Formular_volby_jazyka extends Formular {
    //put your code here

    public function __construct() {
        parent::__construct();
        $this->hrac = $GLOBALS['hrac'];
    }

    public function vygeneruj_a_vloz_formular() {
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<p>";
                echo $this->vygeneruj_vyber_jazyka();
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit", array ()) . "\" /></p>";
            echo "</table>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"volba_jazyka\" />";
        echo "</form>";
    }


}
?>
