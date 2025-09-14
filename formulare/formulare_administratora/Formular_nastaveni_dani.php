<script type="text/javascript">
function pridej_tok(druh_dane) {
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
           document.getElementById("tabulka_danenych_toku").innerHTML=xmlhttp.responseText;
        }
    }
    alert(druh_dane);
    var druh_dane_index = document.getElementById("druh_dane").selectedIndex;
    var druh_dane = document.getElementById("druh_dane").options[druh_dane_index].text;
    var ppdt = document.getElementById("ppdt").value;
    var idt = document.getElementById("idt").value;
    var koef = document.getElementById("koef").value;
    xmlhttp.open("GET","data_pro_ajax/nastaveni_dani.php?akce=pridani&druh_dane=" + druh_dane + "&ppdt=" + ppdt + "&idt="  + idt + "&koef=" + koef,true);
    xmlhttp.send();
}

function aktualizuj_toky(druh_dane) {
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
           document.getElementById("tabulka_danenych_toku").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","data_pro_ajax/nastaveni_dani.php?akce=aktualizuj&druh_dane=" + druh_dane, true);
    xmlhttp.send();
}
</script>
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_nastaveni_dani
 *
 * @author pike
 */
class Formular_nastaveni_dani extends Formular_administratora {
    //put your code here

    public function vygeneruj_a_vloz_formular() {
        $informace_o_ekonomikach_xhtml = Informace_o_ekonomikach_xhtml_vystupy::get_informace_o_ekonomikach_xhtml();
        $informace_o_ekonomikach_xhtml->generuj_tabulku_dani();
        
        $this->vygeneruj_a_vloz_formular_nastaveni_dane_pro_ekonomiku();
        $this->vygeneruj_a_vloz_formular_pridani_druhu_dane();
        $this->vygeneruj_a_vloz_formular_danenych_toku();
    }

    private function vygeneruj_a_vloz_formular_nastaveni_dane_pro_ekonomiku() {
        echo "<h3>" . $this->prekladac->vloz_retezec("nastaveni_dane_pro_ekonomiku") . "</h3>";
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_ekonomiky"),
                    "id_ekonomiky", $this->vygeneruj_vyber_ekonomiky(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_druhu_dane"),
                    "id_druhu_dane", $this->vygeneruj_vyber_druhu_dani(), "select");
                echo $this->generator_formularu->generuj_radek_a_text_area($this->prekladac->vloz_retezec("rovnice_dane"),
                    "rovnice_dane", "2");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"nastaveni_dane_pro_ekonomiku\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

    private function vygeneruj_a_vloz_formular_pridani_druhu_dane() {
        echo "<h3>" . $this->prekladac->vloz_retezec("pridani_druhu_dane") . "</h3>";
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_druhu_dane"),
                    "id_druhu_dane");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("id_druhu_subjektu"),
                    "id_druhu_subjektu", $this->vygeneruj_vyber_druhu_subjektu(), "select");
                echo $this->generator_formularu->generuj_radek_tabulky_formulare($this->prekladac->vloz_retezec("transfer"),
                    "transfer", "", "checkbox");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"pridani_druhu_dane\" />";
                    echo "<input type=\"submit\" value=\"" . $this->prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";
    }

    private function vygeneruj_a_vloz_formular_danenych_toku() {
        $generator_formularu = $this->generator_formularu;
        $prekladac = $this->prekladac;

        echo "<h3>" . $prekladac->vloz_retezec("nastaveni_danenych_toku") . "</h3>";

        echo "<select id=\"druh_dane\" name=\"druh_dane\" onChange=\"aktualizuj_toky(this.value)\"><option />" . $this->vygeneruj_vyber_druhu_dani() . "</select>";
        echo "<div id=\"tabulka_danenych_toku\"> </div>";
        
        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("popis_puvodu_daneneho_toku"),
                    "ppdt", $this->vygeneruj_vyber_druhu_popisu_puvodu_toku(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("id_druhu_trhu"),
                    "idt", $this->vygeneruj_vyber_druhu_trhu(), "select");
                echo $generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("koeficient_toku"),
                    "koef");
            echo "</table>";

            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_ekonomiky\" />";
                    echo "<input type=\"button\" onClick=\"pridej_tok(this.value)\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit")  ."\"/>";
            echo "</p>";
        echo "</form>";

    }

    public function __construct() {
        parent::__construct();
    }
}
?>
