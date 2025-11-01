<!doctype html>

<html>
	<head>	
		<!-- initialse session -->
		<?php
			$session = session();
		?>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
		
		<!-- this for the autocomplete function -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		
		<!-- this for the panzoom function -->
		<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom/dist/panzoom.min.js"></script>
		
		<!-- this for the hotkeys function -->
		<script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script>

		<!-- this for the panzoom and sharpen -->
		<style>
			.panzoom-wrapper 
			{
				height: <?php echo($session->image_y); ?>px;
				border: 1px solid blue;
				overflow: hidden;
				user-select: none;
				touch-action: none;
			}

			.panzoom > img 
			{
				width: 100%;
				filter: url(#unsharpy);
				transform: rotate(<?php echo($session->rotation); ?>deg);
			}
			
			#filters {
				display: block;
				position: absolute;
				top: -9999px;
				left: -9999px;
				width: 0;
				height: 0;
			}
		</style>
		
		<style>
			body 
				{
					a, button
					{
						font-size: 1vw !important;
					}
					font-size: 1.0vw;
				}
			header
				{
					font-size: 1.0vw;
				}
			p
				{
					font-size: 2.0vw;
				}
		</style>
					
	</head>
	
	<body>
		
		<div class="container-fluid px-5">
		<!-- show logo -->
			<br>
			<div class="row mt-4 d-flex justify-content-between">
				<a class="" href="https://www.freeukgenealogy.org.uk" target="_blank"><img src="<?php echo base_url().'/Icons/freeukgen-icon.png' ?>" alt="freeukgen" style="width:30vw;height:auto"></a>
					
				<img src="<?php echo base_url().'/Icons/FreeComETT.png' ?>" alt="FreeComETT" style="width:30vw;height:auto">		
			</div>
			<div class="text-right">
					<p><b>Com</b>munity <b>E</b>ntry <b>T</b>ranscription <b>T</b>ool</p>	
			</div>
			
			<br><br><br>
			
			<!-- show welcome text -->
			<div class="text-left">
				<p>Welcome to FreeComETT, FreeUKGenealogy's transcription application.</p>
			</div>
				
			<!-- show instruction -->
			<div class="text-left">
				<p>To get started, please select the project you wish to work with.</p>
			</div>
		
			<div class="row text-left table-responsive w-auto">
				<table class="table table-hover table-borderless" style="border-collapse: separate; border-spacing: 0;">
					<thead class="sticky-top bg-white">
						<tr>
							<th></th>
						</tr>
					</thead>

					<tbody>
<?php log_message('info', 'Project:' . print_r($session->projects, true)); ?>

						<?php 	foreach ($session->projects as $project): ?>
							<?php 	if ( $project['project_status'] == 'Open' )
											{ ?>
												<tr>
													<td>
														<a id="select_line" href="<?=(base_url().'/projects/load_project/'.esc($project['project_index'])) ?>">
														<span><img src="<?php echo base_url().'/'.$project['project_pathtoicon'].'/'.$project['project_iconname'] ?>" alt="freeukreg" style="width:10vw;height:auto"</span>
													</td>
												</tr>
											<?php
										}
								endforeach; ?>
					</tbody>
				</table>
			</div>

	
			<br>
			
			<div class="row mt-2 justify-content-between align-items-center alert alert-primary">
				<a class="" href="/home/close/">Signout</a>
				<p class="small">&copy; FreeUKGen 2020 - <?php echo date("Y"); ?></p>
			</div>
		</div>
	</body>
</html>


<script>
	document.addEventListener("DOMContentLoaded", () => 
		{
			fetch("<?php echo(base_url('home/test_javascript')) ?>", 
				{
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
					body: "text="
				})
				.then(response => '')
				.then(data => '');
		});
</script>

</body>
</html>