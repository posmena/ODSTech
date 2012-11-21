	
	$(function() {
		$( "#from" ).datepicker({
			showOn: "button",
			buttonImage: "http://odstest.co.uk/api/hilton/images/ui_cal_icon.gif",
			buttonImageOnly: true,
			dateFormat: "dd M yy",
			minDate: -20, 
			maxDate: "+1Y",
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 2,
			onSelect: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});

		$('.odst_findbutton').click(function(){
			 $('input[name="searchQuery"]').val($('#odst_locationTextInput').val());
			 $('input[name="arrivalDate"]').val($('#from').val());
			 $('input[name="departureDate"]').val($('#to').val());
			 $('#frm_odst_hilton').submit();
			 
			});
		
		$( "#to" ).datepicker({
			showOn: "button",
			buttonImage: "http://odstest.co.uk/api/hilton/images/ui_cal_icon.gif",
			buttonImageOnly: true,
			dateFormat: "dd M yy",
			minDate: -20, 
			maxDate: "+1Y",
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 2,
			onSelect: function( selectedDate ) {
				$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
		
		var $search = jQuery('#odst_locationTextInput');
        var $span = jQuery('.labelOneBoxHint ');

          // hide span on click
          $span.on('click', function(){
            $span.hide();
            $search.focus();
          });

          // setup span initially
          if($search.val() != ''){
            $span.hide();
          }

          // set focus and blur events
          $search.focus(function(){
            $span.hide();
          });
          $search.blur(function(){
            if(jQuery(this).val() == '') {
              $span.css('display', 'inline');
            }
          });
	});
	
