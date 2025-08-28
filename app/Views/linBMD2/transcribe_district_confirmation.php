	<?php $session = session(); ?>
	
	<!-- Inject initial values for Panzoom here (x, y, zoom) -->
			<div class="panzoom-wrapper row">
			<div class="panzoom" id='panzoom'>
				<?php echo 
							"<img
								src=\"data:$session->mime_type;base64,$session->fileEncode\"
								alt=\"$session->image\"  
								data-scroll=\"$session->scroll_step\"
							>";
					?>
				</div>
			</div>
	<br>		
	<div class="step1">
		<form action="<?php echo(base_url($session->return_route)) ?>" method="post">
			<div class="row">
				<h4><b>Try REALLY HARD to find a district that you can attach this (mis-spelled?) district to (create a synonym).</b></h4>
			</div>
			<div class="row">
				<p class="">
					- Enter the first few characters.
				</p>
			</div>
			<div class="row">
				<p class="">
					- Try putting in spaces or full stops, especially after St, Mt, E, W, N, or S.
				</p>
			</div>
			<div class="row">
				<p class="">
					- Look at UKBMD districts if you want (see button below).
				</p>
			</div>
			
			<div class="form-group row">
				<label class="col-3 pl-0" for="synonym">Is this district a synonym for =>?</label>
				<input type="text" class="form-control col-2 pl-0" id="synonym" name="synonym" value="<?php echo esc($session->synonym);?>">
				<label for="confirm_synonym" class="col-2 pl-0">Confirm synonym?</label>
				<select name="confirm_synonym" id="confirm_synonym" class="box col-2">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->confirm ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<div class="row mt-4">
				<h4><b>Or, if you REALLY can't find a synonym,</b></h4>
			</div>
			
			<div class="form-group row mt-4">
				<label for="confirm" class="col-3 pl-0">Add the district you entered to the Districts Master database?</label>
				<select name="confirm" id="confirm" class="box col-2">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->confirm ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-4">Confirming will make this synonym available to ALL transcribers!</small>
			</div>
		
			<div class="row d-flex justify-content-between mt-4">
				<button type="submit" class="btn btn-primary mr-0 d-flex">
				<span><?php echo $session->current_project[0]['back_button_text']?></span>	
				</button>
				
				<?php
				use App\Models\Help_Model;
				$help_model = new Help_Model();
				$session->current_help =	$help_model
											->where('help_project', $session->current_project[0]['project_index'])
											->where('help_index', '12')
											->find();
				?>
				<a class="btn btn-primary mr-0" href="<?php echo($session->current_help[0]['help_url']) ?>" target="_blank">Districts to use as synonyms</a>
				
				<a class="btn btn-primary mr-0" href="https://www.ukbmd.org.uk/reg/districts/index.html" target="_blank">UKBMD Districts</a>
			
				<button type="submit" class="btn btn-primary mr-0 d-flex">
					<span>Continue</span>	
				</button>
			</div>
			
		</form>
	</div>
	
<script>
document.addEventListener("DOMContentLoaded", () => 
{
	$(document).keypress(function()
		{				
			$( "#synonym" ).autocomplete(
				{
					minLength: 2,
					source: "<?php echo(base_url('transcribe/search_districts')) ?>",
				})	
		});
});
  </script>
	
