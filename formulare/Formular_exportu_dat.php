<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_exportu_dat
 *
 * @author pike
 */
class Formular_exportu_dat {
    //put your code here

    public function vloz_formular_exportu_dat() {
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
                        echo "Vyberte tabulku k exportu:";
                    echo "</td>";
                    echo "<td>";
                        echo "<select id=\"tabulka\" name=\"tabulka\">";
                            $vysledek = $this->ziskej_seznam_tabulek();
                            while ($row = mysql_fetch_row($vysledek)) {
                                echo "<option value=\"" . $row[0] . "\">" . $row[0] . "</option>";
                            }
                        echo "</select>";
                    echo "</td>";
                echo "</tr>";

            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"export_dat\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Exportuj\" />";
            echo "</p>";
        echo "</form>";
    }

    private function ziskej_seznam_tabulek() {
        $udaje_o_databazi = $GLOBALS['spravce_konfigurace']->get_pristup_k_databazi();
        $sql = "SHOW TABLES FROM " . $udaje_o_databazi['jmeno_databaze'];
        $vysledek = mysql_query($sql);

        if (!$vysledek) {
            echo "DB Error, could not list tables\n";
            echo 'MySQL Error: ' . mysql_error();
            exit;
        }

        return $vysledek;
    }
}
?>
