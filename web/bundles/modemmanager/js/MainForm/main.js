$(document).ready(function(){   
    //Подключение модуля сортировки таблиц
    $("#tsorter").tablesorter({ 
        headers: { 
            0: { 
                sorter: false 
            }
        } 
    });
    $("#checkall").click(function(){
        checkAllStatus = $("#checkall")[0].checked;
        console.log(checkAllStatus);
        $('.checker').each(function(){
            if (checkAllStatus == false) {
                $(this)[0].checked = false;
            } else {
                $(this)[0].checked = true;
            }
        });
    });
    //Индикация статуса модемов
    $('.modem_status').each(function(index){
        if ($(this) !== undefined) {
            if ("IN1" === $(this).text()) {
                $(this).css('background-color', "#F08080");
            }
            if ("IN2" === $(this).text()) {
                $(this).css('background-color', "#90EE90");
            }
        }
    });
    $('#deletemodem').click(function(){
        var url=$("#deletemodem").attr("action");
        var attr = new Array();
        $('.checker:checked').each(function( index ){
            if ($(this)) {
                attr.push($(this).attr("id"));
            }
        });
        $.post(url, {
            ids: attr,
            success: function() {
                $('.checker:checked').each(function( index ){
                    if ($(this)) {
                        $(this).parent().parent().fadeOut('slow');
                    }
                });
            }
        });
    });
    $('#sendsms').click(function(){
        var url=$("#sendsms").attr("action");
        var attr = new Array();
        $('.checker:checked').each(function( index ){
            if ($(this)) {
                attr.push($(this).attr("id"));
            }
        });
        $.post(url, {
            ids: attr
        });
        alert('SMS Подготовлены к отправке');
    });
    $('.activeRowModem').dblclick(function(){
        id = $(this).parents('.activeRowModem').context.id;
        var url = $('.activeRowModem').attr('action') + id;
        $(location).attr('href',url);
    });
    
    $('.activeRowModemGroup').dblclick(function(){
        id = $(this).parents('.activeRowModemGroup').context.id;
        var url = $('.activeRowModemGroup').attr('action') + id;
        $(location).attr('href',url);
    });
    
    $('.activeRowSchedule').dblclick(function(e){
        id = $(this).parents('.activeRowSchedule').context.id;
        var url = $('.activeRowSchedule').attr('action') + id;
        $(location).attr('href',url);
    });
    
    $('.activeTimeblockRow').click(function(){
        //$(this).fadeOut('slow',function(){});
    });

    $('#addstring').click(function(){
        $('.timeblocks').append('<tr><td>HelloWorld</td></tr>');
    });

});