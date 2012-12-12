<?php
$affiliate_id = $_GET['aid'];
$campaign_id = $_GET['cid'];
?>


<link href="styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="ieStyles.css" />
<![endif]-->
<link href="jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" />
<link href="sayt.css" rel="stylesheet" type="text/css" />


<script src="js/jquery-1.8.0.min.js" type="text/javascript" /></script>
<script src="js/jquery-ui-1.8.23.custom.min.js" type="text/javascript"></script>

<form target="_blank" id="frm_odst_hilton" method="POST" action="http://www.awin1.com/awclick.php?awinmid=3624&awinaffid=<?php echo($affiliate_id)?>&clickref=<?php echo($campaign_id)?>&p=http://www3.hilton.com/en_US/hi/search/findhotels/index.htm">
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
<input type="hidden" name="_searchAllBrands" value="on">	<table class="odst"  style="min-height:600px" width="160" height="600" border="0" cellpadding="8" cellspacing="0"  >
		<tr>
			<td align="left" valign="middle" background="images/160x600back.jpg" style="padding:10px">
				<table class="odst"  border="0" table="table" width="140" height="250" cellspacing="0" cellpadding="0" class="vstyle" style="margin-top: 140px;min-height:600px">
					<tr>
						<td>
							<label for="city" style="margin-bottom:5px; display:inline-block;">WHERE ARE YOU GOING?</label>
							<br />   
							<span class="odt_spanTextInput">
								<label class="labelOneBoxHint" for="hotelSearchOneBox" style="display:inline; font-family: Arial;" >City, address...</label>
								<input id="odst_locationTextInput" type="text" value="" maxlength="100" autocomplete="off" style="width:125px;"/>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<label for="guests">Guests</label>
							<br />   
							<select name="guests"  style="margin-top:5px;margin-bottom:8px">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4 </option>
                    	    </select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="checkin" class="labelTop arrival" style="margin-bottom: 7px; display: inline-block;">Arrival</label>
							<br/>
							<span class="odt_spanTextInput" dir="ltr">
								<input type="text" id="from" name="arrivalDate" >
								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>
							</span>
						</td>
					</tr>
					<tr>
						<td> 
							<label for="checkout" class="labelTop departure" style="margin-bottom: 7px; display: inline-block;">Departure</label>
							<br/>
							<span class="odt_spanTextInput" dir="ltr">
								<input type="text" id="to" name="departureDate" >
								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>
							</span> 
						</td>
					</tr>
					<tr>
						<td>
							<a href="#" class="odst_findbutton" title="Find it" role="button" style="width: 44px; height: 11px;margin-top:5px">
								<span class="odst_text" >Find it</span>
								<span class="arrow_icon">&nbsp;</span>
							</a>
						</td>
					</tr>
               	 </table>
            </td>
        </tr>
	</table>
</form>

<script src="js/odst_hilton.js" type="text/javascript"></script>
<script src="js/calendars.js" type="text/javascript"></script>

<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="ie7.css" />
<![endif]-->