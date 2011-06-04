$(document).ready(function(){
    $('#search_keyword').keypress(function(e) {
        //alert(e.keyCode);
        if(e.keyCode == 13)
        {
            $('#search_cpa').click();
            return false;
        }
    });
	$('input.country_selector').click(function(){
		var country_selected_checkboxes = '';
		$('input.country_selector').each(function(){
			if($(this).is(':checked'))
			{
				country_selected_checkboxes = country_selected_checkboxes + $(this).val() + ',';
			}
		});
		$('#countries_selected').val(country_selected_checkboxes);
	});
	$('#search_cpa').click(function(){
		$("span.uncontrolled-interval").stopTime();
		$("span.uncontrolled-interval").everyTime(10,function(i) {
			if(i>100)
			{
				if (i > 3000)
				{
					var $val = ceil(i/100) + 'Oh no. Please wait!';
				}
				else
				{
					var $val = i/100 + '';
				}

			}
			else
			{
				var $val = i + '';
			}
			$(this).html($val);
		});
        if($('#search_keyword').val() == "")
        {
            alert('Search Keyword required.');
            return false;
        }
		$.get("/manage/cpa", { type: "ajax", func: "search_cpa", search_cpa: $('#search_keyword').val(), limit_by_countries: $('#countries_selected').val(), merchant_only:  $('#search_type').is(':checked')},
			function(data){
				$('#cpa_settings_ajax').html(data);
				$('#search_keyword').val('');
				$("span.uncontrolled-interval").stopTime();
				$("span.uncontrolled-interval").html('Search results below!');
			});
		return false;
	});
});