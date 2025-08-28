	<?php $session = session(); ?>
		
	<div>
		<div class="alert alert-dark row" role="alert">
			<span class="col-12 text-center"><b>Add Annotation anchored to line => <?php echo $session->transcribe_detail[0]['BMD_surname'].', '.$session->transcribe_detail[0]['BMD_firstname'].' '.$session->transcribe_detail[0]['BMD_secondname'].' '.$session->transcribe_detail[0]['BMD_thirdname'].', '.$session->transcribe_detail[0]['BMD_partnername']; ?></b></span>
		</div>
		<div class="row table-responsive w-auto text-center" style="">
			<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
				<thead class="sticky-top bg-white">
					<tr>
						<th>Type</th>
						<th>Span</th>
						<th>Text</th>
					</tr>
				</thead>
				
				<tbody>
					<form action="<?php echo(base_url($session->controller.'/comment_step2')) ?>" method="post">
						<tr>
							<td>
								<select class="box" name="comment_type" id="comment_type" style="width: 400px">
								<?php foreach ($session->comment_types as $key => $type): ?>
									 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->comment_type ) {echo esc(' selected');} ?>><?php echo esc($type)?></option>
								<?php endforeach; ?>
								</select>
							</td>
			
							<td><input type="text" id="comment_span" name="comment_span" value="<?php echo esc($session->comment_span);?>"></td>
							<td><input class="col-10 pl-0" type="text" id="comment_text" name="comment_text" placeholder="eg => Entry reads CRITCHER or SMITH for mother's name." value="<?php echo esc($session->comment_text);?>"></td>
						</tr>	
				</tbody>
			</table>
		</div>
		
			<div class="row mt-4 d-flex justify-content-between">
				
					<?php
					if ( $session->BMD_cycle_code == 'VERIT' )
						{ ?>
							<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/verify_step1/'.$session->detail_line['BMD_header_index'])); ?>">
							<?php echo $session->current_project[0]['back_button_text']?>
							</a>
						<?php
						}
					else
						{ ?>
							<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url($session->controller.'/transcribe_'.$session->controller.'_step1/0')); ?>">
							<?php echo $session->current_project[0]['back_button_text']?>
							</a>
						<?php
						} ?>

					<button type="submit" class="btn btn-primary mr-0">
						<span>Submit</span>	
					</button>
				
			</div>
		</form>


		
	



