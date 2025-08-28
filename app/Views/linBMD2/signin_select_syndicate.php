<?php $session = session(); ?>	
	
	<form action="<?php echo(base_url('identity/signin_get_syndicate')) ?>" method="post">
		<div class="form-group row">
			<label for="syndicate" class="col-3 pl-0">Select Syndicate for this FreeComETT session => </label>
			<select class="col-3 pl-0 box" name="syndicate" id="syndicate">
				<?php foreach ($session->project_user_syndicates as $key => $syndicate): ?>
					<option value="<?= esc($syndicate['SyndicateID'])?>">
						<?= esc($syndicate['SyndicateName'])?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		
	<br><br>
	
		<div class="row mt-4 d-flex justify-content-between">
			
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/close/")); ?>">
				<span>Close application</span>
			</a>
			
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/index/")); ?>">
				<span>Select project</span>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0 d-flex">
				<span>Continue</span>	
			</button>	
		</div>
	</form>
