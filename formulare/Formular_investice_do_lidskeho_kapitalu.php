<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formular_investice_do_lidskeho_kapitalu
 *
 * @author pike
 */
class Formular_investice_do_lidskeho_kapitalu extends Formular {
    //put your code here

    protected $definovany_prikaz_investice_do_lidskeho_kapitalu;
    
    public function vygeneruj_a_vloz_formular() {
        ?>
        <h2><?php echo $this->prekladac->vloz_retezec("investice_do_lidskeho_kapitalu"); ?></h2>
        <div class="form">
            <form action="<?php echo $pageURL ?>" method="post" name="form_vyroba">
                <div class="form_radek">
                    <?php echo $this->prekladac->vloz_retezec("investovany_cas"); ?>
                    <input class="form_field" type="text" name="investovany_cas" value="<?php echo $this->definovany_prikaz_investice_do_lidskeho_kapitalu->get_investovany_cas(); ?>" />
                </div>
            <p>
                <input type="hidden" name="typ_formulare" value="investice_do_lidskeho_kapitalu" />
                <input class="ok" type="submit" name="tlacitko" value="<?php echo $this->prekladac->vloz_retezec("formular_potvrdit")?>" />
            </p>
        </form>
        </div>
        <?php
    }

    function  __construct(Prikaz_investice_do_lidskeho_kapitalu $definovany_prikaz_investice_do_lidskeho_kapitalu) {
        parent::__construct();
        $this->definovany_prikaz_investice_do_lidskeho_kapitalu = $definovany_prikaz_investice_do_lidskeho_kapitalu;
    }
}
?>
