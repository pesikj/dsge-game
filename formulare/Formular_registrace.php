<script type="text/javascript">
function validace_emailova_adresa(format_emailu) {
   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   var address = document.forms["registrace"].elements["emailova_adresa"].value;
   if(reg.test(address) == false) {
      alert(format_emailu);
      return false;
   } else {
       return true;
   }
}

function validace_hesel(shoda_hesla) {
    var heslo_1 = document.forms["registrace"].elements["heslo"].value;
    var heslo_2 = document.forms["registrace"].elements["heslo_2"].value;
    if (heslo_1 == heslo_2) {
        return true;
    } else {
        alert(shoda_hesla);
        return false;
    }
}

function validace_formulare(format_emailu, shoda_hesla, kratke_heslo, slabe_heslo, dostatecne_heslo, silne_heslo) {
    if (!validace_emailova_adresa(format_emailu)) {
        return false;
    }
    if (!validace_hesel(shoda_hesla)) {
        return false;
    }
    if (!kontrola_sily_hesla(kratke_heslo, slabe_heslo, dostatecne_heslo, silne_heslo)) {
        alert(vysledek_kontroly_hesla)
        return false;
    }
    return true;
}

var vysledek_kontroly_hesla;

function kontrola_sily_hesla(kratke_heslo, slabe_heslo, dostatecne_heslo, silne_heslo) {
    var strength = document.getElementById('strength');
    var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^a-zA-Z0-9_]).*$", "g");
    var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
    var enoughRegex = new RegExp("(?=.{6,}).*", "g");
    var pwd = document.forms["registrace"].elements["heslo"].value;
    if (false == enoughRegex.test(pwd)) {
        strength.innerHTML = kratke_heslo;
        vysledek_kontroly_hesla = kratke_heslo;
        return false;
    } else if (strongRegex.test(pwd)) {
        strength.innerHTML = '<span style="color:green">' + silne_heslo + '</span>';
        vysledek_kontroly_hesla = silne_heslo;
        return true;
    } else if (mediumRegex.test(pwd)) {
        strength.innerHTML = '<span style="color:green">' + dostatecne_heslo + '</span>';
        vysledek_kontroly_hesla = dostatecne_heslo;
        return true;
    } else {
        strength.innerHTML = '<span style="color:red">' + slabe_heslo + '</span>';
        vysledek_kontroly_hesla = slabe_heslo;
        return false;
    }
}
</script>
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_registrace
 *
 * @author pike
 */
class Formular_registrace extends Formular{
    //put your code here

    private $data_z_formulare_registrace;

    public function generuj_formular_registrace() {
        $prekladac = $this->prekladac;
        $generator_formularu = $this->generator_formularu;
        $data_z_formulare_registrace = $this->data_z_formulare_registrace;
        ?>
        <div id="stred">
        <h1>Hráč mimo ZČU - registrace</h1>
        <div id="registrace">
        <!-- formular -->
        <div id="form">
        <div class="form_ods">
        <?php
        echo "<form action=\"" . $this->pageURL . "\" method=\"post\" id=\"registrace\"
            onsubmit=\"javascript:return validace_formulare('" . $prekladac->vloz_retezec('format_emailu') . "', '" . $prekladac->vloz_retezec('shoda_hesla') . "' , '" .
                $prekladac->vloz_retezec('slabe_heslo') . "', '" . $prekladac->vloz_retezec('kratke_heslo') . "', '" . $prekladac->vloz_retezec('slabe_heslo') . "' , '" .
                            $prekladac->vloz_retezec('dostatecne_heslo') . "', '" . $prekladac->vloz_retezec('silne_heslo') . "');\" style=\"text-align: center;\">";
        ?>
        <div class="form_text"> <?php echo $this->prekladac->vloz_retezec('login_hrace'); ?> </div>
        <div class="form_field"><input type="text" class="form_field_ods" name="login_hrace" id="login_hrace" value="<?php echo $data_z_formulare_registrace['login_hrace']; ?>" /></div>
        <div class="form_text"> <?php echo $this->prekladac->vloz_retezec('emailova_adresa'); ?> </div>
        <div class="form_field"><input type="text" class="form_field_ods" name="emailova_adresa" id="emailova_adresa" value="<?php echo $data_z_formulare_registrace['emailova_adresa']; ?>" /></div>
        <div class="form_text"> <?php echo $this->prekladac->vloz_retezec('heslo'); ?> </div>
        <div class="form_field"><input type="password" class="form_field_ods" name="heslo" id="heslo" 
        onkeyup="<?php echo "kontrola_sily_hesla('" . $prekladac->vloz_retezec('kratke_heslo') . "', '" . $prekladac->vloz_retezec('slabe_heslo') . "' , '" .
                            $prekladac->vloz_retezec('dostatecne_heslo') . "', '" . $prekladac->vloz_retezec('silne_heslo') . "');" ?> " /></div>
        <div class="form_text"><span id="strength"></span></div>
        <div class="form_text"> <?php echo $this->prekladac->vloz_retezec('kontrola_hesla'); ?> </div>
        <div class="form_field"><input type="password" class="form_field_ods" name="heslo_2" id="heslo_2" /></div>
        <div class="form_text"> <?php echo $this->prekladac->vloz_retezec('jazyk'); ?> </div>
        <div class="form_field"><?php $this->vygeneruj_vyber_jazyka(); ?></div>
        <div class="form_text" style="height: 100px; "></div><div class="form_field" style="height: 100px; "><img src="captcha.php?.png" alt="CAPTCHA" /></div>
        <div class="form_text"> <?php echo $this->prekladac->vloz_retezec('opiste_text'); ?> </div>
        <div class="form_field"><input type="text" class="form_field_ods" name="captchastring" id="captchastring" /></div>
        <input type="hidden" name="typ_formulare" value="registrace" />
        <input type="submit" class="button" name="tlacitko" value="<?php echo $this->prekladac->vloz_retezec('potvrdit'); ?>" />
        </div>
        </div>
        </div>
        </div>
        <?php
    }


    public function generuj_formular_zapojeni_do_hry() {
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

        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"zapojeni\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"Zapojit se do hry\"/>";
            echo "</p>";
        echo "</form>";
    }

    public function  __construct($data_z_formulare_registrace = null) {
        $this->data_z_formulare_registrace = $data_z_formulare_registrace;
        parent::__construct();
    }
}
?>
