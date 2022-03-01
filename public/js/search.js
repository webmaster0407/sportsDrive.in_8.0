
$(document).ready(function() {

    //setup before functions
    var typingTimer;                //timer identifier
    var doneTypingInterval = 750;  //time in ms (5 seconds)

    //on keyup, start the countdown
    $('#searchKeyword').on('keyup', function () {
        var str = $(this).val();
        var strlen = str.length;
        if(strlen == "0")//if no keyword is there then hide the poup
            $("#searchList").hide();
        if(strlen > 2){//minimum length should be 3 to search
            clearTimeout(typingTimer);
            if ($('#searchKeyword').val()) {
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            }
        }
    });

    //user is "finished typing," do something
    function doneTyping () {
        var cat=$('#categoryList').val();
        var all=$('.all-left').width();
        var str = $("#searchKeyword").val();
        var suggestList=$("#searchList");
        suggestList.css({left:all})
        $.ajax({
            type: "get",
            url: "/search?keyword="+str+"&cat="+cat,
            success: function (data) {
                suggestList.html(data);
                suggestList.show();
            }
        });
    }

    $(document).on("click", '#close-search-box', function(e) {
        $("#searchList").hide();
        $("#searchKeyword").val('');
        $("#searchKeyword").trigger('focus');
        e.preventDefault();
    });


});

