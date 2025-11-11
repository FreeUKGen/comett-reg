	<?php $session = session(); ?>
	
	<div class="row">
		<p 
			class="bg-warning col-12 pl-0 text-center font-weight-bold" 
			style="font-size:1.5vw;">
			<?php
				echo $session->current_project['allocation_text'].' List Images for -> '.$session->current_allocation[0]['BMD_allocation_name'];
			?>
		</p>
	</div>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>Thumbnail - click to show image</th>
					<th>Image</th>
					<th>Attached to Transcription</th>
					<th>Image transcription started on</th>
					<th>Image transcription completed on</th>
					<th>
						<input class="box no-sort" id="search" type="text" placeholder="Search..." >		
					</th>
					<th class="no-sort"></th>
				</tr>
			</thead>

			<tbody  id="user_table">
				<?php foreach ($session->allocation_images as $image): ?>
						<tr>
							<td class="align-middle"> <a target="_blank" href=<?= esc($image['image_url']) ?>> <img style="border: 1px solid #ddd; border-radius: 4px; padding: 5px;" src=<?= esc($image['image_url']) ?> alt="FreeREG image" width="100" height="120"><a/></td>
							<td class="align-middle"><?= esc($image['image_file_name'])?></td>
							<td class="align-middle"><?= esc($image['transcription_index'])?></td>
							<td class="align-middle"><?= esc($image['trans_start_date'])?></td>
							<td class="align-middle"><?= esc($image['trans_complete_date'])?></td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('allocation/manage_allocations/0')); ?>">
			<?php echo $session->current_project['back_button_text']?>
		</a>
	</div>