<script type="text/javascript">
    
</script>

<form action="formulare/spravce_prihlasovaciho_formulare.php" method="post">
    <?php
        $spravce_konfigurace = $GLOBALS['spravce_konfigurace'];
        if (strstr($spravce_konfigurace->get_adresa_serveru() , "zcu.cz") != false) {
    ?>
            <h3>
                <a href="orion/orion_login.php">Studenti Západočeské univerzity - klikněte
                    <span style="color:red; ">sem</span></a>
            </h3>
    <?php
        }
    ?>
    <table border="0">
        <tr>
            <th colspan="2">
                Přihlášení pro hráče mimo ZČU
            </th>
        </tr>
        <tr style="text-align:center">
            <td>
                Login:
            </td>
            <td>
                <input type="text" name="login" />
            </td>
        </tr>
        <tr style="text-align:center">
            <td>
                Heslo: 
            </td>
            <td>
                <input type="password" name="heslo" />
            </td>
        </tr>
        <tr style="text-align:center">
            <td colspan="2">
                <input type="submit" value="Odeslat" />
            </td>
        </tr>
    </table>
    <h3 style="text-align:center;">
        <a href="index.php?id_stranky=registrace">Registrace</a>
    </h3>
</form>
