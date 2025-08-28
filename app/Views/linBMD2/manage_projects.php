<?php $session = session(); ?>
	
	<div class="row">
		<p class="bg-danger col-12 pl-0 text-center" style="font-size:2vw;">This is VERY sensitive stuff. Only change these parameters if you know what you are doing!</p>
	</div>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Project</th>
					<th>Description</th>
					<th>Environment</th>
					<th>Status</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $session->projects as $project ) 
					{ 
						?>
						<tr>
							<td class="text-center">
								<a id="select_line" href="<?php echo(base_url('projects/manage_projects_step2/'.$project['project_index']))?>">
								<span><?= $project['project_name'];?></span>
							</td>
							<td class="text-left"><?= esc($project['project_desc'])?></td>
							<td><?= esc($project['environment'])?></td>
							<td><?= esc($project['project_status'])?></td>
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
