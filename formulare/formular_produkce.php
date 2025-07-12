<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_produkce
 *
 * @author pike
 */
class formular_produkce {
    //put your code here

    public function generuj_formular_produkce($default_data, $pole_chyb) {
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

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo "<tr>";
                    echo "<td>";
                        echo "Množství vlastní práce pro vlastní výrobu ";
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"";
                        if ($pole_chyb[1] == "E") {
                            echo "style=\"background-color:red;\"";
                        }
                        if ($pole_chyb[1] == "S") {
                            echo "style=\"background-color:yellow;\"";
                        }
                        echo "name=\"hodin_prace\" value=\"";
                        echo $default_data['hodin_prace'] . "\" />";
                    echo "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>";
                        echo "V tomto kole použít zdroje k výrobě";
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"radio\" name=\"vyrabene_zbozi\" value=\"spotrebni_zbozi\"";
                        if ($default_data['druh_zbozi'] == 1) {
                            echo " checked=\"checked\" ";
                        }
                        echo "/> spotřebního zboží <br/>";
                        echo "<input type=\"radio\" name=\"vyrabene_zbozi\" value=\"kapitalove_zbozi\"";
                        if ($default_data['druh_zbozi'] == 2) {
                            echo " checked=\"checked\" ";
                        }
                        echo "/> kapitálového zboží <br />";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"produkce\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Potvrď\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
