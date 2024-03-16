<?php

    function fileManager($file,$lines){
        //Link para o arquivo
        $arqEspelho = file($file);

        $linhaIn = $lines[0]; //Linha Inciial do cupom
        $linhaOut = $lines[1]; //Linha final do cupom

        $error = null; //se houver erro, reportar

        try{
            $fileout = fopen('CupomOut/cupom.txt','w+'); //Inicia um novo arquivo de saida ou zera o atual
        }
        catch(Exception $e){
            $error = $e->getMessage();

        }

        for($ln = $linhaIn; $ln <= $linhaOut; $ln++){
            fwrite($fileout,'<p>');
            fwrite($fileout,$arqEspelho[$ln]); //Escreve a linha do arqEspelho no arquivo de saida
            fwrite($fileout,'<p>');
        }
        fclose($fileout);

        if($error == null){
            return true;
        }else return false;
        
    }

?>