
	
	jQuery(function() {
	

		jQuery( "#from" ).datepicker({
			showOn: "button",
			buttonImage: "http://odst.co.uk/api/hilton/images/ui_cal_icon.gif",
			buttonImageOnly: true,
			dateFormat: "d M yy",
			minDate: -20, 
			maxDate: "+1Y",
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 2,
			onSelect: function( selectedDate ) {
				jQuery( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});

		jQuery('.odst_findbutton').click(function(){
			 jQuery('input[name="searchQuery"]').val(jQuery('#odst_locationTextInput').val());
			 jQuery('input[name="arrivalDate"]').val(jQuery('#from').val());
			 jQuery('input[name="departureDate"]').val(jQuery('#to').val());
			 jQuery('#frm_odst_hilton').submit();
			 
			});
		
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
			beforeShow: function(el,obj)
				{
					jQuery("#odst_d_52652").css('z-index',200000);
					jQuery("#odst_52652").width = "720px";
					jQuery("#odst_52652").height = "720px";				
				};
			onClose: function(dt,obj)
				{
					jQuery("#odst_d_52652").css('z-index',0);
					jQuery("#odst_52652").width = jQuery("#odst_d_52652").width;
					jQuery("#odst_52652").height = jQuery("#odst_d_52652").height;
				};
			}
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
	});
	
