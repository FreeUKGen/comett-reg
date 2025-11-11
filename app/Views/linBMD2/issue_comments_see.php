
	<?php $session = session(); ?>
	
	<?php
	if ( $session->issue_state == 'open' )
			{ ?>
				<div class="step1">
					<form action="<?php echo(base_url('home/issue_comment_step2')) ?>" method="post" enctype="multipart/form-data">
						<div class="form-group row d-flex align-items-center justify-content-between">
							<label for="subject1" class="">Add a Comment =></label>
							<textarea
								rows="3"
								cols="40"
								class="form-control col-8" 
								id="comment1" 
								name="comment1" 
								aria-describedby="userHelp"
							>
								<?=esc($session->comment1)?>
							</textarea>
							<button type="submit" class="btn btn-primary mr-0">
								<span>Register your comment</span>	
							</button>
							
						</div>
					
			<?php
			} ?>
				</div>
	
	<div class="row table-responsive w-auto">
		<table class="table table-borderless" style="border-collapse: separate; border-spacing: 0;" id="show_table">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>Comment <input class="box no-sort" id="search" type="text" placeholder="Search..." ></th>
					<th>
								
					</th>
				</tr>		
			</thead>

			<tbody id="user_table">
				<?php foreach ($session->comments_result as $comment) 
					{ ?>
						<tr>
							<td style="border-bottom:2pt solid green;"><textarea readonly style="overflow:hidden;" class="autoresize" rows="2" cols="158"><?= esc(htmlspecialchars_decode($comment['body']))?></textarea></td>
							<?php 
							if ( $session->issue_state == 'open' )
								{ ?>
									<td style="border-bottom: 2pt solid green;">
										<a id="comment_delete" class="btn btn-primary mr-0" href="/home/issue_comments_delete/<?=esc($comment['id'])?>">
											Delete
										</a>
									</td>
								<?php
								} ?>
						</tr>
					<?php 
					} ?>
			</tbody>
		</table>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('/home/issue_see/open')); ?>">
			<?php echo $session->current_project['back_button_text']?>
		</a>					
	</div>

<script>
	$(document).ready(function() 
		{
			$('.autoresize').each(function(i, obj) 
				{
					obj.style.height = 'auto';
					obj.style.height = (obj.scrollHeight) + 'px';
				});
		});
</script>