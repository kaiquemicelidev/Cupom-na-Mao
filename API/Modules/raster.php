<?php
//header("Content-type: application/json; charset=utf-8");


function rasterArqEspelho($file, $type, $filter){
    //Link para o arquivo importado na função
    $arqespelho = file($file);

    /*Criação das arrays para coleta dos dados
      - CupomKey = Numero do cupom
      - CupomIn = Linha inicial do cupom
      - CupomOut = Linha final do cupom
      - CupomPay = Forma de Pagamento
      - CupomValue = Valor do Cupom
      - CupomDate = Data/Hora da emissão do cupom
      */

      $cupomArray = array();

    /*Utilizando como ponto de referência a linha contendo a string 'MERCADO-X S.A.'
     Detalhe adicional: Para não se confundir com via Cupom de pagamento por Cartão
     que também tem a mesma string, iremos se referẽnciar também com uma linha anterior onde
     é necessário ter a string 'SAO CARLOS'*/

    $string_mercardox = 'MERCADO-X S.A.';
    $string_saocarlos = 'SAO CARLOS';

    /*Coletando número de linhas do arqespelho*/
    $count = count($arqespelho);

    $ln = 0; //Linha de referência
    $filterYes = false; //Se o usuario colocou filtro e se ele foiou nao localizado. Padrao é false.
    $cupomOpen = false; //Verifica se achou as referências necessárias para carregar info de cupom
    $searchCount = 0;   //Indica o numero de resultados
    while($ln < $count){
        if((preg_match("#$string_mercardox#",$arqespelho[$ln])) && 
        (preg_match("#$string_saocarlos#",$arqespelho[$ln - 1]))){
            if($cupomOpen == false){ //se nenhum cupom está em aberto, irá abrir
                $cupomOpen = true;

                $CupomIn = $ln - 1; //Pegando a referência da linha inicial deste cupom aberto recentemente



            }else{ //se um cupom estiver em aberto, irá pegar a referência da ult. linha (-2) e entao fechar
                $CupomOut = $ln - 2;
                $cupomOpen = false;


                /* Se o usuario colocou filtro, só irá acrescentar o que tiver com o FiltroYes ativado
                caso o usuario colocou Filtro e nao foi localizado, não irá acrescentar. Se o usuario
                não colocou filtro, adicionara todos os cupons localizados */

                if($type == 0){
                    $array = array(
                        'CupomKey' => $CupomKey,
                        'Prop' => array(
                            'CupomIn' => $CupomIn,
                            'CupomOut' => $CupomOut,
                            'CupomPay' => $CupomPay,
                            'CupomValue' => $CupomValue,
                            'CupomDate' => $CupomDate,
                        )
                    );
                    array_push($cupomArray,$array);
                    $searchCount++;
                }else{
                    if($filterYes == true){
                        $filterYes = false;

                        $array = array(
                            'CupomKey' => $CupomKey,
                            'Prop' => array(
                                'CupomIn' => $CupomIn,
                                'CupomOut' => $CupomOut,
                                'CupomPay' => $CupomPay,
                                'CupomValue' => $CupomValue,
                                'CupomDate' => $CupomDate
                            )
                        );
                        array_push($cupomArray,$array);
                        $searchCount++;
                    }
                }

            }
        }

            /*Conforme o array estiver rolando fora da referência verifica se existe cupom aberto, se sim
            continua procurando outras propriedades*/
            if($cupomOpen == true){
                /* Próximo passo agora é localizar a linha que contenha o número do cupom.
                Para agilizar a busca, será pulado 7 linhas, pois se sabe que é apenas info.
                do mercado (não necessário na busca) */
                //$ln = $ln + 7;

                //Abaixo irá filtrar somente os numeros da linha que representam o numero do cupom
                if(preg_match("#Extrato No.#",$arqespelho[$ln])){
                    $CupomKey = filter_var($arqespelho[$ln], FILTER_SANITIZE_NUMBER_INT);
                }
                
                /* Próximo passo é saber o valor total do cupom, então como referência será procurado
                a referência 'TOTAL R$' e pagando somente o valor numérico Double */
                if(preg_match('#TOTAL #',$arqespelho[$ln])){

                    //Abaixo estão aplicados os filtros de caracteres especiais do arquivo e de strings
                    $ValueFilterEspecialChar = filter_var($arqespelho[$ln], FILTER_SANITIZE_URL);
                    $charEliminate = array('G','W','T','O','A','L','R','$');
                    $newCharArray = array('');
                    $ValueFilterString = str_replace($charEliminate,$newCharArray,$ValueFilterEspecialChar);
                    
                    $CupomValue = trim(substr($ValueFilterString,-8));
                    
                }

                /* Este passo será utilizado se o usuário colocou algum filtro. Irá pegar a string
                como referência */

                if($type == 1){
                    if(preg_match("#$filter#",$arqespelho[$ln])){
                        $filterYes = true;
                    }
                }

                /* Passo seguinte é localizar a forma de pagamento tendo como refência
                'Dinheiro', 'Cartão de Debito' e 'Credito a Vista'. Para agilizar será pulado
                duas linhas da referencia anterior. */

                if(preg_match('#Dinheiro #',$arqespelho[$ln])) $CupomPay = 'Din'; 
                if(preg_match('#Cartao de Debito #',$arqespelho[$ln])) $CupomPay = 'Deb';
                if(preg_match('#Cartao de Credito #',$arqespelho[$ln])) $CupomPay = 'Cre';

                /* Então será localizado a data e hora que foi emitida o cupom pegando como referência a string 
                de operador,pularemos umas 15 linhas para agilizar, sabendo que são informações irrelevantes neste intervalo
                depois como referencia se */
                //$ln = $ln + 15;

                if(preg_match('#OPR:0#',$arqespelho[$ln])){
                    $CupomDate = trim(substr($arqespelho[$ln],-9));
                }

            }

        //Não menos importante, o acrescimo do LN
        $ln++;

    }//FIM DO WHILE

    return array(
        'ArrayCount' => $searchCount, //NUMERO DE RESULTADOS
        'CupomList' =>  $cupomArray,  //LISTA DE CUPONS DA PESQUISA
    );
    


}//FIM DA FUNCTION