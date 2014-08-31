function updateModemList() {
    attr = new Array();
    $('.modem_id').each(function(){
        attr.push($(this).html());
    });
    
    $.post("/modemlist/updatemodemstatus/",{  
        data: attr
        },
        function(data){
            for (var i in data) {
                data[i].id;
                data[i].status;
                $('.modem_id').each(function(){
                    if ($(this).html() == data[i].id ) {
                        if ("IN1" == data[i].status) {
                            $(this).parent().children('.modem_status').html(data[i].status);
                            $(this).parent().children('.modem_status').css('background-color', '#F08080');
                        } else if ("IN2" == data[i].status){
                            $(this).parent().children('.modem_status').html(data[i].status);
                            $(this).parent().children('.modem_status').css('background-color', '#90EE90');
                        }
                    };
                });
            }
        },"json"
    );
    
}
updateModemList();
setInterval('updateModemList()',10000);