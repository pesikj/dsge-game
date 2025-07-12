<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_nabidka
 *
 * @author pike
 */
class formular_nabidka {
    //put your code here

    public function generuj_formular_nabidky ($default_data, $nazev_tabulky, $pole_chyb, $oznaceni_P) {

        end($default_data);

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
                    echo "<td colspan=\"4\" style=\"text-align: center;\">";
                        echo "<strong>Nabídka</strong>";
                    echo "</td>";
                echo "</tr>";

                for ($i = 4; $i > 0; $i--) {
                    echo "<tr>";
                        echo "<td>";
                            echo "Pokud je " . $oznaceni_P . " větší než";
                        echo "</td>";
                        echo "<td>";
                            echo "<input type=\"text\"  name=\"cena" . $i . "\" value=\"" . key($default_data) . "\" />";
                        echo "</td>";
                        echo "<td> budu nabízet </td>";
                        echo "<td>";
                            echo "<input type=\"text\"  name=\"mnozstvi" . $i . "\" value=\"" . current($default_data) . "\"";
                                prev($default_data);
                                if ($pole_chyb[$i] == 'E') {
                                    echo " style=\" background-color: red \"";
                                }
                                echo "/>";
                        echo "</td>";
                    echo "</tr>";
                }
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nabidka\" />";
                echo "<input type=\"hidden\" name=\"nazev_tabulky\" value=\"" .  $nazev_tabulky . "\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Potvrď\" />";
            echo "</p>";
        echo "</form>";
    }


}
?>
