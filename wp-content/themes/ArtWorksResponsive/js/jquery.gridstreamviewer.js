function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}



//expanding to stream
function gridtostream() {

    $("body").on("click", ".thumb_img", function(e) {



        //  $('a[rel="lightbox"]:not(".led")')



        $(".columns.three").removeClass("small");
        $(".columns.three").addClass("big");

        $(".full_img").show();
        $(".thumb_img").hide();

        var id = $(this).parent().attr('id');
          var slug = $(this).parent().attr('slug');
        $('html, body').animate({
            scrollTop: $("#" + id).offset().top - 35
        }, 200);

        //   $("#" + id).css("background-color", "whitesmoke");
        //     alert("element ID clicked:");
        //  window.location.href = 'http://kupamemow.pl/?p'+id;

        // History.pushState(null, null, 'http://kupamemow.pl/?p='+id);
        History.pushState({state: $click_cnt}, null, 'http://kupamemow.pl/' + slug);


    });
}

function streamtogrid() {
    //shirnking to grid
    $("body").on("click", ".full_img", function(e) {


// $(".columns.three").css("background-color", "white");

        
      if ( viewport()["width"]>900 ) { 



        $(".columns.three").removeClass("big");
        $(".columns.three").addClass("small");

        $(".full_img").hide();
        $(".thumb_img").show();

        var id = $(this).parent().attr('id');
    var slug = $(this).parent().attr('slug');
        $('html, body').animate({
            scrollTop: $("#" + id).offset().top - 35
        }, 200);


        // $("#" + id).css("background-color", "whitesmoke");
        //     alert("element ID clicked:");

        History.pushState({state: $click_cnt}, null, 'http://kupamemow.pl/' + slug);
        
        }

    });







}


$(document).ready(function() {

      if ($('#single_cont').length === 1) {


  $click_cnt = 0;

    (function(window, undefined) {

        // Bind to StateChange Event
        History.Adapter.bind(window, 'statechange', function() { // Note: We are using statechange instead of popstate
            var State = History.getState(); // Note: We are using History.getState() instead of event.state
        });



    })(window);

      }
      
    //grid page case

  if ($('#single_cont').length === 0) {

       gridtostream();
    streamtogrid();

    }  


}






);







