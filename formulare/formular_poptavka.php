<script type="text/javascript">
    function vygeneruj_dalsi_pole_pro_zadani_prikazu_poptavky() {
        var prvni_cast = "<?php $prekladac = $GLOBALS['prekladac']; echo $prekladac->vloz_retezec("formular_poptavka_prvni_cast"); ?>";
        var druha_cast = "<?php echo $prekladac->vloz_retezec("formular_poptavka_druha_cast") ?>";
        document.form_poptavka.pocet_polozek.value = document.form_poptavka.pocet_polozek.value + 1;
        var pole = document.createElement("div");
        pole.setAttribute("class", "form_radek");
        pole.innerHTML = prvni_cast;
        pole.innerHTML += "<input type=\"text\" class=\"form_field\" name=\"cena" + document.form_poptavka.pocet_polozek.value + "\" value=\"0\" />";
        pole.innerHTML += druha_cast;
        pole.innerHTML += "<input type=\"text\" class=\"form_field\" name=\"mnozstvi" + document.form_poptavka.pocet_polozek.value + "\" value=\"0\" />";
        pole.innerHTML += "</div>";
        document.getElementById('pole_zadani_prikazu_poptavky').appendChild(pole);
    }
</script>
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_poptavka
 *
 * @author pike
 */
class formular_poptavka extends Formular {
    //put your code here

    private $default_data;
    private $id_trhu;
    private $pocet_prikazu;


    public function generuj_formular_poptavky() {
        $pocet_prikazu = $this->pocet_prikazu;
        $prekladac = $GLOBALS['prekladac'];

        $default_data = $this->default_data;
        $id_trhu = $this->id_trhu;

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
        <h2><?php echo $prekladac->vloz_retezec("poptavka") ?></h2>
        <div class="form">
            <form action="<?php echo $pageURL ?>" method="post" name="form_poptavka">
                <div id="pole_zadani_prikazu_poptavky">
                <?php
                
                for ($i = $this->pocet_prikazu; $i > 0; $i--) {
                    echo "<div class=\"form_radek\">";
                    echo $prekladac->vloz_retezec("formular_poptavka_prvni_cast");
                    echo "<input class=\"form_field\" type=\"text\" name=\"cena" . $i . "\" value=\"" . key($default_data) . "\" />";
                    echo $prekladac->vloz_retezec("formular_poptavka_druha_cast");
                    echo "<input class=\"form_field\" type=\"text\" name=\"mnozstvi" . $i . "\" value=\"" . current($default_data) . "\"/>";
                    echo "</div>";
                    prev($default_data);
                }
                ?>
                </div>
                <a class="plus" onclick="vygeneruj_dalsi_pole_pro_zadani_prikazu_poptavky()"><?php echo $prekladac->vloz_retezec("pridej_radek");?></a>
            <input type="hidden" name="pocet_polozek" value="<?php echo $this->pocet_prikazu; ?>" />
            <input type="hidden" name="typ_formulare" value="poptavka" />
            <input type="hidden" name="id_trhu" value="<?php echo $id_trhu ?>" />
            <input class="potvrdit" type="submit" name="potvrdit" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit"); ?>"/>
            <input class="ok" type="submit" name="potvrdit_a_zobrazit" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit_a_zobrazit");?>" />
        </form>
        </div>
        <?php
    }

    public function generuj_dalsi_radek_pro_prikaz_poptavky() {

    }

    function __construct($id_trhu, $default_data) {
        parent::__construct();
        $this->default_data = $default_data;
        $this->id_trhu = $id_trhu;
        $this->pocet_prikazu = sizeof($default_data);
    }

}
?>
