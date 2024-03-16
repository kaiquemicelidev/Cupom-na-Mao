$("#button_go").on('click', function(){
    
    $.ajax({
        url: 'http://localhost/Cupom-na-Mao/API/index.php?type=search&date=' + 
        $('#date_select').val() + '&pdv=' + $("#pdv_select").val() + '&filter=' +
        $("#filter_txt").val(), 
        type: 'GET',
        dataType: 'json',
        success: function(Result) {
            
            
        }
    })
});