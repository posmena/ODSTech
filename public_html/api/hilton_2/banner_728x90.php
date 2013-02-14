
<link href="styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="ieStyles.css" />
<![endif]-->
<link href="jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" />
<link href="sayt.css" rel="stylesheet" type="text/css" />


<script src="js/jquery-1.8.0.min.js" type="text/javascript" /></script>
<script src="js/jquery-ui-1.8.23.custom.min.js" type="text/javascript"></script>

<form target="_blank" id="frm_odst_hilton" method="POST" action="http://www3.hilton.com/en_US/hi/search/findhotels/index.htm">
	<input type="hidden" name="searchType" value="ALL">
	<input type="hidden" name="searchQuery" value="">
<input type="hidden" name="arrivalDate" value="">
<input type="hidden" name="departureDate" value="">
<input type="hidden" name="radiusFromLocation" value="40">
<input type="hidden" name="radiusUnits" value="MILES">
<input type="hidden" name="_flexibleDates" value="on">
<input type="hidden" name="_rewardBooking" value="on">
<input type="hidden" name="numberOfRooms" value="1">
<input type="hidden" name="numberOfAdults[0]" value="1">
<input type="hidden" name="numberOfChildren[0]" value="0">
<input type="hidden" name="numberOfAdults[1]" value="1">
<input type="hidden" name="numberOfChildren[1]" value="0">
<input type="hidden" name="numberOfAdults[2]" value="1">
<input type="hidden" name="numberOfChildren[2]" value="0">
<input type="hidden" name="numberOfAdults[3]" value="1">
<input type="hidden" name="numberOfChildren[3]" value="0">
<input type="hidden" name="promoCode" value="">
<input type="hidden" name="srpIds" value="">
<input type="hidden" name="onlineValueRate" value="">
<input type="hidden" name="groupCode" value="">
<input type="hidden" name="corporateId" value="">
<input type="hidden" name="_rememberCorporateId" value="on">
<input type="hidden" name="_aaaRate" value="on">
<input type="hidden" name="_aarpRate" value="on">
<input type="hidden" name="_seniorRate" value="on">
<input type="hidden" name="_governmentRate" value="on">
<input type="hidden" name="_travelAgentRate" value="on">
<input type="hidden" name="selectedHotelBrands" value="CH">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="DT">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="ES">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="HP">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="HI">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="GI">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="HT">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="HW">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="selectedHotelBrands" value="WA">
<input type="hidden" name="_selectedHotelBrands" value="on">
<input type="hidden" name="searchAllBrands" value="true">
<input type="hidden" name="_searchAllBrands" value="on">
<table class="odst" width="728" height="90" border="0" cellpadding="8" cellspacing="0"  >
		<tr>
			<td align="left" valign="top" background="images/728x90back.jpg" style="position: relative;">
				<div id='messages' style=" position: absolute; top: 4px; left: 23px; width: 230px; height; 100px; text-align: center; color: #2f51a8; text-transform: uppercase;  margin-top: 10px;">
					<div id='div1' class=''> 
  						<span style="font-size: 25px; font-weight: bold;">SAVE UP TO 40%</span>
					</div>
					<div id='div2' class='display'> 
						<span style="font-size: 15px; font-weight: bold;">ON HOTEL STAYS </span> 
						<br/>
						<span style="font-size: 20px; font-weight: bold;">ANY WEEKEND, ANYWHERE</span>
					</div>
					<div id='div3' class='display'> 
						<span style="font-size: 20px; font-weight: bold;">Save Upto</span> <span style="font-size: 40px; font-weight: bold;">40%</span>
  						<br/>
  						<span style="color: black; ">Any Weekend, Anywhere</span>
					</div>
				</div>
				<table class="odst" border="0" table="table" width="300" height="35" cellspacing="0" cellpadding="0" class="vstyle" style="text-align: left; margin-left: 280px!important; margin-top: 13px!important; display: block; font-size: 10px; ">
					<tr>
						<td>
							<label for="city" style="margin-bottom:5px; text-align: left; display:inline-block;">WHERE ARE YOU GOING?</label>
							<br />   
							<span class="odt_spanTextInput" style="height: 15px; margin-right: 5px;">
								<label class="labelOneBoxHint" for="hotelSearchOneBox" style="display:inline; font-family: Arial;" >City, airport, address, attraction, or hotel</label>
								<input id="odst_locationTextInput" type="text" value="" maxlength="100" autocomplete="off" style="width:210px; "/>
							</span>
						</td>
						 <td valign="top" width="190" align="left" class="labelTop" style="margin-top: 4px; margin-right:5px; "> 
							<label for="guests">Guests</label>
							<br/>
							<select name="guests"  style="margin-top:3px;">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4 </option>
                    	    </select>
						</td>
					</tr>
				</table>
				<table class="odst" border="0" table="table" width="265" cellspacing="0" cellpadding="0" class="vstyle" height="30" style="margin-left: 280px!important; font-size: 10px; margin-top: 4px!important; display: block; ">
					<tr valign="top">
				 		<td valign="middle" width="130" align="left"> 
							<label for="checkin" class="labelTop arrival" style="float: left; margin-top: 4px; margin-right: 5px;">Arrival</label>
							<span class="odt_spanTextInput" dir="ltr">
								<input type="text" id="from" name="arrivalDate" style="width: 60px;">
								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>
							</span>
						</td>
						<td valign="middle" width="140" align="left" > 
							<label for="checkout" class="labelTop departure" style="float: left; margin-top: 4px; margin-right:5px;">Departure</label>
							<span class="odt_spanTextInput" dir="ltr">
								<input type="text" id="to" name="departureDate" style="width: 60px;">
								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>
							</span> 
						</td>
						
					</tr>
					<tr>
						<div valign="top" align="left" width="84" style="position: absolute;  left: 546px; top: 29px; z-index: 999;">
							<a href="#" class="728back" title="find it" role="button" style="z-index: 999; background-image: url('images/120button.jpg'); margin-top: 5px; width: 74px; height: 23px; display: block;">
								<span style="display:none;" >find it</span>
							</a>
						</div>	
					</tr>
					
               	 </table>
            </td>
        </tr>
	</table>
</form>



<script src="js/odst_hilton_v2.js" type="text/javascript"></script>
<script src="js/calendars_v2.js" type="text/javascript"></script>

<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="ie7.css" />
<![endif]-->