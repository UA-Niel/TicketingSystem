$(document).ready(function() {
    $("#koopNu-btn").click(function() {     
        //Form slide effect
        $(".form-wrapper").slideDown(1000);

        //Make price fade-out
        $(".price span").fadeOut(500);
        
        //Set new margin
        $(this).css({'margin-top': '7px'});
    
        //Display progress
        $(".progress").fadeIn(1300);
    });
});
