$(document).ready(function(){
    $('#search_keyword').keypress(function(e) {
        //alert(e.keyCode);
        if(e.keyCode == 13)
        {
            $('#search_product').click();
            return false;
        }
    });
	$('a.add_products,a.remove_products').click(function(){
		$('#manage_products').submit();
		return false;
	});
	$('#group_id').change(function(){
		$('#change_selected_category').click();
	});
	$('#search_product').click(function(){
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
		$.get("/manage/products", { type: "ajax", func: "search_products", search_products: $('#search_keyword').val(), exclude_keywords: $('#exclude_keywords').val(), musthave_keywords: $('#musthave_keywords').val(), gender: $('#search_gender').val()},
			function(data){
				$('#product_settings_ajax').html(data);
				$("span.uncontrolled-interval").stopTime();
				$("span.uncontrolled-interval").html('Search results below!');
			});
		return false;
	});
});