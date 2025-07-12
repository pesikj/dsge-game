<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Spravce_clanku
 *
 * @author pike
 */
class Spravce_clanku {
    //put your code here

    private $sx;
    private $cesta_k_souboru;
    private $pravo_k_zapisu_do_souboru;

    function __construct() {
        if (file_exists('clanky_czech.xml')) {
            $cesta_k_souboru = 'clanky_czech.xml';

        } else if (file_exists('../clanky_czech.xml')) {
            $cesta_k_souboru = '../clanky_czech.xml';
        }

        $sx = simplexml_load_file($cesta_k_souboru);
        $this->sx = $sx;
        $this->cesta_k_souboru = $cesta_k_souboru;

        if (@file_put_contents($this->cesta_k_souboru, $this->sx->asXML()) == false) {
            $this->pravo_k_zapisu_do_souboru = false;
        } else {
            $this->pravo_k_zapisu_do_souboru = true;
        }
    }

    public function get_uvodni_clanek() {
        if ($this->pravo_k_zapisu_do_souboru == true) {
            $uvodni_clanek = $this->sx->uvodni_clanek[0];
            return (string) $uvodni_clanek;
        } else {
            $dotaz_na_ziskani_uvodniho_clanku = "SELECT * FROM clanky WHERE id_clanku = 'uvodni_clanek';";
            $vysledek_ziskani_uvodniho_clanku = mysql_query($dotaz_na_ziskani_uvodniho_clanku);

            if (mysql_num_rows($vysledek_ziskani_uvodniho_clanku) == 1) {
                $radek = mysql_fetch_array($vysledek_ziskani_uvodniho_clanku);
                return $radek['obsah'];
            } else {
                return "";
            }
        }
    }

    public function uloz_uvodni_clanek($uvodni_clanek) {
        if ($this->pravo_k_zapisu_do_souboru == true) {
            $this->sx->uvodni_clanek[0] = $uvodni_clanek;
            file_put_contents($this->cesta_k_souboru, $this->sx->asXML());
        } else {
            $dotaz_na_ziskani_uvodniho_clanku = "SELECT * FROM clanky WHERE id_clanku = 'uvodni_clanek';";
            $vysledek_ziskani_uvodniho_clanku = mysql_query($dotaz_na_ziskani_uvodniho_clanku);

            if (mysql_num_rows($vysledek_ziskani_uvodniho_clanku) == 1) {
                $dotaz_zmena_uvodniho_clanku = "UPDATE clanky SET obsah = '" . $uvodni_clanek . "' WHERE id_clanku = 'uvodni_clanek';";
            } else {
                $dotaz_zmena_uvodniho_clanku = "INSERT INTO clanky VALUES ('uvodni_clanek', '" . $uvodni_clanek . "');";
            }

            mysql_query($dotaz_zmena_uvodniho_clanku) or die ($dotaz_zmena_uvodniho_clanku);
        }

    }

}
?>
