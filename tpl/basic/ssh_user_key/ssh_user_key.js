$('body').on("click",".un-list-body > div",function(){
    $('#caption_wait').html('<div class="pls-wait"><i class="fa fa-spinner fa-spin"></i> '+lan["hashmon_pleasewait"]+'</div>');
    var user = $(".un-current").text();
    var data = {
        'user':user,
    };
    $.ajax({
        url: 'index.php?do=ssh_user_key&subdo=get_key',
        data:data,
        type: "POST",
        success: function(data) {
            $("#user_key").show().html(data);
            $('#caption_wait').html('');
        }
    });
});


 $('body').on("click",".do_save",function(){
     $('#caption_wait').html('<div class="pls-wait"><i class="fa fa-spinner fa-spin"></i> '+lan["hashmon_pleasewait"]+'</div>');
     var user = $(".un-current").text();
     var content = $("#key_content").val();
     var data = {
         'user':user,
         'content':content,
     };
     $.ajax({
         url: 'index.php?do=ssh_user_key&subdo=do_save',
         data:data,
         type: "POST",
         success:function(data){
             $('#caption_wait').html('');
             var obj = $.parseJSON(data);
             show_modal(obj.message);
         }
     });
 });
