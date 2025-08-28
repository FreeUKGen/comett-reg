
	<?php $session = session(); ?>
	
	<div class="row table-responsive w-auto">
		<table class="table table-borderless" style="border-collapse: separate; border-spacing: 0;" id="show_table">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>Reference</th>
					<th>Title</th>
					<th>Body <input class="box no-sort" id="search" type="text" placeholder="Search Table..." ></th>
					<th>Labels</th>
					<th>Comments</th>
					<th>Related Issues</th>
				</tr>		
			</thead>

			<tbody id="user_table">
				<?php foreach ($session->curl_result as $issue) 
					{ 
							$labels = '';
							foreach ( $issue['labels'] as $label )
								{
									$labels = $labels.$label['name'].', ';
								} ?>
								
						<tr>
							<td style="border-bottom: 2pt solid green;"><?= esc($issue['number'])?></td>
							<td style="border-bottom: 2pt solid green;"><?= esc(htmlspecialchars_decode($issue['title']))?></td>
							<?php if ( $issue['body'] == null )
								{
									$issue['body'] = '';
								} ?>
							<td style="border-bottom: 2pt solid green;"><textarea readonly style="overflow:hidden;" class="autoresize" rows="2" cols="95"><?= esc(htmlspecialchars_decode($issue['body']))?></textarea></td>
							<td style="border-bottom: 2pt solid green;"><?= esc(htmlspecialchars_decode($labels))?></td>
							<td style="border-bottom: 2pt solid green;">
								<a id="comments" class="btn btn-primary mr-0" href="/home/issue_comments_see/<?=esc($issue['number']).'/'.$issue['title']?>">
									<?= esc($issue['comments'])?>
								</a>
							</td>
							<?php 
							$related_issues = '';
							if ( array_key_exists($issue['number'], $session->related_issues) )
								{ ?>
									<td style="border-bottom: 2pt solid green;"><?= esc(htmlspecialchars_decode($session->related_issues[$issue['number']]))?></td>
								<?php
								} ?>
						</tr>
					<?php 
					} ?>
			</tbody>
		</table>
	</div>
		
	<div class="row mt-4 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		<a id="open_issues" class="btn btn-primary mr-0" href="/home/issue_see/open">
			Open Issues
		</a>
		<a id="closed_issues" class="btn btn-primary mr-0" href="/home/issue_see/closed">
			Closed Issues
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


