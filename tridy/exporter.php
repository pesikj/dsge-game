<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$exporter = new exporter("localhost", "root", "", "simulacnihra");
$exporter->vyber_tabulku("vyvoj_trznich_cen");
echo $exporter->export_do_csv(array("cena", "kolo"), array ("kolo" => "0"));
echo $exporter->export_do_XML();

/**
 * Description of exporter
 *
 * @author pike
 */
class exporter {
    //put your code here
    
    private $tabulka;

    public function __construct($server, $login, $passwd, $databaze) {
        $spojeni = mysql_connect($server, $login, $passwd);
        mysql_select_db($databaze);
    }

    public function vyber_tabulku($tabulka) {
        $this->tabulka = $tabulka;
    }

    public function export_do_csv($sloupce = null, $pole_podminek = null) {
        if (isset ($this->tabulka) == false) {
            echo "Neni vybrana tabulka.";
            return;
        }

        if (isset ($sloupce) == false) {
            $dotaz = "SELECT * FROM " . $this->tabulka . " ";
        } else {
            $dotaz = "SELECT ";

            $delka = sizeof($sloupce);
            $pozice = 1;

            foreach ($sloupce as $aktualni_sloupce) {
                $dotaz .= " " . $aktualni_sloupce . " ";
                if ($pozice < $delka) {
                    $dotaz .= ", ";
                }

                $pozice++;
            }

            $dotaz .= " FROM " . $this->tabulka . " ";;
        }

        if (isset ($pole_podminek) == true) {
            $dotaz .= " WHERE ";
            foreach ($pole_podminek as $klic_podminky => $hodnota_podminky) {
                $dotaz .= " " . $klic_podminky . " = " . $hodnota_podminky . " ";
            }
        }

        $dotaz .= ";";

        $vysledek = mysql_query($dotaz) or die($dotaz);
        $soubor = "";

        while ($radek = mysql_fetch_assoc($vysledek)) {
            foreach ($radek as $klic => $hodnota) {
                $soubor .= $hodnota . ";";
            }

            $soubor .= "\n";
        }

        return $soubor;
    }

    public function export_do_XML($sloupce = null) {
        $soubor = "<data>";
        if (isset ($this->tabulka) == false) {
            echo "Neni vybrana tabulka.";
            return;
        }

        if (isset ($sloupce) == false) {
            $dotaz = "SELECT * FROM " . $this->tabulka . "; ";
        }

        $vysledek = mysql_query($dotaz) or die($dotaz);
        $soubor = "";

        while ($radek = mysql_fetch_assoc($vysledek)) {
            $soubor .= "<element>";

            foreach ($radek as $klic => $hodnota) {
                $soubor .= "<" . $klic . ">";
                $soubor .= $hodnota;
                $soubor .= "</" . $klic . ">";
            }

            $soubor .= "</element>";
        }

        $soubor .= "</data>";

        return $soubor;
    }
}
?>
