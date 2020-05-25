$(function(){

    $(document).on('keypress','.number',function(e){
        return isNumber(e);
    });
    $(document).on('keypress','.digit',function(e){
        return isNumberKey(e);
    });




    $('.select2').select2();

    ajaxFormSubmit();
});

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode == 46){
        var inputValue = $("#floor").val();
        var count = (inputValue.match(/'.'/g) || []).length;
        if(count<1){
            if (inputValue.indexOf('.') < 1){
                return true;
            }
            return false;
        }else{
            return false;
        }
    }
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

function scrollTo(element){
    $('html, body').animate({
        scrollTop: ($(element).offset().top)
    },500);
}

function ajaxFormSubmit()
{
    $(document).on('submit','form.ajaxform',function(e){


        e.preventDefault();
        toastr.info('...Sending');

        var postData = $(this).serialize();


        var formURL = $(this).attr("action");


        $.ajax(
            {
                url : formURL,
                type: "POST",
                data : postData,
                dataType: "json",
                //  contentType: "application/json",
                headers: {
                    Accept:"application/json"
                },
                success:function(data, textStatus, jqXHR)
                {

                    //data: return data from server
                    if(data.status)
                    {
                        toastr.success(data.message);
                    }
                    else
                    {
                        toastr.error(data.message);
                    }


                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    //if fails
                    toastr.error('Error', 'Submission Failed! Please try again later: '+errorThrown);
                }
            });



    });
}
