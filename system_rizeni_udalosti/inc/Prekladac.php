<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Prekladac
 *
 * @author pike
 */
class Prekladac {
    //put your code here
    
    private $sx;

    public function __construct() {
        if (file_exists('czech.xml')) {
            $cesta_k_souboru = 'czech.xml';
        } else if (file_exists('../czech.xml')) {
            $cesta_k_souboru = '../czech.xml';
        }

        $sx = simplexml_load_file($cesta_k_souboru);
        $this->sx = $sx;
    }

    public function vloz_retezec($id_retezce, $parametry) {
        if (gettype($parametry) != 'array') {
            return;
        }
        foreach ($this->sx->retezec as $retezec) {
            if ((String)$retezec['id_retezce'] == $id_retezce) {
                foreach ($parametry as $id_parametru => $parametr) {
                    $retezec = str_replace("$" . $id_parametru, $parametr, $retezec);
                }
                return $retezec;
            }
        }
    }
}
?>
