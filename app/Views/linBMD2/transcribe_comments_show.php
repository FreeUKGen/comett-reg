	<?php $session = session(); ?>
			
	<div>
		<div class="alert alert-dark row" role="alert">
			<span class="col-12 text-center"><b>Annotations already anchored to line => <?php echo $session->transcribe_detail[0]['BMD_surname'].', '.$session->transcribe_detail[0]['BMD_firstname'].' '.$session->transcribe_detail[0]['BMD_secondname'].' '.$session->transcribe_detail[0]['BMD_thirdname'].', '.$session->transcribe_detail[0]['BMD_partnername']; ?></b></span>
		</div>
		<div class="row table-responsive w-auto text-center" style="height:250px">
			<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
				<thead class="sticky-top bg-white">
					<tr>
						<th>Delete</th>
						<th>Type</th>
						<th>Span</th>
						<th>Text</th>
					</tr>
				</thead>

				<tbody>
					<?php if( $session->transcribe_detail_comments ): ?>
						<?php foreach ($session->transcribe_detail_comments as $detail): ?>
							<tr>
								<td>
									<a id="delete_line" href="<?=(base_url($session->controller.'/remove_comment/'.esc($detail['BMD_index']))) ?>"</a>
									<span><?= '-';?></span>
								</td>
								<td><?= esc($detail['BMD_comment_type'])?></td>
								<td><?= esc($detail['BMD_comment_span'])?></td>
								<td><?= esc($detail['BMD_comment_text'])?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
