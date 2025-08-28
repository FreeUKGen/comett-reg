<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Codeigniter 4 + jQuery UI auto Complete</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>


$(document).ready(function() {
    $( "#cari" ).autocomplete({
        minLength: 0,
        source: "<?php echo(base_url('auto/search')) ?>",
        focus: function( event, ui ) {
            $( "#cari" ).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $( "#cari" ).val( ui.item.label );

            $( "#results").text( ui.item.email);    
            return false;
    }
})

});
  </script>
</head>
<body>
 
Search: <input type="text" id="cari" />

<br/><br/><br/><br/>

<p>Email : <span id="results"></span></p>

 
 
</body>
</html>
