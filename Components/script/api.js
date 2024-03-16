$("#button_go").on('click', function(){

    $(".cpnList").html("");
    
    $.ajax({
        url: 'http://localhost/Cupom-na-Mao/API/index.php?type=search&date=' + 
        $('#date_select').val() + '&pdv=' + $("#pdv_select").val() + '&filter=' +
        $("#filter_txt").val(), 
        type: 'GET',
        dataType: 'json',
        success: function(Result) {
            if(Result.FileExists){ //O arquivo existe
                console.log('Existe');
                let ListCount = Result.ListCount;
                $(".cpnList").append(
                    "<div class='ListCount'>Número de Resultados: " + Result.ListCount + "</div>"
                );
                if(Result.ListCount > 0){
                    let CupomList = Result.CupomList;
                    CupomList.forEach(Cupom => {
                        let CupomKey = Cupom.CupomKey;
                        let CupomIn = Cupom.Prop.CupomIn;
                        let CupomOut = Cupom.Prop.CuponOut;
                        let CupomPay = Cupom.Prop.CupomPay;
                        let CupomValue = Cupom.Prop.CupomValue;
                        let CupomDate = Cupom.Prop.CupomDate;

                        $(".cpnList").append(
                            "<div class='CupomItem'>" +
                            "<h3>" + CupomKey + "</h3>" +
                            "<h4>" + CupomValue + "</h4>" +
                            "<h4>" + CupomPay + "</h4>" +
                            "<h4>" + CupomDate + "</h4>" 
                            );
                        
                    });
                }

            }else{ // O arquivo não existe
                $(".cpnList").append(
                    "<div class='ListCount'>ArqEspelho inexistente</div>"
                );
            }
            
        }
    })
});