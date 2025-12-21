	<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('home/issue_step2')) ?>" method="post" enctype="multipart/form-data">
			
			<div class="form-group row d-flex align-items-center">
				<label for="subject1" class="col-2">Subject =></label>
				<input 
					type="text" 
					class="form-control col-6" 
					id="subject1" 
					name="subject1" 
					aria-describedby="userHelp" 
					value="<?php echo esc($session->subject1); ?>" 
				>
				<small id="userHelp" class="form-text text-muted col-3">Please enter a brief description.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="body" class="col-2">Description of problem or suggestion</label>
				<textarea 
					class="form-control col-6"
					rows="6"
					id="body" 
					name="body" 
					aria-describedby="userHelp" 
					value="<?php echo esc($session->body); ?>"
				> 
				</textarea>
				<small id="userHelp" class="form-text text-muted col-3">Describe your problem or suggestion here. Drag the lower righthand corner to add more lines.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="label" class="col-2">Select a label</label>
				<select 
					class="form-control col-6"
					id="label" 
					name="label"
					aria-describedby="userHelp" >
						<option value="question">Question</option>
						<option value="Help wanted">Help wanted</option>
						<option value="bug">Bug</option>
						<option value="enhancement">Enhancement</option>
						<option value="suggestion">Suggestion</option>
						<option value="accessibility">Accessibility</option>
						<option value="database">Database</option>
						<option value="design">Design</option>
						<option value="documentation">Documentation</option>
						<option value="quality">Quality</option>
						<option value="user experience">User Experience</option>
				</select>
				<small id="userHelp" class="form-text text-muted col-3">Select a label to apply to this report from the dropdown list.</small>
			</div>
			
			
			
			<div class="alert row mt-2 d-flex justify-content-between">
				<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('transcribe/transcribe_step1/0')); ?>">
					<?php echo $session->current_project['back_button_text']?>
				</a>
			
				<button type="submit" class="btn btn-primary mr-0">
					<span>Register your report</span>	
				</button>
			</div>		
	
	</form>