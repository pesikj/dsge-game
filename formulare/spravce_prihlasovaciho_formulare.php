<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    function __autoload($class_name) {
        if (file_exists('../udalosti/' . $class_name . '.php')) {
            require_once ('../udalosti/' . $class_name . '.php');
        }
        if (file_exists('../tridy/' . $class_name . '.php')) {
            require_once ('../tridy/' . $class_name . '.php');
        }
        if (file_exists('../tridy/spravci_GUI/' . $class_name . '.php')) {
            require_once ('../tridy/spravci_GUI/' . $class_name . '.php');
        }
        if (file_exists('../formulare/' . $class_name . '.php')) {
            require_once ('../formulare/' . $class_name . '.php');
        }
    }
    if (isset ($_GET['session_id']) == false) {
        over_uzivatele();
    } else {
        require_once '../inc/mysql_connect.php';
        require_once '../inc/config.php';
        $mysql_connection = new mysql_connection();
        $spojeni = $mysql_connection->otevri_pripojeni();
        $spravce_konfigurace = new spravce_konfigurace();
        if ($spravce_konfigurace->get_parametry_cookie() != null) {
            $parametry_cookie = $spravce_konfigurace->get_parametry_cookie();
            session_set_cookie_params(time() + 60*60, $parametry_cookie['path'], $parametry_cookie['domain']);
        }
        session_start();
        $adresa_serveru = $spravce_konfigurace->get_adresa_serveru();
        over_napojene_subjekty($_SESSION['login']);
        header("Location: " . $adresa_serveru . "index.php");
    }


    
    function over_uzivatele() {
        require_once '../inc/mysql_connect.php';
        require_once '../inc/config.php';
        $mysql_connection = new mysql_connection();
        $spojeni = $mysql_connection->otevri_pripojeni();

        $spravce_konfigurace = new spravce_konfigurace();
        $adresa_serveru = $spravce_konfigurace->get_adresa_serveru();

        $login = $_POST['login'];
        $heslo = $_POST['heslo'];

        $login = trim($login);
        $heslo = trim($heslo);

        $login = addslashes($login);
        $heslo = addslashes($heslo);

        //overeni_ldap($login);


        $dotaz = "SELECT * FROM hraci WHERE login_hrace ='" . $login . "' AND " .
            "heslo = SHA1('" . $heslo . "') ;";
        $vysledek = mysql_query($dotaz) or die ($vysledek);
        if (mysql_num_rows($vysledek) == 1) {
            session_start();
            $_SESSION['auth'] = 1;
            $_SESSION['login'] = $login;
            over_napojene_subjekty($login);
            header("Location: " . $adresa_serveru . "index.php");
        } else {
            header("Location: " . $adresa_serveru . "index.php");
        }
    }

    function over_napojene_subjekty($login) {
        $dotaz_na_napojene_subjekty = "SELECT * FROM subjekty WHERE login_hrace = '" . $login ."'; ";
        $vysledek_napojene_subjekty = mysql_query($dotaz_na_napojene_subjekty) or die($dotaz_na_napojene_subjekty);

        $dotaz_na_zapojeni_hrace_do_hry = "SELECT * FROM hraci WHERE login_hrace = '" . $login. "';";
        $vyledek_zapojeni_hrace_do_hry = mysql_query($dotaz_na_zapojeni_hrace_do_hry) or die ($dotaz_na_zapojeni_hrace_do_hry);

        if (mysql_num_rows($vysledek_napojene_subjekty) == 1 ) {
            $radek_zapojeni_do_hry = mysql_fetch_assoc($vyledek_zapojeni_hrace_do_hry);
            
            if ($radek_zapojeni_do_hry['superadministratorska_prava'] == 0) {
                $radek_napojene_subjekty = mysql_fetch_assoc($vysledek_napojene_subjekty);
                $_SESSION['id_subjektu'] = $radek_napojene_subjekty['id_subjektu'];
            }
        }
    }


    function overeni_ldap ($login) {
        $ds=ldap_connect("ldap.zcu.cz");  // must be a valid LDAP server!
        if ($ds) {
            $r=ldap_bind($ds);     // this is an "anonymous" bind, typically
            $sr=ldap_search($ds, "ou=rfc2307, o=zcu, c=cz", "uid=". $login ."");
            $info = ldap_get_entries($ds, $sr);
            if ($info["count"] != 1) {
                header("Location: " . $adresa_serveru . "index.php");
            }
            ldap_close($ds);

        } else {
            die ("Nelze se připojit k LDAP serveru.");
        }
    }

    

?>
