<html>

    <head>
        <title>Domain Check - WebUI</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
   
        <link rel="stylesheet" type="text/css" href="own_style.css">

        <script>
            function includeHTML() {
              var z, i, elmnt, file, xhttp;
              /* Loop through a collection of all HTML elements: */
              z = document.getElementsByTagName("*");
              for (i = 0; i < z.length; i++) {
                elmnt = z[i];
                /*search for elements with a certain atrribute:*/
                file = elmnt.getAttribute("own-include-html");
                if (file) {
                  /* Make an HTTP request using the attribute value as the file name: */
                  xhttp = new XMLHttpRequest();
                  xhttp.onreadystatechange = function() {
                    if (this.readyState == 4) {
                      if (this.status == 200) {elmnt.innerHTML = this.responseText;}
                      if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
                      /* Remove the attribute, and call this function once more: */
                      elmnt.removeAttribute("own-include-html");
                      includeHTML();
                    }
                  }
                  xhttp.open("GET", file, true);
                  xhttp.send();
                  /* Exit the function: */
                  return;
                }
              }
            }
        </script> 

    </head>

    <body class="body-color">

        <div own-include-html="navbar.html"></div>        

        <script>
            includeHTML();
        </script> 

        Welcome <?php echo $_GET["name"]; ?><br>
        Your email address is: <?php echo $_GET["email"]; ?>

    </body>

</html> 