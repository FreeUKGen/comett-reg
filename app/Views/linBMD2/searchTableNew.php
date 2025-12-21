<script>
	
$(document).ready(function()
	{
		$("#search").on("keyup", function() 
			{
				var value = $(this).val().toLowerCase();
				$("#user_table tr").filter(function() 
					{
						$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
					});
			});
	});

</script>
