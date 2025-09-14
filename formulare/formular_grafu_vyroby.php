<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_grafu_produkce
 *
 * @author pike
 */
class formular_grafu_vyroby extends Formular{
    //put your code here

    private $default_data_pro_formular_grafu;

    public function generuj_formular_grafu_vyroby() {
        $default_data_pro_formular_grafu = $this->default_data_pro_formular_grafu;
        $prekladac = $GLOBALS['prekladac'];
        
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
        <h2><?php echo $this->prekladac->vloz_retezec('zobrazeni_produkcni_funkce'); ?></h2>
        <div class="form">
            <form action="<?php echo $pageURL ?>" method="post" name="form_vyroba">
            <div class="form_radek">
                <?php echo $prekladac->vloz_retezec("formular_grafu_vyroby_variabilni_promenna"); ?>
                <select class="form_field" name=\"variabilni_vyrobni_faktor\" size=\"1\">";
                    <option value="prace"
                        <?php if ($default_data_pro_formular_grafu['variabilni_vyrobni_faktor'] == 'prace') {
                            echo " selected=\"selected\" ";
                        }
                    echo " > " . $prekladac->vloz_retezec("mnozstvi_prace") ."  </option>";

                    echo "<option value=\"kapitalove_zbozi\"";
                        if ($default_data_pro_formular_grafu['variabilni_vyrobni_faktor'] == 'kapitalove_zbozi') {
                            echo " selected=\"selected\" ";
                        }
                    echo " >" . $prekladac->vloz_retezec("mnozstvi_kapitaloveho_zbozi") .  "</option>";
                    ?>
                </select>
            </div>
            <div class="form_radek">
                <?php echo $prekladac->vloz_retezec("formular_graf_vyroby_mnozstvi_fixniho_faktoru"); ?>
                <input class="form_field" type="text"  name="mnozstvi_fixniho_faktoru" value="<?php echo $default_data_pro_formular_grafu['mnozstvi_fixniho_faktoru']; ?>" />
            </div>
            <div class="form_radek">
                <?php echo $prekladac->vloz_retezec("formular_graf_vyroby_max_x"); ?>
                <input class="form_field" type="text"  name="osa_x_max" value="<?php echo $default_data_pro_formular_grafu['osa_x_max']; ?>" />
            </div>
            <p>
                <input type="hidden" name="id_stranky" value="vyroba" />
                <input class="ok" type="submit" name="tlacitko" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit") ?>" />
            </p>
        </form>
        </div>
        <?php
    }

    function __construct($default_data_pro_formular_grafu) {
        parent::__construct();
        $this->default_data_pro_formular_grafu = $default_data_pro_formular_grafu;
    }

}
?>
