<script type="text/javascript">
function generuj_pole_pro_zadavani_polozek() {
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("nastaveni_trhu_dle_druhu_trhu").innerHTML=xmlhttp.responseText;
        }
    }
    var id_druhu_trhu = document.getElementById("id_druhu_trhu").value;
    xmlhttp.open("GET","data_pro_ajax/nastaveni_trhu_dle_druhu_trhu.php?id_druhu_trhu="+id_druhu_trhu,true);
    xmlhttp.send();
}

function generuj_tabulku_prav() {
    if (window.XMLHttpRequest) {
        xmlhttp2=new XMLHttpRequest();
    }
    xmlhttp2.onreadystatechange=function() {
        if (xmlhttp2.readyState==4 && xmlhttp2.status==200) {
            document.getElementById("tabulka_druhu_subjektu_a_prav_obchodovani_na_trhu").innerHTML=xmlhttp2.responseText;
        }
    }
    var id_druhu_trhu = document.getElementById("id_druhu_trhu").value;
    var id_integracniho_celku = document.getElementById("id_integracniho_celku").value;
    xmlhttp2.open("GET","data_pro_ajax/tabulka_druhu_subjektu_a_prav_obchodovani_na_trhu.php?id_druhu_trhu="+id_druhu_trhu + "&id_integracniho_celku=" + id_integracniho_celku,true);
    xmlhttp2.send();
}

function prejmenuj_trh() {
    if (window.XMLHttpRequest) {
        xmlhttp3=new XMLHttpRequest();
    }
    xmlhttp3.onreadystatechange=function() {
        if (xmlhttp3.readyState==4 && xmlhttp3.status==200) {
            document.getElementById("nazev_trhu").value=xmlhttp3.responseText;
        }
    }
    var id_druhu_trhu = document.getElementById("id_druhu_trhu").value;
    var id_meny = document.getElementById("id_meny").value;
    xmlhttp3.open("GET","data_pro_ajax/nazev_trhu.php?id_druhu_trhu="+id_druhu_trhu + "&id_meny=" + id_meny,true);
    xmlhttp3.send();
}

function akce_id_druhu_trhu() {
    generuj_pole_pro_zadavani_polozek();
    generuj_tabulku_prav();
    prejmenuj_trh();
}


</script>
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_vytvoreni_trhu
 *
 * @author pike
 */
class Formular_vytvoreni_trhu extends Formular_administratora{
    //put your code here

    public function __construct() {
        parent::__construct();
    }

    public function vygeneruj_a_vloz_formular() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $this->prekladac;

        $informace_o_ekonomikach_xhtml = Informace_o_ekonomikach_xhtml_vystupy::get_informace_o_ekonomikach_xhtml();
        $informace_o_ekonomikach_xhtml->generuj_tabulku_trhu();

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";

                echo $generator_formularu->generuj_zahlavi_formulare($prekladac->vloz_retezec("vytvoreni_trhu"), "2");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("nazev_trhu"), "nazev_trhu");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("id_meny"),
                    "id_meny", $this->vygeneruj_vyber_meny(), "select",
                    "onchange=\"generuj_pole_pro_zadavani_polozek(); generuj_tabulku_prav(); prejmenuj_trh();\"");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("id_integracniho_celku"),
                    "id_integracniho_celku", $this->vygeneruj_vyber_integracnich_celku(), "select",
                    "onchange=\"generuj_pole_pro_zadavani_polozek(); generuj_tabulku_prav(); prejmenuj_trh();\"");

                    echo "<tr>";
                        echo "<td>";
                            echo $prekladac->vloz_retezec("id_druhu_trhu");
                        echo "</td>";
                        echo "<td>";
                            echo "<select id=\"id_druhu_trhu\" name=\"id_druhu_trhu\" onchange=\"generuj_pole_pro_zadavani_polozek(); generuj_tabulku_prav(); prejmenuj_trh();\">";
                                echo "<option />" . $this->vygeneruj_vyber_druhu_trhu();
                            echo "</select>";
                        echo "</td>";
                    echo "</tr>";

            echo "</table>";
            echo "<div id=\"tabulka_druhu_subjektu_a_prav_obchodovani_na_trhu\"></div>";
            echo "<div id=\"nastaveni_trhu_dle_druhu_trhu\"></div>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_trhu\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }
}
?>
