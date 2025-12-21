<?php $session = session(); ?>
	
	<br><br>
	<div class="step1">
		
		<form action="<?php echo(base_url($session->return_route)) ?>" method="post">
			<div class="form-group row alert alert-warning pl-0" style="font-size:20px; font-weight:bold;">
				<pre><?php echo "LAST Values    => ";?></pre>
				<?php foreach ( $session->last_values as $value )
					{ ?>
						<pre><?php echo esc($value);?></pre>
						<pre><?php echo " | ";?></pre>
					<?php
					} ?>	
			</div>
			<div class="form-group row alert alert-success pl-0" style="font-size:20px; font-weight:bold;">
				<pre><?php echo "CURRENT Values => ";?></pre>
				<?php foreach ( $session->current_values as $value )
					{ ?>
						<pre><?php echo esc($value);?></pre>
						<pre><?php echo " | ";?></pre>
					<?php
					} ?>	
			</div>
			<div class="form-group row">
				<label for="confirm" class="col-2 pl-0" style="font-size:20px; font-weight:bold;">Confirm duplicate line?</label>
				<select name="confirm" id="confirm" class="col-1 box" style="font-size:20px; font-weight:bold;">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->confirm ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
			</div>
		
		<div class="row d-flex justify-content-end mt-4">
				<button type="submit" class="btn btn-primary mr-0 d-flex">
					<span>Continue</span>	
				</button>
			</div>
			
		</form>
	</div>
