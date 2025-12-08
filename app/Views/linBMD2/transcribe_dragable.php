<?php $session = session(); ?>	

<script>
	$(document).ready(function() 
		{
			// Initialise the table
			$("#parameter_drag_rows").tableDnD(
				{
					onDragClass: "myDragClass",
				});

		});
</script>

