<?php


class html_struktura {
    public static function vloz_xhtml_hlavicku() {
        ?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
            <head>
              <link rel="stylesheet" type="text/css" href="html/styl.css" />
              <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
                    <title>DSGE Game  </title>

            </head>
                <body>

        <?php
    }

    public static function vloz_menu(hrac $hrac) {
        ?>
            <div id="prave_menu">
                <p>Příhlášen jako: <b><?php echo $hrac->getLogin(); ?></b><br />
                    <?php if (getenv("REMOTE_USER") == false) {
                        echo "<a href='inc/odhlaseni.php'>Odhlásit</a>";
                    }
                    ?>
                    </p>
                <ul>
                        <li><a href="index.php?id_stranky=prehled">Přehled</a></li>
                        
                </ul>
                <ul>
                        <li><a href="index.php?id_stranky=produkce">Vlastní produkce</a></li>
                        
                </ul>
                <ul>
                        <li><a href="index.php?id_stranky=trh&amp;id_trhu=trh_prace">Trh práce</a></li>
                        <li><a href="index.php?id_stranky=trh&amp;id_trhu=trh_kapitaloveho_zbozi">Trh kapitálového zboží</a></li>
                        <li><a href="index.php?id_stranky=trh&amp;id_trhu=trh_kapitalu_2_obdobi">Trh kapitálu (2 období)</a></li>
                        <li><a href="index.php?id_stranky=trh&amp;id_trhu=trh_spotrebniho_zbozi">Trh spotřebního zboží</a></li>
                </ul>
                <ul>
                        <li><a href="index.php?id_stranky=prognoza">Prognóza finančních toků</a></li>

                </ul>
                <ul>
                        <li><a href="index.php?id_stranky=aktualni_stav">Aktuální stav na trzích</a></li>
                        <li><a href="index.php?id_stranky=administrace">Administrace</a></li>
                        <li><a href="index.php?id_stranky=administrace_editor">Editor úvodní stránky</a></li>
                        <li><a href="index.php?id_stranky=seznam_hracu">Seznam hráčů</a></li>
                        <li><a href="index.php?id_stranky=export_dat">Export dat</a></li>

                </ul>
            </div>
        <?php
    }


    public static function vloz_horizontalni_pruh (hrac $hrac, $cislo_aktualniho_kola) {
    ?>
            <div id="stavovy_radek">
                    <ul>
                            <li>Finanční hotovost:
                                <?php
                                    echo $hrac->getMnozstvi_kapitalu();
                                ?>
                            </li>
                            <li>Spotřební statky:
                                <?php
                                    echo $hrac->getMnozstvi_spotrebniho_zbozi();
                                ?>
                            </li>
                            <li>Kapitálové statky na skladě:
                                <?php
                                    echo $hrac->getMnozstvi_kapitaloveho_zbozi();
                                ?>
                            </li>
                            <li>Ve výrobě:
                                <?php
                                    echo $hrac->getMnozstvi_kapitaloveho_zbozi_ve_vyrobe();
                                ?>
                            </li>
                            <li>Počet bodů:
                                <?php
                                    echo $hrac->get_pocet_bodu();
                                ?>
                            </li>
                            <li> Aktuální kolo:
                                <?php
                                    echo $cislo_aktualniho_kola;
                                ?>
                            </li>
                    </ul>
            </div>
    <?php
    }
    public static function vloz_paticku_stranky() {
        ?>
                    <div id="footer">
                        <p>Kontakt: <a href="mailto:pesikj@students.zcu.cz">Jiří Pešík</a> </p>
                    </div>
                    </div>
                </body>
            </html>
        <?php

    }
}
    ?>