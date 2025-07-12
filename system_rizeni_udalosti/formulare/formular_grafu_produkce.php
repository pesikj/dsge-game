<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_grafu_produkce
 *
 * @author pike
 */
class formular_grafu_produkce {
    //put your code here

    public function generuj_formular_grafu_produkce($default_data_pro_formular_grafu) {
        $pageURL = 'http';
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $pageURL = str_replace("&", "&amp;", $pageURL);

        echo "<form action=\"" . $pageURL . "\" method=\"get\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo "<tr>";
                    echo "<td>";
                        echo "Variabilní proměnná ve výrobě";
                    echo "</td>";
                    echo "<td>";
                        echo "<select name=\"graf_variabilni_vyrobni_faktor\" size=\"1\">";
                            echo "<option value=\"prace\"";
                                if ($default_data_pro_formular_grafu['variabilni_vyrobni_faktor'] == 'prace') {
                                    echo " selected=\"selected\" ";
                                }
                            echo " > hodiny práce  </option>";

                            echo "<option value=\"kapitalove_zbozi\"";
                                if ($default_data_pro_formular_grafu['variabilni_vyrobni_faktor'] == 'kapitalove_zbozi') {
                                    echo " selected=\"selected\" ";
                                }
                            echo " > množství kapitálového zboží </option>";
                        echo "</select>";
                    echo "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>";
                        echo "Množství fixního faktoru: ";
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"mnozstvi_fixniho_faktoru\" value=\"";
                            echo $default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru'];
                        echo "\" />";
                    echo "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>";
                        echo "Maximum osy x: ";
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"osa_x_max\" value=\"";
                            echo $default_data_pro_formular_grafu['osa_x_max'];
                        echo "\" />";
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"id_stranky\" value=\"produkce\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Potvrď\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
