
	jQuery(document).ready(function() {
		
		
		jQuery('.odst_findbutton').click(function(){
			 jQuery('input[name="searchQuery"]').val(jQuery('#odst_locationTextInput').val());
			 jQuery('input[name="arrivalDate"]').val(jQuery('#from').val());
			 jQuery('input[name="departureDate"]').val(jQuery('#to').val());
			 jQuery('#frm_odst_hilton').submit();
			 
			});
		
		
		var jQuerysearch = jQuery('#odst_locationTextInput');
        var jQueryspan = jQuery('.labelOneBoxHint ');

          // hide span on click
          jQueryspan.on('click', function(){
            jQueryspan.hide();
            jQuerysearch.focus();
          });

          // setup span initially
          if(jQuerysearch.val() != ''){
            jQueryspan.hide();
          }

          // set focus and blur events
          jQuerysearch.focus(function(){
            jQueryspan.hide();
          });
          jQuerysearch.blur(function(){
            if(jQuery(this).val() == '') {
              jQueryspan.css('display', 'inline');
            }
          });
		  
		if ($.browser.msie  && ( parseInt($.browser.version, 10) === 7) || parseInt($.browser.version, 10) === 8 ) {

	} else {
  
	
	
		jQuery( "#from" ).datepicker({
			showOn: "button",
			buttonImage: "http://odst.co.uk/api/hilton/images/ui_cal_icon.gif",
			buttonImageOnly: true,
			dateFormat: "d M yy",
			minDate: -20, 
			maxDate: "+1Y",
			changeMonth: false,
			numberOfMonths: 2,
			onSelect: function( selectedDate ) {
				jQuery( "#to" ).datepicker( "option", "minDate", selectedDate );				
			},
			beforeShow: function(el,obj)
				{
				jQuery('#ui-datepicker-div').mouseleave(function(){jQuery( "#from" ).datepicker('hide')})					
				}
		});
		
		var fromDate = new Date();
		var toDate = new Date();
		fromDate.setTime(fromDate.getTime() + (1000*3600*24*1));
		toDate.setTime(fromDate.getTime() + (1000*3600*24*3));
	
		$("#from").datepicker('setDate', fromDate);

		jQuery( "#to" ).datepicker({
			showOn: "button",
			buttonImage: "http://odst.co.uk/api/hilton/images/ui_cal_icon.gif",
			buttonImageOnly: true,
			dateFormat: "dd M yy",
			minDate: -20, 
			maxDate: "+1Y",
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 2,
			onSelect: function( selectedDate ) {
				jQuery( "#from" ).datepicker( "option", "maxDate", selectedDate );
				},
			beforeShow: function(el,obj)
				{
				jQuery('#ui-datepicker-div').mouseleave(function(){jQuery( "#to" ).datepicker('hide')})					
				}
		});
		
		$("#to").datepicker('setDate', toDate);
		
		}

	});
	
