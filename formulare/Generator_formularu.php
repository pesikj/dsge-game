<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Generator_formularu
 *
 * @author pike
 */
class Generator_formularu {
    //put your code here

    public function generuj_text_input($name, $value = "") {
        return "<input id=\"" . $name . "\" type=\"text\" name=\"" . $name . "\" value=\"" . $value . "\" />";
    }

    public function generuj_samostatny_checkbox($name, $value = "") {
        $checkbox = "<input type=\"checkbox\"  name=\"" . $name . "\" id=\"" . $name . "\"";
        if ($value == "checked") {
            $checkbox .= " checked=\"checked\"";
        }
        $checkbox .= " />";
        return $checkbox;
    }

    public function generuj_radek_tabulky_formulare($popis_radku, $name, $value = "", $typ_prvku_formulare = "text_input", $vlastni_atributy = "") {
        $radek = "<tr>";
            $radek .= "<td>";
                $radek .= $popis_radku;
            $radek .=  "</td>";
            $radek .=  "<td>";
                if ($typ_prvku_formulare == "text_input") {
                    $radek .= $this->generuj_text_input($name, $value);
                } else if ($typ_prvku_formulare == "checkbox") {
                    $radek .= $this->generuj_samostatny_checkbox($name, $value);
                } else if ($typ_prvku_formulare == "select") {
                    $radek .= "<select id=\"" . $name . "\" name=\"" . $name . "\" " . $vlastni_atributy . " >";
                        $radek .= $value;
                    $radek .= "</select>";
                }
                
            $radek .=  "</td>";
        $radek .=  "</tr>";
        return $radek;
    }

    public function generuj_radek_a_text_area($popis_radku, $name, $colspan, $value = "") {
        $radek = "<tr>";
            $radek .= "<td colspan=\"" . $colspan . "\">";
                $radek .= $popis_radku;
            $radek .= "</td>";
        $radek .= "</tr>";

        $radek .= "<tr>";
            $radek .= "<td colspan=\"" . $colspan . "\">";
                $radek .= "<textarea rows=\"2\" name=\"" . $name . "\" style=\"width: 100%\">";
                    $radek .= $value;
                $radek .= "</textarea>";
            $radek .= "</td>";
        $radek .= "</tr>";
        return $radek;
    }

    public function generuj_zahlavi_formulare($text_zahlavi, $colspan) {
        $zahlavi = "<tr>";
            $zahlavi .= "<td colspan=\"". $colspan . "\"> <b>";
                $zahlavi .= $text_zahlavi;
            $zahlavi .= "</b> </td>";
        $zahlavi .= "</tr>";
    }
}
?>
