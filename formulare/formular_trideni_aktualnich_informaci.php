<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_trideni_aktualnich_informaci
 *
 * @author pike
 */
class formular_trideni_aktualnich_informaci {
    //put your code here

    public function generuj_formular_aktualnich_informaci($default_data) {
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
                    echo "<td>";
                        echo "Zobrazit informaci o";
                    echo "</td>";
                    echo "<td>";
                        echo "<select name=\"zobrazovana_informace\" size=\"1\">";
                            echo "<option value=\"trh_spotrebniho_zbozi\"";
                                if ($default_data['zobrazovana_informace'] == 'trh_spotrebniho_zbozi') {
                                    echo " selected=\"selected\" ";
                                }
                            echo ">trhu spotřebního zboží </option>";

                            echo "<option value=\"trh_kapitaloveho_zbozi\"";
                                if ($default_data['zobrazovana_informace'] == 'trh_kapitaloveho_zbozi') {
                                    echo " selected=\"selected\" ";
                                }
                            echo ">trhu kapitálového zboží </option>";

                            echo "<option value=\"trh_prace\"";
                                if ($default_data['zobrazovana_informace'] == 'trh_prace') {
                                    echo " selected=\"selected\" ";
                                }
                            echo ">trhu práce </option>";

                            echo "<option value=\"trh_kapitalu_2_obdobi\"";
                                if ($default_data['zobrazovana_informace'] == 'trh_kapitalu_2_obdobi') {
                                    echo " selected=\"selected\" ";
                                }
                            echo ">trhu kapitálu (2 obdobi) </option>";


                        echo "</select>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>";
                        echo "Kolo: ";
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"vybrane_kolo\" value=\"";
                            echo $default_data['vybrane_kolo'];
                        echo "\" />";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"id_stranky\" value=\"aktualni_stav\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Potvrď\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
