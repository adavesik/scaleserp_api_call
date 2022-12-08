$(document).ready(function() {
    //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
    $(".add-more").click(function() {
        var html = $(".copy-fields").html();
        $(".after-add-more").after(html);
    });
    //here it will remove the current value of the remove button which has been pressed
    $("body").on("click", ".remove", function() {
        $(this)
            .parents(".control-group")
            .remove();
    });

    // Submit has been pressed
    $('#submit').click(function(){

        $.ajax({
            url:"action.php",
            method:"POST",
            data:$('#keywords').serialize(),
            beforeSend: function() {
                $('#loader').removeClass('hidden')
            },
            success:function(data)
            {
                var data = jQuery.parseJSON(data);
                var articleHTML = "<h2 class=\"lead\">Results were found for the search for: <strong class=\"text-danger\"><span id=\"search_for_keyword\">" + data.keyword+ "</span></strong></h2>";

                console.log(data);

                $.each(data.titles, function(key, item){
                    articleHTML += "<article class=\"search-result row\">";
                    articleHTML += "<div class=\"col-xs-12 col-sm-12 col-md-4\">\n" +
                        "               <h5>"+ item + "</h5>\n" +
                        "           </div>\n" +
                        "           <div class=\"col-xs-12 col-sm-12 col-md-4\">\n" +
                        "               <h5><a href=\"#\" title=\"\">" + data.links[key] + "</a></h5>\n" +
                        "           </div>\n" +
                        "                <div class=\"col-xs-12 col-sm-12 col-md-4 excerpet\">\n" +
                        "                    <h5><a href=\"#\" title=\"\">" + data.contacts[key] + "</a></h5>\n" +
                        "                </div>\n" +
                        "                <span class=\"clearfix borda\"></span>\n" +
                        "            </article>";
                });

                $('#final_result').html(articleHTML);
            },
            done: function ()
            {
                $('#loader').addClass('hidden')
            }
        });
    });

});
