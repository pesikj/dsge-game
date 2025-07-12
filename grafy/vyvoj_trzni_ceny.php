<?php

    require_once ("jpgraph/jpgraph.php");
    require_once ("jpgraph/jpgraph_line.php");
    require_once ("jpgraph/jpgraph_bar.php");
    require_once '../tridy/individualni_produkcni_funkce.php';
    require_once '../tridy/EvalMath.php';
    require_once '../udalosti/Trida_reagujici_na_udalosti.php';
    require_once '../inc/config.php';
    require_once '../inc/mysql_connect.php';
    require_once '../inc/mysql_connect.php';

    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();


    $id_trhu = $_GET['id_trhu'];

    $data_osa_x = array();
    $vyvoj_ceny = array();

    $dolni_hranice = 0;
    $spravce_konfigurace = new spravce_konfigurace();
    $kolo = $spravce_konfigurace->get_cislo_aktualniho_kola();

    $dotaz_na_vyvoj_trzni_ceny = "SELECT * FROM vyvoj_trznich_cen WHERE nazev_trhu = '" . $id_trhu . "' ORDER BY kolo; ";
    $vysledek_vyvoj_trzni_ceny = mysql_query($dotaz_na_vyvoj_trzni_ceny) or die ($dotaz_na_vyvoj_trzni_ceny);

    $max_cena = 0;
    while ($radek = mysql_fetch_array($vysledek_vyvoj_trzni_ceny)) {
        $data_osa_x[] = $radek['kolo'];
        $vyvoj_ceny[] = $radek['cena'];

        if ($radek['cena'] > $max_cena) {
            $max_cena = $radek['cena'];
        }
    }

    $width = 650; $height = 200;
    $graph = new Graph($width,$height);
    $graph->SetScale('intint', 0,$max_cena + round($max_cena * 0.1));
    $graph->title->Set('Vyvoj trzni ceny');
    $graph->xaxis->title->Set('Cislo kola');
    $graph->yaxis->title->Set('Trzni cena');
    $graph->xaxis->SetTickLabels($data_osa_x);
    $lineplot=new LinePlot($vyvoj_ceny);
    $graph->Add($lineplot);

    $graph->Stroke();


?>
