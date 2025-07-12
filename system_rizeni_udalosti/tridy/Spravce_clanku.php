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

    function __construct() {
        if (file_exists('clanky_czech.xml')) {
            $cesta_k_souboru = 'clanky_czech.xml';

        } else if (file_exists('../clanky_czech.xml')) {
            $cesta_k_souboru = '../clanky_czech.xml';
        }

        $sx = simplexml_load_file($cesta_k_souboru);
        $this->sx = $sx;
        $this->cesta_k_souboru = $cesta_k_souboru;
    }

    public function get_uvodni_clanek() {
        $uvodni_clanek = $this->sx->uvodni_clanek[0];

        return (string) $uvodni_clanek;
    }

    public function uloz_uvodni_clanek($uvodni_clanek) {
        $this->sx->uvodni_clanek[0] = $uvodni_clanek;
        file_put_contents($this->cesta_k_souboru, $this->sx->asXML());
    }

}
?>
