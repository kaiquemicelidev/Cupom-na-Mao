<?php header('Access-Control-Allow-Origin: *'); ?>
<?php

/************************************************
 * 
 * API responsável por receber e enviar dados
 * de pesquisa e envio de dados de cupom
 * e criação de arquivo espelho.
 * 
 * Developer: Kaique Miceli
 * Criação Ideologica: 12/06/2021.
 * Update p/ Portifólio: 16/03/2024.
 *
 * **********************************************
*/

header("Content-type: application/json; charset=utf-8");


//IMPORTAÇÃO DA FUNÇÃO DE RASTREAMENTO
include_once 'Modules/raster.php';

if(isset($_GET['type'])){
    //SE EXISTE O GET TYPE, A API É VALIDA

    if($_GET['type'] === 'search'){
        //SE O TIPO FOR PESQUISA

        $date = $_GET['date']; // DATA
        $pdv = $_GET['pdv']; // NUMERO DO PDV
        $filter = $_GET['filter']; //SE EXISTE PALAVRA KEY
        
        if(strlen($filter) > 2){ //SÓ SERÁ VÁLIDA KEY SE TIVER 3 OU MAIS CARACTERES
            $search_type = 1; //O TIPO DE PESQUISA SERA 1 (COM FILTRO KEY)
        }else{
            $search_type = 0; //PESQUISA SEM FILTRO KEY
            $filter = '';
        } 

        $dateexplode = explode('-',$date); //FILTRANDO A DATA
        $datefolder = $dateexplode[2].'-'.$dateexplode[1];

        $fileurl = "Files/$datefolder/arqEspelho.p$pdv"; // CRIANDO URL DO ARQUIVO

        if(file_exists($fileurl)){
            //SE O ARQUIV EXISTIR PEGA O RESULTADO E IMPRIME JSON

            $r = rasterArqEspelho($fileurl,$search_type,$filter);
            echo json_encode(array(
                'FileExists' => true,
                'ListCount' => $r['ArrayCount'],
                'CupomList' => $r['CupomList']
            ));

        }else{
            //SE ARQUIVO NÃO EXISTIR
            echo json_encode(array(
                'FileExists' => false
            ));
        }

    }

}


?>