$('.modemstatus').children('tbody').children('tr').children('.smstext').each(function(index, value){
    if ($(this).html().indexOf('IN2') > 0) {
        $(this).css('background-color','#90EE90');
    } else {
        $(this).css('background-color','#F08080');
    }
});

