$(document).ready(function(){
	function init_selectors()
	{
		$('#select_all_remove').click(function(){
			if($(this).is(':checked'))
			{
				$('.checkbox_remove').attr('checked', true);
			}
			else
			{
				$('.checkbox_remove').attr('checked', false);
			}
		});

		$('#select_all_add').click(function(){
			if($(this).is(':checked'))
			{
				$('.checkbox_add').attr('checked', true);
			}
			else
			{
				$('.checkbox_add').attr('checked', false);
			}
		});
	}
	init_selectors();
});