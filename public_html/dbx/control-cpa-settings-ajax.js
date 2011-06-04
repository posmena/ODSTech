function makeTall(){$(this).find(".description").show('slow');}
function makeShort(){$(this).find(".description").hide('fast');}
$(document).ready(function(){

	$('.no_merchant_id a').click(function(){
		var meta_id = $(this).attr('href');
		alert('You want to add: '+meta_id);
		$.get("/manage/cpa", { type: "ajax", func: "add_merchant", meta_id: meta_id},
				function(data){
					alert('Merchant ' + data + ' has been added to the queue for processing. Should be available in 24/48 hours.');
					return false;
				});
		return false;
	});

	$('.pending a').click(function(){
		alert('This merchant has been requested already and shall be added shortly.');
		return false;
	});
	 
	$('input.merchant_selector').click(function(){
		var abort = false;
		var selected_checkboxes = $('#locked_merchants').val();

		if($(this).is(':checked'))
		{
			if ($(this).val() == '')
			{
				alert('Sorry, this merchant is unavailable. Please contact admin.');
				var abort = true;
				return false;
			}
			else
			{
				selected_checkboxes = selected_checkboxes + $(this).val() + ',';
				$('#locked_merchants').val(selected_checkboxes);
				$(this).parent().parent().hide('slow');
			}
		}

		if(!abort)
		{
			$.get("/manage/cpa", { type: "ajax", func: "update_merchants", selected_merchants: selected_checkboxes},
				function(data){
					$('#current_merchants').html(data);
				});
		}
		else
		{
			return false;
		}
	});

	$('.merchant_row').each(function(){
		var config = {    
			 sensitivity: 3, // number = sensitivity threshold (must be 1 or higher)    
			 interval: 500, // number = milliseconds for onMouseOver polling interval    
			 over: makeTall, // function = onMouseOver callback (REQUIRED)    
			 timeout: 1000, // number = milliseconds delay before onMouseOut    
			 out: makeShort // function = onMouseOut callback (REQUIRED)    
		};
		$(this).hoverIntent(makeTall,makeShort);
		$(this).hover(
			function(){
				$(this).css('background-color', '#FAFAFA');
			},
			function(){
				$(this).css('background-color', 'transparent');
		});

	});
});