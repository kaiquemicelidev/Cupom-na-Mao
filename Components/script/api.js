function openCupom(date, pdv, lin, lout){
    console.log(pdv);
    $.ajax({
        url: 'http://localhost/Cupom-na-Mao/API/index.php?type=select&date=' + date + '&file=' + pdv +
        '&lineIn=' + lin + '&lineOut=' + lout,
        type: 'GET',
        dataType: 'json',
        success: function(Resp) {
            if(Resp.status){
                $(".cpnView").load("API/CupomOut/cupom.txt");
                $(".cpnView").css("visibility","visible");
                $(".cpnView").append("<div class='btnDownload'>Download</div>");
            }
        }
    });

}


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

                let ListCount = Result.ListCount;
                $(".cpnList").append(
                    "<div class='ListCount'>Número de Resultados: " + Result.ListCount + "</div>"
                );
                if(Result.ListCount > 0){
                    var CupomUrl = Result.FileUrl;
                    let CupomList = Result.CupomList;
                    let FileDate = Result.FileDate;
                    let FilePdv = Result.FilePdv;
                    CupomList.forEach(Cupom => {
                        let CupomKey = Cupom.CupomKey;
                        let CupomIn = Cupom.Prop.CupomIn;
                        let CupomOut = Cupom.Prop.CupomOut;
                        let CupomPay = Cupom.Prop.CupomPay;
                        let CupomValue = Cupom.Prop.CupomValue;
                        let CupomDate = Cupom.Prop.CupomDate;

                        $(".cpnList").append(
                            "<div class='CupomItem' " +
                            "onClick=openCupom('" + FileDate + "','" + FilePdv + 
                                                "','" + CupomIn + "','" + CupomOut + "')>" +
                            "<h3>" + CupomKey + "</h3>" +
                            "<h4>" + CupomValue + "</h4>" +
                            "<h4>" + CupomPay + "</h4>" +
                            "<h4>" + CupomDate + "</h4>" +
                            "</div>"
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
