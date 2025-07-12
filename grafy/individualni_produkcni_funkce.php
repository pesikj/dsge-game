<?php

    require_once ("jpgraph/jpgraph.php");
    require_once ("jpgraph/jpgraph_line.php");
    require_once ("jpgraph/jpgraph_bar.php");
    require_once '../tridy/individualni_produkcni_funkce.php';
    require_once '../tridy/EvalMath.php';
    require_once '../udalosti/Trida_reagujici_na_udalosti.php';
    require_once '../inc/config.php';
    require_once '../inc/mysql_connect.php';

    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();   

    $variabilni_vyrobni_faktor = $_GET['variabilni_vyrobni_faktor'];

    $data_osa_x = array();
    $produkce = array();
    $popis_osa_x = "";

    $dolni_hranice = 0;
    $horni_hranice = $_GET['osa_x_max'];
    $spravce_konfigurace = new spravce_konfigurace();
    $kolo = $spravce_konfigurace->get_cislo_aktualniho_kola();

    if ($variabilni_vyrobni_faktor == 'prace') {
        $mnozstvi_kapitaloveho_zbozi = $_GET['mnozstvi_fixniho_faktoru'];
        $popis_osa_x = "Hodin prace";
        $pom = 0;

        for ($i = $dolni_hranice; $i <= $horni_hranice; $i++) {
            $data_osa_x[] = $i;
            $produkce[] = individualni_produkcni_funkce::produkcni_funkce($i,
                $mnozstvi_kapitaloveho_zbozi, $kolo);
        }
    } else if ($variabilni_vyrobni_faktor == 'kapitalove_zbozi') {
        $hodin_prace = $_GET['mnozstvi_fixniho_faktoru'];
        $popis_osa_x = "Mnozstvi kapitaloveho zbozi";

        for ($i = $dolni_hranice; $i <= $horni_hranice; $i++) {
            $data_osa_x[] = $i;
            $produkce[] = individualni_produkcni_funkce::produkcni_funkce($hodin_prace,
                $i, $kolo);
        }
    }

    $width = 650; $height = 200;
    $graph = new Graph($width,$height);
    $graph->SetScale('intint');
    $graph->title->Set('Individualni produkcni funkce');
    $graph->xaxis->title->Set($popis_osa_x);
    $graph->yaxis->title->Set('Velikost produkce');
    $graph->xaxis->SetTickLabels($data_osa_x);
    $lineplot=new LinePlot($produkce);
    $graph->Add($lineplot);
    
    $graph->Stroke();


?>
