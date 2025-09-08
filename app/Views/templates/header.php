<!doctype html>

<html>
	<head>	
		<!-- initialse session -->
		<?php
			$session = session();
		?>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes, user-scalable=yes" />

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap js -->
		<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/latest/js/bootstrap.min.js"></script>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css">

		<!-- this for the autocomplete function -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
		
		<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
		<script src="<?php echo base_url().'../assets/js/resizeableInputs/dynamic-width-resize/jquery.dynamicWidth.js'; ?>"></script>

		<style>
			.ui-autocomplete 
				{
					
					background-color: #fffdd0;
					
				}
		
			.ui-menu-item 
				{
					
					padding: 0px 0px;
					clear: both;
					font-size: 1vw;
					font-weight: bold;
					line-height: 0.5hw;
					color: black;
					white-space: normal;
					text-decoration: none;
				}
				
			.ui-state-hover, .ui-state-active 
				{
					color: #ffffff;
					text-decoration: none;
					background-color: #0088cc;
					border-radius: 0px;
					-webkit-border-radius: 0px;
					-moz-border-radius: 0px;
					background-image: none;
				}
		
		</style>
		
		<!-- FreeComETT Awesome Icons from font awesome -->
		<script src="https://kit.fontawesome.com/9470003581.js" crossorigin="anonymous"></script>
	
		<!-- this for the virtual keyboard function -->
		<link rel="stylesheet" href="<?php echo base_url().'../assets/Keyboard-master/css/keyboard.css'; ?>">
		<script src="<?php echo base_url().'../assets/Keyboard-master/js/jquery.keyboard.js'; ?>"></script>
		<script src="<?php echo base_url().'../assets/Keyboard-master/js/jquery.keyboard.extension-autocomplete.js'; ?>"></script>
		<style>
			.keyboardicon {
				position: absolute;
				z-index: 1;
				right: 2vw;
				top: 1.0vh;
				color: #5f9ea0;
				cursor: pointer;
				width: 0;
			}
		</style>
		
		<!-- this for the panzoom function https://github.com/timmywil/panzoom/ -->
		<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom/dist/panzoom.min.js"></script>
		
		<style>
			.panzoom-wrapper 
			{
				height: <?php echo $session->image_y; ?>px;
				border: 3px solid blue;
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
		
		<!-- this for the hotkeys function -->
		<!-- <script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script> -->
		
		<!-- this for the spinner function -->
		<style>
			.spinner-border, #districts_staleness_spinner, #districts_refresh_spinner {display:none;}
			.spinner-border.active, #districts_staleness_spinner.active, #districts_refresh_spinner.active {display:block;}
			<!-- .ui-autocomplete { max-height: 130px; max-width: 190px; overflow-y: auto; overflow-x: hidden; } -->
		</style>

		<!-- this for the drop down menu and search boxes -->
		<style>
			.box 
			{
				width: 11vw;
				height: 4vh;
				border: 1px solid #999;
				color: green;
				background-color: #eee;
				border-radius: 5px;
			}
			.box_sm 
			{
				width: 8vw;
				height: 4vh;
				border: 1px solid #999;
				color: green;
				background-color: #eee;
				border-radius: 5px;
			}
			.box_vs 
			{
				width: 5vw;
				height: 4vh;
				border: 1px solid #999;
				color: green;
				background-color: #eee;
				border-radius: 5px;
			}
			.box_us 
			{
				width: 4vw;
				height: 2vh;
				border: 1px solid #999;
				color: green;
				background-color: #eee;
				border-radius: 5px;
			}
			.box_sm1 
			{
				width: 5vw;
				height: 2vh;
				border: 1px solid #999;
				color: green;
				background-color: #eee;
				border-radius: 5px;
			}
			.box_sm2 
			{
				width: 8vw;
				height: 2vh;
				border: 1px solid #999;
				color: green;
				background-color: #eee;
				border-radius: 5px;
			}
			.go_button_us
			{
				width: 1vw;
				height: 2vh;
				border: 1px solid #999;
				font-weight: bold;
				color: white;
				background-color: green;
				border-radius: 5px;
			}
			
			.go_button 
			{
				width: 3vw;
				height: 4vh;
				border: 1px solid #999;
				font-weight: bold;
				color: white;
				background-color: green;
				border-radius: 5px;
			}
			
			.go_button_setto
			{
				font-size: 1vh !important;
				height: 2vh;
				border: 1px solid #999;
				font-weight: bold;
				color: white;
				background-color: green;
				border-radius: 5px;
			}
		</style>
		
		<style>
			body 
				{
					a, button
					{
						font-size: 1vw !important;
					}
					font-size: 0.8vw;
				}
			header
				{
					font-size: 0.8vw;
				}
		</style>
		
		<!-- this is for the resize of the transcribe input -->
		<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
		<style>
			.resizable 
				{
					margin-right: 5px;
					overflow: hidden;
					touch-action: none;
					box-sizing: border-box;
				}
				
			.draggable 
				{
					  touch-action: none;
					  user-select: none;
				}
		</style>
		
		<!-- this is for the speed test -->
		<script type="text/javascript" src="<?=base_url('../assets/js/speedtest/speedtest.js')?>"></script>
		
		<!-- this is for the resizeable table columns -->
		<!-- http://www.bacubacu.com/colresizable/ -->
		<script type="text/javascript" src="<?=base_url('../assets/js/colresizable/colResizable-1.6.min.js')?>"></script>
		<style>
			.grip
				{
					width:20px;
					height:15px;
					margin-top:-3px;
					background-image:url("<?=base_url('../assets/js/colresizable/grip.png')?>");
					margin-left:-5px;
					position:relative;
					z-index:88;
					cursor:e-resize;
				}

			.grip:hover
				{
					background-position-x:-20px;
				}
		</style>
	
		<!-- this is for drag and drop table rows -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/0.9.1/jquery.tablednd.js"></script>
		
		<style>
			tr.myDragClass td 
				{
					/*position: fixed;*/
					color: yellow;
					text-shadow: 0 0 10px blue, 0 0 10px blue, 0 0 8px blue, 0 0 6px blue, 0 0 6px blue;
					background-color: #E7E896;
					-webkit-box-shadow: 0 12px 14px -12px #111 inset, 0 -2px 2px -1px #333 inset;
				}
				
			tr.myDragClass td:first-child 
				{
					-webkit-box-shadow: 0 12px 14px -12px #444 inset, 12px 0 14px -12px #111 inset, 0 -2px 2px -1px #333 inset;
				}
				
			tr.myDragClass td:last-child 
				{
					-webkit-box-shadow: 0 12px 14px -12px #111 inset, -12px 0 14px -12px #111 inset, 0 -2px 2px -1px #333 inset;
				}
		</style>
		
		<!-- this is for the loading-overlay.jquery.js https://gasparesganga.com/labs/jquery-loading-overlay/ -->
		<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
		
		<!-- this is highlighting search text -->
		<script src="https://cdn.jsdelivr.net/npm/mark.js/dist/jquery.mark.min.js"></script>

<!-- start -->

		<title><?= esc($session->title); ?></title>
		
		<div class="container-fluid px-5">
			<?php
				switch ($session->environment)
					{
						case 'LIVE':
						?>
							<div class="row d-flex justify-content-between alert alert-info align-items-center">
						<?php
							break;
						case 'TEST':
						?>
							<div class="row d-flex justify-content-between alert alert-danger align-items-center">
						<?php
							break;
						default:
						?>
							<div class="row d-flex justify-content-between alert alert-warning align-items-center">
						<?php
					} ?>
					
				<?php
				// show logo and allow click to go to project home page
				switch ( $session->current_project[0]['project_index'] )
					{
						case 1: ?>
							<a class="" href="https://www.freebmd.org.uk/" target="_blank"><img src="<?php echo base_url().'/'.$session->current_project[0]['project_pathtoicon'].'/'.$session->current_project[0]['project_iconname']; ?>" alt="freebmd" style="width:10vw;height:auto"></img></a>
							<?php
							break;
						case 2: ?>
							<a class="" href="https://www.freereg.org.uk/" target="_blank"><img src="<?php echo base_url().'/'.$session->current_project[0]['project_pathtoicon'].'/'.$session->current_project[0]['project_iconname']; ?>" alt="freereg" style="width:10vw;height:auto"></img></a>
							<?php
							break;
						case 3: ?>
							<a class="" href="https://www.freecen.org.uk/" target="_blank"><img src="<?php echo base_url().'/'.$session->current_project[0]['project_pathtoicon'].'/'.$session->current_project[0]['project_iconname']; ?>" alt="freecen" style="width:10vw;height:auto"></img></a>
							<?php
							break;
					} ?>
				
				<?php if ( $session->signon_success == 1 ): ?>
					<span class="small font-weight-bold"><?= esc('Environment = '.$session->environment); ?></span>
					<span class="small font-weight-bold"><?= esc($session->realname.' in '.$session->syndicate_name); ?></span>
					<span class="small font-weight-bold"><?= esc($session->total_records.' records transcribed and uploaded to this project'); ?></span>
					<?php
					if ( $session->current_project[0]['project_index'] != 2 )
						{ ?>
							<span class="small font-weight-bold"><?= esc($session->verify_mode_text); ?></span>
						<?php
						} ?>
					<span class="small font-weight-bold"><?= esc($session->zoom_status); ?></span>
					
				<?php endif ?>
				<span class="small font-weight-bold"><?= esc(date("jS F Y")); ?></span>
				<span class="small font-weight-bold">
					<img src="<?php echo base_url().'/Icons/FreeComETT.png' ?>" alt="FreeComETT" style="width:10vw;height:auto">
					<?= esc('Version '.$session->version); ?>
				</span>
			</div>
		</div>
	</head>
	
	<body>

		<div class="container-fluid px-5">
			
			<!-- show info message but only if message 2 is empty -->
			<?php 
			if ( $session->message_2 == '' )
				{ ?>
					<div class="<?=esc($session->message_class_1)?> alert-dismissible mt-1 row pl-0" role="alert">
						<span class="col-12 small font-weight-bold"><?=esc($session->message_1)?></span>
					</div>
				<?php
				} ?>

			<!-- show message 2 but only if message it is not empty -->
			<?php 
			if ( $session->message_2 != '' )
				{ ?>
					<div class="<?=esc($session->message_class_2)?> alert-dismissible row pl-0">
						<span class="col-12 small font-weight-bold"><?=esc($session->message_2)?></span>
					</div>
				<?php
				} ?>
