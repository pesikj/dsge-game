<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    require_once ("jpgraph/jpgraph.php");
    require_once ("jpgraph/jpgraph_line.php");
    require_once ("jpgraph/jpgraph_bar.php");
    require_once '../tridy/Prikaz.php';
    require_once '../tridy/komponenty_trhu/Trzni_prikaz.php';
    require_once '../tridy/individualni_produkcni_funkce.php';
    require_once '../tridy/Informace_o_ekonomikach.php';
    require_once '../inc/EvalMath.php';
    require_once '../inc/config.php';
    require_once '../inc/mysql_connect.php';
    require_once '../inc/config.php';
    require_once '../tridy/komponenty_trhu/individualni_poptavka.php';
    require_once '../tridy/komponenty_trhu/individualni_nabidka.php';

    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();
    session_start();

    $cislo_kola = $_GET['cislo_kola'];
    $id_trhu = $_GET['id_trhu'];
    $druh_prikazu = $_GET['druh_prikazu'];
    if (isset ($_SESSION['id_subjektu']) == false) {
        exit;
    }
    $id_subjektu = $_SESSION['id_subjektu'];

    if ($druh_prikazu == 'poptavka') {
        $individualni_poptavka = new individualni_poptavka($cislo_kola, $id_subjektu, $id_trhu);
        $individualni_poptavka->nacti_data_prikazu_z_databaze();
        $osa_x_max = $individualni_poptavka->get_nejvyssi_mezni_cena();
    } else if ($druh_prikazu == 'nabidka') {
        $individualni_nabidka = new individualni_nabidka($cislo_kola, $id_subjektu, $id_trhu);
        $individualni_nabidka->nacti_data_prikazu_z_databaze();
        $osa_x_max = $individualni_nabidka->get_nejvyssi_mezni_cena();
    }
    
    $osa_x_max = max(round ($osa_x_max*1.2, - 1), 50);

    $pole_grafu = array();
    $osa_x = array();

    for ($i = 1; $i <= $osa_x_max; $i++) {
        $osa_x[$i] = $i;
        if ($druh_prikazu == 'poptavka') {
            $pole_grafu[$i] = $individualni_poptavka->urci_poptavane_mnozstvi_pri_dane_cene($i);
        } else if ($druh_prikazu == 'nabidka') {
            $pole_grafu[$i] = $individualni_nabidka->urci_nabizene_mnozstvi_pri_dane_cene($i);
        }
    }


    $width = 840; $height = 200;
    $graph = new Graph($width,$height);
    $graph->SetScale('intint');
    $graph->title->Set($druh_prikazu);
    $graph->xaxis->title->Set('Trzni cena');
    $graph->xaxis->SetTickLabels($osa_x);
    $graph->yaxis->title->Set('Mnozstvi');

    $graf_poptavky = new BarPlot($pole_grafu);

    $graph->Add($graf_poptavky);

    $graph->Stroke();



?>
