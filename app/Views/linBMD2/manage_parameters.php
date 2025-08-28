<?php $session = session(); ?>
	
	<div class="row">
		<p class="bg-danger col-12 pl-0 text-center" style="font-size:2vw;">This is VERY sensitive stuff. Only change these parameters if you know what you are doing!</p>
	</div>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Parameter Description</th>
					<th>Value</th>
					<th>Default</th>
					<th>Allowed Values</th>
					<th>Allow Change</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $session->parameters as $parameter ) 
					{ 
						?>
						<tr>
							<td class="text-center">
								<?php
								if ( $parameter['Parameter_allow_change'] == 'YES' )
									{ ?>
										<a id="select_line" href="<?php echo(base_url('parameter/manage_parameters_step2/'.$parameter['Parameter_key']))?>">
										<span><?= $parameter['Parameter_description'];?></span>
									<?php
									} 
								else
									{?>
										<span><?= $parameter['Parameter_description'];?></span>
									<?php
									} ?>
							</td>
							<td><?= esc($parameter['Parameter_value'])?></td>
							<td><?= esc($parameter['Parameter_default'])?></td>
							<td><?= esc($parameter['Parameter_allowed_values'])?></td>
							<td><?= esc($parameter['Parameter_allow_change'])?></td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('database/database_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		
	</div>
