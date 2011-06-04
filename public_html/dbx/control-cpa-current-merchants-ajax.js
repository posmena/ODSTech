$(document).ready(function(){

	$('a.unjoin_merchant').click(function(){
		var selected_checkboxes = $('#locked_merchants').val();
		if ($(this).attr('href') == '')
		{
			alert('Sorry, this merchant is unavailable. Please contact admin.');
			var abort = true;
			return false;
		}
		else
		{

			selected_checkboxes = selected_checkboxes.replace(',' + $(this).attr('href') + ',', ',');
			$('#locked_merchants').val(selected_checkboxes);
			$(this).parent().parent().hide('slow');
			
			$.get("/manage/cpa", { type: "ajax", func: "update_merchants", selected_merchants: selected_checkboxes},
				function(data){
					
				});
			return false;
			
		}
		
	});

});