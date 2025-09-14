<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_volby_subjektu
 *
 * @author pike
 */
class Formular_volby_subjektu extends Formular{
    //put your code here

    private $hrac;
    
    public function __construct() {
        parent::__construct();
        $this->hrac = $GLOBALS['hrac'];
    }

    private function zjisti_subjekty_napojene_na_ucet_hrace() {
        $dotaz_na_ovladane_subjekty = "SELECT * FROM subjekty WHERE login_hrace = '" . $this->hrac->getLogin() . "'";
        $vysledek = mysql_query($dotaz_na_ovladane_subjekty) or die($dotaz_na_ovladane_subjekty);

        if (mysql_num_rows($vysledek) == 0) {
            return null;
        }

        $pole_subjektu = array();
        while ($radek = mysql_fetch_assoc($vysledek)) {
            $pole_subjektu[] = $radek['id_subjektu'];
        }

        return $pole_subjektu;
    }

    private function zjisti_subjekty() {
        $dotaz_na_ovladane_subjekty = "SELECT * FROM subjekty;";
        $vysledek = mysql_query($dotaz_na_ovladane_subjekty) or die($dotaz_na_ovladane_subjekty);

        if (mysql_num_rows($vysledek) == 0) {
            return null;
        }

        $pole_subjektu = array();
        while ($radek = mysql_fetch_assoc($vysledek)) {
            $pole_subjektu[] = $radek['id_subjektu'];
        }

        return $pole_subjektu;
    }

    public function vygeneruj_formular_volby_subjektu() {
        $pole_subjektu = $this->zjisti_subjekty_napojene_na_ucet_hrace();

        if ($pole_subjektu == null || $GLOBALS['hrac']->get_superadministratorska_prava() == true) {
            $this->vygeneruj_formular_vytvoreni_subjektu(true);
        }

        if ($pole_subjektu != null) {
            $this->vygeneruj_formular_volby_existujicich_subjektu($pole_subjektu);
        }
    }

    private function vygeneruj_formular_volby_existujicich_subjektu($pole_subjektu) {
        $prekladac = $GLOBALS['prekladac'];
        foreach ($pole_subjektu as $aktualni_id_subjektu) {
            $vyber .= "<option value=\"" . $aktualni_id_subjektu . "\">" . $aktualni_id_subjektu . "</option>";
        }
        ?>
        <div id="form">
        <div class="form_ods">

        <form action="<?php echo $pageURL; ?>" method="post">
        <div class="form_text"><?php echo $prekladac->vloz_retezec("id_subjektu"); ?></div>
        <div class="form_field"><select class="form_field_ods" name="id_subjektu"><?php echo $vyber; ?></select></div>
        <input type="hidden" name="typ_formulare" value="volba_existujiciho_subjektu" />
        <input class="button" type="submit" name="tlacitko" value="<?php echo $prekladac->vloz_retezec("formular_potvrdit"); ?>" />
        </form>
        </div>
        </div>
        <?php
        if ($GLOBALS['hrac']->get_superadministratorska_prava() == true) {
            $pole_subjektu = $this->zjisti_subjekty();
            unset($vyber);
            foreach ($pole_subjektu as $aktualni_id_subjektu) {
                $vyber .= "<option value=\"" . $aktualni_id_subjektu . "\">" . $aktualni_id_subjektu . "</option>";
            }
            echo "<div class=\"cleaner\" />";
            echo "<form action=\"" . $pageURL . "\" method=\"post\">";
                echo "<table border=\"0\" style=\"width: 100%\">";
                    echo "<tr>";
                        echo "<td>";
                            echo $prekladac->vloz_retezec("id_subjektu");
                        echo "</td>";
                        echo "<td>";
                            echo "<select name=\"id_subjektu\">";
                                echo $vyber;
                            echo "</select>";
                        echo "</td>";
                    echo "</tr>";
                echo "</table>";
                echo "<p>";
                    echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"volba_existujiciho_subjektu\" />";
                    echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit", array ()) . "\" />";
                echo "</p>";
            echo "</form>";
            echo "<div class=\"cleaner\" />";
        }
    }

    private function vygeneruj_formular_vytvoreni_subjektu($automaticky_nazev_subjektu) {
        $prekladac = $GLOBALS['prekladac'];

        echo "<p>" . $prekladac->vloz_retezec("formular_vytvoreni_subjektu_uvod") . "</p>";


        echo "<form action=\"" . $pageURL . "\" method=\"post\">";
            echo "<table border=\"0\" style=\"width: 100%\">";

                if ($automaticky_nazev_subjektu == false || $GLOBALS['hrac']->get_superadministratorska_prava() == true) {
                    echo "<tr>";
                        echo "<td>";
                            echo $prekladac->vloz_retezec("formular_vytvoreni_subjektu_id_subjektu");
                        echo "</td>";
                        echo "<td>";
                            echo "<input type=\"text\"  name=\"id_subjektu" . "\" />";
                        echo "</td>";
                    echo "</tr>";
                } else {
                    echo "<input type=\"hidden\" name=\"id_subjektu\" value=\"" . $GLOBALS['hrac']->get_login() . "\" />";
                }

                echo "<tr>";
                    echo "<td>";
                        echo $prekladac->vloz_retezec("formular_vytvoreni_subjektu_nazev_ekonomiky");
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"id_ekonomiky" . "\" />";
                    echo "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>";
                        echo $prekladac->vloz_retezec("formular_vytvoreni_subjektu_pristupovy_kod");
                    echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\"  name=\"pristupovy_kod" . "\" />";
                    echo "</td>";
                echo "</tr>";

                if ($GLOBALS['hrac']->get_superadministratorska_prava()) {
                    echo $this->generator_formularu->generuj_radek_tabulky_formulare($prekladac->vloz_retezec("druh_subjektu"),
                        "id_druhu_subjektu", $this->vygeneruj_vyber_druhu_subjektu(), "select");
                }

            echo "</table>";
            echo "<p>";
                echo "<input type=\"hidden\" name=\"typ_formulare\" value=\"vytvoreni_subjektu\" />";
                echo "<input type=\"submit\" name=\"tlacitko\" value=\"" . $prekladac->vloz_retezec("formular_potvrdit", array ()) . "\" />";
            echo "</p>";
        echo "</form>";
    }
}
?>
