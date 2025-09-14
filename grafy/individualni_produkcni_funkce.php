<?php

    require_once ("jpgraph/jpgraph.php");
    require_once ("jpgraph/jpgraph_line.php");
    require_once ("jpgraph/jpgraph_bar.php");;
    require_once '../inc/EvalMath.php';
    require_once '../inc/config.php';
    require_once '../inc/mysql_connect.php';
    require_once '../inc/config.php';
    require_once '../tridy/Informace_o_ekonomikach.php';
    $mysql_connection = new mysql_connection();
    $mysql_connection->otevri_pripojeni();

    $variabilni_vyrobni_faktor = $_GET['variabilni_vyrobni_faktor'];
    $id_ekonomiky = $_GET['id_ekonomiky'];
    $oznaceni_produkcni_funkce = $_GET['oznaceni_produkcni_funkce'];
    $cislo_kola = $_GET['cislo_kola'];
    $data_osa_x = array();
    $produkce = array();
    $dolni_hranice = 0;
    if (is_numeric($_GET['osa_x_max'])) {
        $horni_hranice = $_GET['osa_x_max'];
    } else {
        $horni_hranice = 100;
    }
    $evalmath = new EvalMath();
    $informace_o_ekonomikach = Informace_o_ekonomikach::get_informace_o_ekonomikach();
    $produkcni_funkce = $informace_o_ekonomikach->get_produkcni_funkce_spotrebni_zbozi($id_ekonomiky);
    if ($variabilni_vyrobni_faktor == 'prace') {
        $mnozstvi_kapitaloveho_zbozi_ve_vyrobe = $_GET['mnozstvi_fixniho_faktoru'];
        $popis_osa_x = "Hodin prace";
        for ($i = $dolni_hranice; $i <= $horni_hranice; $i++) {
            $data_osa_x[] = $i;
            $produkcni_funkce_dosazeni = $produkcni_funkce;
            $produkcni_funkce_dosazeni = str_replace("cislo_kola", $cislo_kola, $produkcni_funkce_dosazeni);
            $produkcni_funkce_dosazeni = str_replace("mnozstvi_prace_ve_vyrobe", $i, $produkcni_funkce_dosazeni);
            $produkcni_funkce_dosazeni = str_replace("mnozstvi_kapitaloveho_zbozi_ve_vyrobe", $mnozstvi_kapitaloveho_zbozi_ve_vyrobe, $produkcni_funkce_dosazeni);
            $produkcni_funkce_dosazeni = str_replace(",", ".", $produkcni_funkce_dosazeni);
            $produkce[] = $evalmath->evaluate($produkcni_funkce_dosazeni);
        }
    } else {
        $mnozstvi_prace_ve_vyrobe = $_GET['mnozstvi_fixniho_faktoru'];
        $popis_osa_x = "Mnozstvi kapitaloveho zbozi";
        for ($i = $dolni_hranice; $i <= $horni_hranice; $i++) {
            $data_osa_x[] = $i;
            $produkcni_funkce_dosazeni = $produkcni_funkce;
            $produkcni_funkce_dosazeni = str_replace("cislo_kola", $cislo_kola, $produkcni_funkce_dosazeni);
            $produkcni_funkce_dosazeni = str_replace("mnozstvi_prace_ve_vyrobe", $mnozstvi_prace_ve_vyrobe, $produkcni_funkce_dosazeni);
            $produkcni_funkce_dosazeni = str_replace("mnozstvi_kapitaloveho_zbozi_ve_vyrobe", $i, $produkcni_funkce_dosazeni);
            $produkcni_funkce_dosazeni = str_replace(",", ".", $produkcni_funkce_dosazeni);
            $produkce[] = $evalmath->evaluate($produkcni_funkce_dosazeni);
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
