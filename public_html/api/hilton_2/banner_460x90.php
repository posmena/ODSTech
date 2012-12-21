
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
		<table class="odst" width="460" height="60" border="0" cellpadding="8" cellspacing="0" >
		<tr>
			<td align="right" valign="middle" background="images/468X60back.jpg" style="padding:5px; ">
			<table class="odst" border="0" table="table" width="150" height="20" cellspacing="0" cellpadding="0" class="vstyle" style="color: #2f51a8; text-align: center;  margin-top: 5px; display: inline-block; text-transform: uppercase; position: absolute; top: 6px; left: -6px;">
					<tr> 
						<td>
							<span style="font-size: 13px; font-weight: bold;">Save Upto</span> <span style="font-size: 17px; font-weight: bold;">40%</span>
						</td>
					</tr>
					<tr style="padding-top: 5px; display: inline-block; font-size: 10px; ">
						<td style="color: black; ">Any Weekend,
						<br/>anywhere</td>
					</tr>
				</table>
				<table valign="top" align="left" class="odst" border="0" table="table" width="260" height="20" cellspacing="0" cellpadding="0" class="vstyle" style="text-align: left; margin-left: 127px!important; display: inline-block; font-size: 10px;">
					<tr>
						<td>
							<label for="city" style="margin-bottom:5px; display:inline-block; vertical-align: middle;">WHERE ARE YOU GOING?</label>
						</td>
 						<td>
							<span class="odt_spanTextInput" style="height: 15px;">
								<label class="labelOneBoxHint" for="hotelSearchOneBox" style="display:inline; font-family: Arial;" >City, airport, address...</label>
								<input id="odst_locationTextInput" type="text" value="" maxlength="100" autocomplete="off" style="width:140px; "/>
							</span>
						</td>
					</tr>
				</table>
				<table class="odst" valign="bottom" align="left" border="0" table="table" width="250" height="20" cellspacing="0" cellpadding="0" class="vstyle" style=" margin-left: 127px!important; display: inline-block; font-size: 10px;" >
					<tr>
						<td align="left" width="50px">
							<label for="guests"  style="margin-bottom: 0px;">Guests</label>
							<br/>
							<select name="guests" style="margin-top: 0px; height: 10px;">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4 </option>
                    	    </select>
						</td>					
						<td width="80px" align="left">
							<label for="checkin" class="labelTop arrival" style="margin-bottom: 0px; display: inline-block;">Arrival</label>
							<br/>					
							<span class="odt_spanTextInput smallBanner" dir="ltr" style="height: 14px; width: 70px;">
								<input type="text" id="from" name="arrivalDate" >
								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>
							</span>
						</td>
						
						<td width="80px" align="left"> 
							<label for="checkout" class="labelTop departure" style=" display: inline-block;">Departure</label>
							<br/>
							<span class="odt_spanTextInput smallBanner" dir="ltr" style="height: 14px; width: 70px;">
								<input type="text" id="to" name="departureDate" >
								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>
							</span> 
						</td>
						<td align="right" style="position: absolute; top: 29px; left: 337px;">
							<a href="#" class="" title="Find it" role="button" style="margin-top: 5px; width: 56px; height: 15px; background: url('images/button_460x60.gif'); display: block; margin-left: auto; margin-right: auto;">
								<span style="display:none;" >Find it</span>
							</a>
						</td>
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
