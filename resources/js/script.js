
$(document).ready(function(){
    $('tbody').find('tr').on('click',function(){
        $('tbody').find('tr').removeClass('active');
        $(this).addClass('active');
    });
});
