<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
$aid = $_GET['aid']; 
?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <link rel="stylesheet" href="css/Calendar.css" type="text/css" />
  <link rel="stylesheet" href="css/odst_disney.css" type="text/css" />
  <script type="text/javascript" src="js/jquery-1.8.0.min.js"  /></script>
  <script type="text/javascript" src="js/jQuery-UI-1.8.16.min.js" ></script>
  <script type="text/javascript" src="js/jsQuickQuote.js" ></script>
  
  
<script>
	
	$(function() {
		$( "#from" ).datepicker({
			showOn: "button",
			buttonImage: "images/Calendar.gif",
			buttonImageOnly: true,
			dateFormat: "dd M yy",
			minDate: -20, 
			maxDate: "+1Y",
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});

		var fromDate = new Date();
		var toDate = new Date();
		fromDate.setTime(fromDate.getTime() + (1000*3600*24*30));
		toDate.setTime(fromDate.getTime() + (1000*3600*24*7));
	
		$("#from").datepicker('setDate', fromDate);
		
		$( "#to" ).datepicker({
			showOn: "button",
			buttonImage: "images/Calendar.gif",
			buttonImageOnly: true,
			dateFormat: "dd M yy",
			minDate: -20, 
			maxDate: "+1Y",
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
		
		$("#to").datepicker('setDate', toDate);
		
		jQuery('#btnSearch').click(function(event){
			 event.preventDefault();
			 
			 var arrive = $("#from").datepicker('getDate');
			 var depart = $("#to").datepicker('getDate');
			
		
			 jQuery('input[name="arrivalDay"]').val(arrive.getDate());
			 jQuery('input[name="arrivalMonth"]').val(arrive.getMonth()+1);
			 jQuery('input[name="arrivalYear"]').val(arrive.getFullYear());
						
			 jQuery('input[name="departureDay"]').val(depart.getDate());
			 jQuery('input[name="departureMonth"]').val(depart.getMonth()+1);
			 jQuery('input[name="departureYear"]').val(depart.getFullYear());
			
			
			 jQuery('#form').submit();
			 
			});
			
	});
	
	
	
</script>
  
  <title>Disney</title>
</head>

<body>
 <form id="form" action="http://www.awin1.com/awclick.php?mid=2632&id=<?php echo $aid?>&p=https://disneyworld.disney.go.com/services/BookingGenie/submitUK" method="post">
 <input type="hidden" value="NextGenFlightPackagesSQQProductOption_BookingGenie_en_GB" name="publishedKey">
 <input type="hidden" value="WDWFlightPackages" name="BusinessType">
 <input type="hidden" value="WDW" name="BusinessUnit">
 <input type="hidden" value="" name="arrivalDay">
 <input type="hidden" value="" name="arrivalMonth">
 <input type="hidden" value="" name="arrivalYear">
 <input type="hidden" value="" name="departureDay">
 <input type="hidden" value="" name="departureMonth">
 <input type="hidden" value="" name="departureYear">
 <input type="hidden" value="Economy" name="ClassOfTravel">
 
 <input type=hidden id="WDW_NextGenFlightPackagesSQQProductOption" name="BusinessType" value="WDWFlightPackages">
 <fieldset>
        <div>
            <label for="departure">From</label> 
            <select id="wdwUKAirportFrom" name="wdwUKAirportFrom"  class="wide">
              <option value="LondonAllAirports">London (all airports)</option><option value="Gatwic">London Gatwick</option><option value="LondonHeathrow">London Heathrow</option><option value="Manchester">Manchester</option><option value="Aberdeen">Aberdeen</option><option value="Belfast">Belfast</option><option value="Birmingham">Birmingham</option><option value="Bristol">Bristol</option><option value="Cardiff">Cardiff</option><option value="Edinburgh">Edinburgh</option><option value="Glasgow">Glasgow</option><option value="Inverness">Inverness</option><option value="Jersey">Jersey</option><option value="Newcastle">Newcastle</option></select>
			  
            </select> 
        </div>
        <div>
            <label for="destination">To</label> 
            <select id="wdwAirportTo" name="wdwAirportTo" class="wide">
              <option value="OrlandoAllAirports">Orlando (all airports)</option><option value="OrlandoInternational">Orlando International</option><option value="Sanford">Sanford</option><option value="Tampa">Tampa</option></select>
            </select> 
          </div>
        <div>
   			<label for="flight-leavingday">Depart</label>
			<input type="text" id="from" name="from" >
			<span style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>
    	 </div>
    	 <div>
   			<label for="flight-leavingday">Return</label>
			<input type="text" id="to" name="to" >
			<span style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>
    	 </div>
        
          <div class="pax">
            <label for="numAdults">Adults (12+)</label>
            <select id="numAdults" name="numAdults" class="narrow">
              <option value="1">
                1
              </option>

              <option value="2" selected="selected">
                2
              </option>

              <option value="3">
                3
              </option>

              <option value="4">
                4
              </option>

              <option value="5">
                5
              </option>

              <option value="6">
                6
              </option>
            </select> 
        </div>
        <div class="pax">
            <label for="numChildren">Children (2-11)</label><select id="numChildren" name="numChildren" class="narrow">
              <option value="0" selected="selected">
                0
              </option>

              <option value="1">
                1
              </option>

              <option value="2">
                2
              </option>

              <option value="3">
                3
              </option>

              <option value="4">
                4
              </option>

              <option value="5">
                5
              </option>
            </select> 
            
          </div>
         <div id="last-div">
         <input type="button" id="btnSearch" name="inputSubmit" class="button" value="Find Prices">
         </div>

	</fieldset>


  </form>

</body>


