//Function to show progress in purchase process
//:param x: Amount of bullets to colour
//:param n: Total amount of bullets
function showProgress(x, n) {
    var html = "<div class=\"progress\">";
    
    var bullet = "<div class=\"bullet\"></div>";
    var bulletColored = "<div class=\"bullet colored\"></div>";

    for (var i = 0; i < n; i++) {
        if (i < x) {
            html += bulletColored;
        } else {
            html += bullet;
        }
    }

    html += "</div>";

    document.write(html);
}
