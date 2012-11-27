	
	jQuery(function() {
		jQuery( ".odst_from" ).datepicker({
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
				jQuery( ".odst_to" ).datepicker( "option", "minDate", selectedDate );
			}
		});

		jQuery('.odst_findbutton').click(function(){
			 jQuery('input[name="searchQuery"]').val(jQuery('#odst_locationTextInput').val());
			 jQuery('input[name="arrivalDate"]').val(jQuery('#from').val());
			 jQuery('input[name="departureDate"]').val(jQuery('#to').val());
			 jQuery('#frm_odst_hilton').submit();
			 
			});
		
		jQuery( ".odst_to" ).datepicker({
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
				jQuery( ".odst_from" ).datepicker( "option", "maxDate", selectedDate );
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
	
