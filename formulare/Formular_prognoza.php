<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_prognoza
 *
 * @author pike
 */
class Formular_prognoza extends Formular{
    //put your code here

    private $pole_prognozovanych_trznich_cen;

    public function generuj_formular_prognozy() {
        $dostupne_trhy = $GLOBALS['subjekt']->get_dostupne_trhy();
        $prekladac = $GLOBALS['prekladac'];
        $prefix_prognozovana_cena = spravce_GUI::$prefix_prognozovana_cena;
        $infromace_o_ekonomikach = $this->informace_o_ekonomikach;

        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        $pageURL = str_replace("&", "&amp;", $pageURL);

        echo "<form action=\"" . $pageURL . "\" method=\"get\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
            echo "<tr>";
                echo "<td colspan=\"2\" style=\"text-align:center;\"><b>" .  $prekladac->vloz_retezec('prognoza_pri_starych_cenach') . "</b></td>";
            echo "</tr>";
            foreach ($dostupne_trhy as $id_aktualniho_trhu => $prava_na_aktualnim_trhu) {
                echo "<tr>";
                    echo "<td>";
                        echo $infromace_o_ekonomikach->get_nazev_trhu($id_aktualniho_trhu);
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"" . $prefix_prognozovana_cena . $id_aktualniho_trhu . "\" value=\"";
                        echo $this->pole_prognozovanych_trznich_cen[$id_aktualniho_trhu];
                        echo "\" />";
                    echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"id_stranky\" value=\"prognoza\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_prognoza_prepocitej") . "\" />";
            echo "</p>";
        echo "</form>";
    }

    function __construct($pole_prognozovanych_trznich_cen) {
        parent::__construct();
        $this->pole_prognozovanych_trznich_cen = $pole_prognozovanych_trznich_cen;
    }

}
?>
