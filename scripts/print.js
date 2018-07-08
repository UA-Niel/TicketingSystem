function printPage() {
    html2canvas(document.querySelector("#capture")).then(canvas => {
        document.getElementsByClassName("scissor-line")[0].appendChild(canvas);
        document.querySelector(".scissor-line .ticket").style.display = "none";
     //   document.body.appendChild(canvas);
        
        setTimeout(function() {
            window.print();
        }, 500);
         
    });
    
   
} 
