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
		jQuery('#btnSearch').click(function(event){
			 event.preventDefault();
			 
			 //var arrive = $("#from").datepicker('getDate');
			 //var depart = $("#to").datepicker('getDate');
			
		
			 //jQuery('input[name="arrivalDay"]').val(arrive.getDate());
			 //jQuery('input[name="arrivalMonth"]').val(arrive.getMonth()+1);
			 //jQuery('input[name="arrivalYear"]').val(arrive.getFullYear());
						
			 //jQuery('input[name="departureDay"]').val(depart.getDate());
			 //jQuery('input[name="departureMonth"]').val(depart.getMonth()+1);
			 //jQuery('input[name="departureYear"]').val(depart.getFullYear());
			
			
			 jQuery('#form').submit();
			 
			});
		});

	
</script>
  
  <title>Disney</title>
</head>

<body>
 <form id="form" target="_blank" action="http://www.awin1.com/awclick.php?mid=2632&id=<?php echo $aid?>&p=https://disneyworld.disney.go.com/services/BookingGenie/submitUK" method="post">
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

		  <div style="clear:both;">
      	 	<label>Depart</label>
        	<select name="arrivalDay" id="" size="1">
        			<option value="1">1</option>
        			<option value="2">2</option>
        			<option value="3">3</option>
        			<option value="4">4</option>
        			<option value="5">5</option>
        			<option value="6">6</option>
        			<option value="7">7</option>
        			<option value="8">8</option>
        			<option value="9">9</option>
        			<option value="10">10</option>
        			<option value="11">11</option>
        			<option value="12">12</option>
        			<option value="13">13</option>
        			<option value="14">14</option>
        			<option value="15">15</option>
        			<option value="16">16</option>
        			<option value="17">17</option>
        			<option value="18">18</option>
        			<option value="19">19</option>
        			<option value="20">20</option>
        			<option value="21">21</option>
        			<option value="22">22</option>
        			<option value="23">23</option>
        			<option value="24">24</option>
        			<option value="25">25</option>
        			<option value="26">26</option>
        			<option value="27">27</option>
        			<option value="28">28</option>
        			<option value="29">29</option>
        			<option value="30">30</option>		
        		</select>
        	<select name="arrivalMonth" size="1">
        		<option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option><option value="4">Apr</option><option value="5">May</option><option value="6" selected="selected">Jun</option><option value="7">Jul</option><option value="8">Aug</option><option value="9">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
        	</select>
        	<select name="arrivalYear" size="1">
        		<option value="2013" selected="selected">2013</option><option value="2014">2014</option>
       		 </select>
        </div>
        	        	
        <div>
           <label>Return</label>
        <select name="departureDay" size="1">
        	<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25" selected="selected">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option>
        </select>
        <select name="departureMonth" size="1">
        	<option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option><option value="4">Apr</option><option value="5">May</option><option value="6" selected="selected">Jun</option><option value="7">Jul</option><option value="8">Aug</option><option value="9">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
        </select>
        <select name="departureYear" size="1">
       		 <option value="2013" selected="selected">2013</option><option value="2014">2014</option>
        </select>
        
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


