<script type="text/javascript">
    function vygeneruj_dalsi_pole_pro_zadani_prikazu_nabidky() {
        var prvni_cast = "<?php $prekladac = $GLOBALS['prekladac']; echo $prekladac->vloz_retezec("formular_nabidka_prvni_cast"); ?>";
        var druha_cast = "<?php echo $prekladac->vloz_retezec("formular_nabidka_druha_cast") ?>";
        document.form_nabidka.pocet_polozek.value = document.form_nabidka.pocet_polozek.value + 1;
        var pole = document.createElement("div");
        pole.setAttribute("class", "form_radek");
        pole.innerHTML = prvni_cast;
        pole.innerHTML += "<input type=\"text\" class=\"form_field\" name=\"cena" + document.form_nabidka.pocet_polozek.value + "\" value=\"0\" />";
        pole.innerHTML += druha_cast;
        pole.innerHTML += "<input type=\"text\" class=\"form_field\" name=\"mnozstvi" + document.form_nabidka.pocet_polozek.value + "\" value=\"0\" />";
        pole.innerHTML += "</div>";
        document.getElementById('pole_zadani_prikazu_nabidky').appendChild(pole);
    }
</script>

<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_nabidka
 *
 * @author pike
 */
class formular_nabidka extends Formular {
    //put your code here

    private $id_trhu;
    private $default_data;
    private $pole_chyb;
    private $pocet_prikazu;

    public function generuj_formular_nabidky ($pocet_prikazu = 4) {
        $prekladac = $GLOBALS['prekladac'];

        $id_trhu = $this->id_trhu;
        $default_data = $this->default_data;
        $pole_chyb = $this->pole_chyb;

        end($default_data);

        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        $pageURL = str_replace("&", "&amp;", $pageURL);
        ?>
        <h2><?php echo $prekladac->vloz_retezec("nabidka") ?></h2>
        <div class="form">
            <form action="<?php echo $pageURL ?>" method="post" name="form_nabidka">
                <div id="pole_zadani_prikazu_nabidky">
                <?php
                for ($i = $this->pocet_prikazu; $i > 0; $i--) {
                    echo "<div class=\"form_radek\">";
                    echo $prekladac->vloz_retezec("formular_nabidka_prvni_cast");
                    echo "<input type=\"text\" class=\"form_field\" name=\"cena" . $i . "\" value=\"" . key($default_data) . "\" />";
                    echo $prekladac->vloz_retezec("formular_nabidka_druha_cast", array ());
                    echo "<input type=\"text\" class=\"form_field\" name=\"mnozstvi" . $i . "\" value=\"" . current($default_data) . "\"";
                        prev($default_data);
                    echo "/ />";
                    echo "</div>";
                }
                ?>
                </div>
            <a class="plus" onclick="vygeneruj_dalsi_pole_pro_zadani_prikazu_nabidky()"><?php echo $prekladac->vloz_retezec("pridej_radek");?></a>
            <input type="hidden" name="pocet_polozek" value="<?php echo $this->pocet_prikazu; ?>" />
            <input type="hidden" name="typ_formulare" value="nabidka" />
            <input type="hidden" name="id_trhu" value="<?php echo $id_trhu ?>" />
            <input class="potvrdit" type="submit" name="potvrdit" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit"); ?>"/>
            <input class="ok" type="submit" name="potvrdit_a_zobrazit" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit_a_zobrazit");?>" />
        </form>
        </div>
        <?php
    }

    function __construct($id_trhu, $default_data, $pole_chyb) {
        parent::__construct();
        $this->id_trhu = $id_trhu;
        $this->default_data = $default_data;
        $this->pocet_prikazu = sizeof($default_data);
        $this->pole_chyb = $pole_chyb;
    }



}
?>
