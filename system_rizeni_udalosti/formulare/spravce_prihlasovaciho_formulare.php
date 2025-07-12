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

    over_uzivatele();
    
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


        $dotaz = "SELECT * FROM hraci WHERE login ='" . $login . "' AND " .
            "heslo = SHA1('" . $heslo . "') ;";
        $vysledek = mysql_query($dotaz);
        if (mysql_num_rows($vysledek) == 1) {
            session_start();
            $_SESSION['auth'] = 1;
            $_SESSION['login'] = $login;
            header("Location: " . $adresa_serveru . "index.php");

            //poznamenej přihlášení
            $dotaz_na_zaznam_o_prihlaseni = "UPDATE hraci SET " .
                " posledni_prihlaseni = CURRENT_TIMESTAMP WHERE login = '" . $login ."';";
            mysql_query($dotaz_na_zaznam_o_prihlaseni);
        } else {
            header("Location: " . $adresa_serveru . "index.php");
        }
    }


    function overeni_ldap ($login) {
        $ds=ldap_connect("ldap.zcu.cz");  // must be a valid LDAP server!
//        echo "connect result is " . $ds . "<br />";

        if ($ds) {
//            echo "Binding ...";
            $r=ldap_bind($ds);     // this is an "anonymous" bind, typically
                                   // read-only access
//            echo "Bind result is " . $r . "<br />";

//            echo "Searching for (sn=S*) ...";
            // Search surname entry
            $sr=ldap_search($ds, "ou=rfc2307, o=zcu, c=cz", "uid=". $login ."");
//            echo "Search result is " . $sr . "<br />";
//
//            echo "Number of entires returned is " . ldap_count_entries($ds, $sr) . "<br />";
//
//            echo "Getting entries ...<p>";
            $info = ldap_get_entries($ds, $sr);
            if ($info["count"] != 1) {
                header("Location: " . $adresa_serveru . "index.php");
            }
//            echo "Data for " . $info["count"] . " items returned:<p>";

//            for ($i=0; $i<$info["count"]; $i++) {
//                echo "dn is: " . $info[$i]["dn"] . "<br />";
//                echo "first cn entry is: " . $info[$i]["cn"][0] . "<br />";
//                echo "first email entry is: " . $info[$i]["mail"][0] . "<br /><hr />";
//            }

//            echo "Closing connection";
            ldap_close($ds);

        } else {
            die ("Nelze se připojit k LDAP serveru.");
        }
    }

    

?>
