<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_poptavka
 *
 * @author pike
 */
class formular_poptavka {
    //put your code here

    public function generuj_formular_poptavky($default_data, $nazev_tabulky, $oznaceni_P) {
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
                        echo "<strong>Poptávka</strong>";
                    echo "</td>";
                echo "</tr>";

                for ($i = 4; $i > 0; $i--) {
                    echo "<tr>";
                        echo "<td>";
                            echo "Pokud je " . $oznaceni_P . " menší než";
                        echo "</td>";
                        echo "<td>";
                            echo "<input type=\"text\" name=\"cena" . $i . "\" value=\"" . key($default_data) . "\" />";
                        echo "</td>";
                        echo "<td> budu poptávat </td>";
                        echo "<td>";
                            echo "<input type=\"text\" name=\"mnozstvi" . $i . "\" value=\"" . current($default_data) . "\"/>";
                        echo "</td>";
                    echo "</tr>";
                    prev($default_data);
                }

            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"poptavka\" />";
                echo "<input type=\"hidden\" name=\"nazev_tabulky\" value=\"" . $nazev_tabulky . "\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Potvrď\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
