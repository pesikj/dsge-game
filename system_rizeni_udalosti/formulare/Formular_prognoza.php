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
class Formular_prognoza {
    //put your code here

    public function generuj_formular_prognozy($default_data,spravce_konfigurace $spravce_konfigurace, Prekladac $prekladac) {
        $pageURL = 'http';
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $pageURL = str_replace("&", "&amp;", $pageURL);
        $spravce_konfigurace->get_aktivni_trhy();

        echo "<form action=\"" . $pageURL . "\" method=\"get\">";
            echo "<table border=\"0\" style=\"width: 100%\">";

            foreach ($spravce_konfigurace->get_aktivni_trhy() as $aktualni_aktivni_trh) {
                echo "<tr>";
                    echo "<td>";
                        echo $prekladac->vloz_retezec($aktualni_aktivni_trh, array());
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"" . $aktualni_aktivni_trh . "\" value=\"";
                            echo $default_data[$aktualni_aktivni_trh];
                        echo "\" />";
                    echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"id_stranky\" value=\"prognoza\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Přepočítej prognózu\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
