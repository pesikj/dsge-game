<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formular_produkce
 *
 * @author pike
 */
class Formular_vyroby extends Formular {
    //put your code here
    
    private $definovany_prikaz_vyroby;
    private $pole_chyb;



    public function generuj_formular_vyroba() {
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
        <h2><?php echo $prekladac->vloz_retezec("vyroba") ?></h2>
        <div class="form">
            <form action="<?php echo $pageURL ?>" method="post" name="form_vyroba">
                <div class="form_radek">
                    <?php echo $prekladac->vloz_retezec("relativni_podil_spotrebni_zbozi"); ?>
                    <input class="form_field" type="text" name="relativni_podil_spotrebni_zbozi" value="<?php echo $this->definovany_prikaz_vyroby->get_relativni_pomer_spotrebni_zbozi();?>" />
                </div>
                <?php if ($GLOBALS['subjekt']->get_pravo_pouzit_vlastni_praci_ve_vyrobe() == 1) { ?>
                    <div class="form_radek">
                    <?php echo $prekladac->vloz_retezec("mnozstvi_vlastni_prace_ve_vyrobe"); ?>
                    <input class="form_field" type="text" name="mnozstvi_vlastni_prace_ve_vyrobe" value="<?php echo $this->definovany_prikaz_vyroby->get_mnozstvi_vlastni_prace();?>" />
                    </div>
                <?php } ?>
            <input type="hidden" name="typ_formulare" value="vyroba" />
            <input class="ok" type="submit" name="tlacitko" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit")?>" />
        </form>
        </div>
        <?php
    }

    function __construct(Prikaz_vyroby $definovany_prikaz_vyroby, $pole_chyb = null) {
        parent::__construct();
        $this->definovany_prikaz_vyroby = $definovany_prikaz_vyroby;
        $this->pole_chyb = $pole_chyb;
    }

}
?>
