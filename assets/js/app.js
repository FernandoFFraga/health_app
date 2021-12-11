function fitBlocks(){
    $(".controllers button").css("height", $(".controllers button").css("width"))
    
    $(".controllers button").each(function(){
        var status = $(this).attr("data-value");
        if (status == 1) {
            $(this).addClass("checked");
        }
    })
}

$(".controllers button").click(function(){
    var status = $(this).attr("data-value");
    if (status == 1) {
        $(this).removeClass("checked");
        status = 0;
    } else {
        $(this).addClass("checked");
        status = 1;
    }
    
    $(this).attr("data-value", status);

    var params = {
        training: $("#ctrl-training").attr("data-value"),
        water: $("#ctrl-water").attr("data-value"),
        food: $("#ctrl-food").attr("data-value"),
        sleep: $("#ctrl-sleep").attr("data-value"),
        day: day
    };

    updateDB(params);
})


function updateDB(params){
    $.ajax({
        url : "executor.php",
        type : 'post',
        data : params
   })
   .done(function(response){
        console.log(response);
   })
   .fail(function(jqXHR, textStatus, response){
        console.log(response);
   });
}

$("#ctrl-prev-date").click(function(){
    var url = basepath + "/" + day + "/PREV";
    window.location=url;
});

$("#ctrl-next-date").click(function(){
    var url = basepath + "/" + day + "/NEXT";
    window.location=url;
});

fitBlocks()