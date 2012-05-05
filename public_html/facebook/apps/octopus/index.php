<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<?php

    // facebook app authentication details
    $appId = '180360922086647';
    $appSecret = '46deae6d38f1738a3ac684119732da71';
    $canvasUrl = 'http://apps.facebook.com/odsthotelsearch/';
    
    // pull out the session code / request
    session_start();
    $authCode = $_REQUEST["code"];
    if(empty($authCode)) {
        $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
        $dialogUrl = "http://www.facebook.com/dialog/oauth?client_id=" . $appId . "&redirect_uri=" . urlencode($canvasUrl) . "&state=" . $_SESSION['state'];
        echo("<script> top.location.href='" . $dialogUrl . "'</script>");
		
    }

    // security check
    $user = null;
	$state = $_REQUEST['state'];
	if( empty($state) )
		{
		header( 'Location: /notAuthorized.php?e=1' ) ;
		}
    if($state == $_SESSION['state']) {
        $token_url = "https://graph.facebook.com/oauth/access_token?client_id=" . $appId . "&redirect_uri=" . urlencode($canvasUrl) . "&client_secret=" . $appSecret . "&code=" . $authCode;

        // get the response
        $response = file_get_contents($token_url);
        $params = null;
        parse_str($response, $params);

        // pull out user etc ...
        $graphUrl = "https://graph.facebook.com/me?access_token=" . $params['access_token'];
        $user = json_decode(file_get_contents($graphUrl));
    }
    else {
        
        // not authorized, just give up now!
		  header( 'Location: /notAuthorized.php?e=2' ) ;
        //redirect('notAuthorized.php');
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Search Hotel Bookings</title>
        <script type="text/javascript" src="jquery/jquery.min.js"></script>
        <script type="text/javascript" src="jquery/jquery.autocomplete-min.js"></script>
        <script type="text/javascript" src="jquery/zebra_datepicker/zebra_datepicker.js"></script>
        <link rel="stylesheet" href="jquery/zebra_datepicker/zebra_datepicker.css" type="text/css">
        <script type="text/javascript">
        
            $(function() {
                
                // autocomplete for the city field
                $('input[name="cityCodeString"]').autocomplete({
                    serviceUrl: 'ajax/locations.php',
                    minChars: 2,
                    noCache: true,
                    onSelect: function(v, d) {
                        $('input[name="cityCode"]').val(d);
                    }
                });
                
                // auto-submit search form via. ajax
                $('#searchButton').on('click', function() {
                    var $form = $('#searchForm form');
                    $.post($form.attr('action'), $form.serialize(), function(htmlStr) {
                        $('#results').replaceWith($('#results', htmlStr));
                    });
                });
                
                // setup the datepickers
                $('input.datepicker').Zebra_DatePicker();
                               
            })
        
        </script>
    </head>
    <body>
        <h1>Hi <?php echo $user->name; ?>, Book your hotel here!</h1>
        <div id="searchForm">
            <form action="ajax/search.php" method="POST">
                <fieldset>
                    <ol>
                        <li>
                            <label>Where are you going?</label>
                            <input type="hidden" name="cityCode" />
                            <input type="text" name="cityCodeString" />
                        </li>
                        <li>
                            <label>Check In</label>
                            <input type="text" class="datepicker" name="checkIn" />
                        </li>
                        <li>
                            <label>Check Out</label>
                            <input type="text" class="datepicker" name="checkOut" />
                        </li>
                        <li>
                            <label>Number of Rooms</label>
                            <select name="quantity">
                                <option value="1">1 Room</option>
                                <option value="2">2 Rooms</option>
                                <option value="3">3 Rooms</option>
                                <option value="4">4 Rooms</option>
                                <option value="5">5 Rooms</option>
                            </select>
                        </li>
                        <li>
                            <input type="button" id="searchButton" name="search" value="Find Rooms" />
                        </li>
                    </ol>
                </fieldset>
            </form>
        </div>
        <div id="results">
            <p>Please fill out the search form to view results here.</p>
        </div>
    </body>
</html>