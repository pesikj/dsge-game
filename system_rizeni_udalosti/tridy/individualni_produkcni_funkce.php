<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of individualni_produkcni_funkce
 *
 * @author Jiří Pešík
 */
class individualni_produkcni_funkce {
    //put your code here

    private $login;
    private $mnozstvi_prace;
    private $mnozstvi_kapitaloveho_zbozi;
    private $kolo;
    private $druh_zbozi;
    private $velikost_produkce;

    private $hrac;

    /**
     *
     * @param <type> $login
     * @param <type> $mnozstvi_prace
     * @param <type> $mnozstvi_kapitaloveho_zbozi
     * @param <type> $kolo
     * @param <type> $druh_zbozi 1 znamená spotřební zboží a 2 kapitálové zboží
     */
    function __construct($login, $mnozstvi_prace, $mnozstvi_kapitaloveho_zbozi, $kolo, $druh_zbozi) {
        $this->mnozstvi_prace = $mnozstvi_prace;
        $this->mnozstvi_kapitaloveho_zbozi = $mnozstvi_kapitaloveho_zbozi;
        $this->kolo = $kolo;
        $this->druh_zbozi = $druh_zbozi;

        $this->velikost_produkce = $this->vypocti_velikost_produkce();
        $this->login = $login;
        $this->hrac = new hrac($login);
    }

    /**
     * Zavolá statickou metodu a z dat instance vypočte velikost produkce.
     * @return <type> velikost produkce dané instance zaokrouhlená podle matematických pravidel
     */
    public function vypocti_velikost_produkce() {
        $velikost_produkce = self::produkcni_funkce($this->mnozstvi_prace, $this->mnozstvi_kapitaloveho_zbozi, $this->kolo);
        return round($velikost_produkce);
    }

    public function vygeneruj_a_proved_dotaz_na_upravdu_databaze() {
        $dotaz_na_pridani_zaznamu_o_vyrobe = "INSERT INTO zaznamy_o_produkci VALUES ( '" . $this->login . "', " .
            $this->kolo . ", ";

        if ($this->druh_zbozi == 1) {
            $this->hrac->zvys_mnozstvi_spotrebniho_zbozi($this->velikost_produkce);
            $dotaz_na_pridani_zaznamu_o_vyrobe .= $this->velikost_produkce . ", 0 ); ";
        } else if ($this->druh_zbozi == 2) {
            $this->hrac->zvys_mnozstvi_kapitaloveho_zbozi($this->velikost_produkce);
            $dotaz_na_pridani_zaznamu_o_vyrobe .=  "0, " . $this->velikost_produkce . "); ";
        }

        mysql_query($dotaz_na_pridani_zaznamu_o_vyrobe) or die ($dotaz_na_pridani_zaznamu_o_vyrobe);
    }

    /**
     * Reprezentuje individuální produkční funkci, která je základem celého systému. Její parametry jsou pro všechny stejné,
     * proto je implementovaná jako statická metoda. Je také volána při generování grafu (to, že je statická, umožňuje její
     * volání bez vytváření instance třídy.
     * @param <type> $mnozstvi_prace množství práce zapojené do produkce
     * @param <type> $mnozstvi_kapitaloveho_zbozi množství kapitálového zboží zapojeného do výroby
     * @param <type> $kolo aktuální kolo, důležité kvůli exogennímu technologickému pokroku
     * @return <type> velikost produkce jako racionální číslo
     */
    public static function produkcni_funkce($mnozstvi_prace, $mnozstvi_kapitaloveho_zbozi, $kolo) {
        $rovnice = new EvalMath();
        $spravce_konfigurace = new spravce_konfigurace();
        $retezec_rovnice = $spravce_konfigurace->get_produkcni_funkce();
        $retezec_rovnice = str_replace("kolo", $kolo, $retezec_rovnice);
        $retezec_rovnice = str_replace("mnozstvi_kapitaloveho_zbozi", $mnozstvi_kapitaloveho_zbozi, $retezec_rovnice);
        $retezec_rovnice = str_replace("mnozstvi_prace", $mnozstvi_prace, $retezec_rovnice);

        $vysledek = $rovnice->evaluate($retezec_rovnice);
        return $vysledek;
    }



}
?>
