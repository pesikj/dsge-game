<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_editor
 *
 * @author pike
 */
class Formular_editor {
    //put your code here

    public function generuj_formular_editor($clanek) {
        $pageURL = 'http';
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $pageURL = str_replace("&", "&amp;", $pageURL);

        echo "<script type=\"text/javascript\" src=\"jscripts/tiny_mce/tiny_mce.js\"></script>";
        echo "<script type=\"text/javascript\">;";
            echo "tinyMCE.init({";
            echo "mode : \"textareas\"";
            echo "});";
        echo "</script>";

        echo "<form method=\"post\" action=\"" . $pageURL . "\">";
            echo "<p>";
                echo "<textarea name=\"obsah\" cols=\"50\" rows=\"15\">" . $clanek . " </textarea>";
                echo "<input type=\"submit\" value=\"Save\" />";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"editor\" />";
                echo "<input type=\"hidden\" name=\"id_clanku\" value=\"uvodni_clanek\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
