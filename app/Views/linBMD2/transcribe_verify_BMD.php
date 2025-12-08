	<?php $session = session(); ?>
	
	<!-- line width is 1132 pixels -->
	<?php $sw = '50px'; ?>
	<?php $nw = '1080px'; ?>
	
	<table class="table table-hover">
		<thead class="d-block">
			<tr>
				<th style="padding:5px; text-align:left; width:<?php echo($sw); ?>;">BMD line number</th>
				<th style="padding:5px; text-align:left; width:<?php echo($nw); ?>;">BMD line text</th>
			</tr>
		</thead>

		<tbody class="d-block overflow-auto" style="height:500px;">
			<?php foreach ($session->verify_BMD_file as $key => $value): ?>
				<tr>
					<td style="padding:5px; text-align:left; width:<?php echo($sw); ?>;"><?php echo esc($key)?></th>
					<td style="padding:5px; text-align,left; width:<?php echo($nw); ?>;"><?php echo esc($value)?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
		
	<div class="row mt-4 d-flex justify-content-between">	
			<a class="btn btn-primary btn-sm" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">Return</a>
	</div>
	
