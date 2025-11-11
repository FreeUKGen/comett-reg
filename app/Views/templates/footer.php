<?php $session = session(); ?>    
    
	<?php
	if ( $session->masquerade == 1 )
		{ ?>
			<footer class="row mt-3 mb-3">
		<?php
		}
	else
		{ ?>
			<footer class="row mt-3 mb-3">
		<?php
		} ?>
				<a class="" title="Signout" href="/home/signout/"><i class="fa-solid fa-door-open"></i>SignOut</a>
				<a class="" title="Transcribe Home" href="<?=(base_url('transcribe/transcribe_step1/0')) ?>"><i class="fa-solid fa-house-user"></i>Home</a>
				<a class="" title="Manage your Identity" href="<?=(base_url('identity/change_details_step2/0')) ?>"><i class="fa-solid fa-id-card"></i>Identity</a>
				<a class="" title="Housekeeping" href="/housekeeping/index/0"><i class="fa-solid fa-warehouse"></i>Housekeeping</a>
				<?php
				switch ( $session->current_project['project_index'] )
					{
						case 1: ?>
							<a class="" title="FreeBMD FAQ" href="https://www.freebmd.org.uk/vol_faq.html" target="_blank"><i class="fa-regular fa-circle-question"></i>FAQ</a>
							<?php
							break;
						case 2: ?>
							<a class="" title="FreeREG FAQ" href="https://www.freereg.org.uk/cms/information-for-transcribers.html" target="_blank"><i class="fa-regular fa-circle-question"></i>Info</a>
							<?php
							break;
						case 3: ?>
							<?php
							break;
					} ?>
					
				<?php
				switch ( $session->current_project['project_index'] )
					{
						case 1: ?>
							<a class="" title="FreeBMD File Management" href="<?php echo esc($session->curl_url); ?>" target="_blank"><i class="fa-solid fa-folder-open"></i></a>
							<?php
							break;
						case 2:
							if ( $session->environment == 'LIVE' )
								{ ?>		
									<a class="" title="FreeREG file management" href="https://www.freereg.org.uk/cms/refinery/login.html" target="_blank"><i class="fa-solid fa-folder-open"></i></a>
								<?php
								}
							else
								{ ?>
									<a class="" title="FreeREG file management" href="https://test3.freereg.org.uk/cms/refinery/login.html" target="_blank"><i class="fa-solid fa-folder-open"></i></a>
								<?php
								}
							break;
						case 3: ?>
							<?php
							break;
					} ?>
					
				<a class="" title="FreeComETT Help" href="/help/help_show/0"><i class="fa-solid fa-circle-question"></i></a>
				
				<a class="" title="Post comment" href="/home/issue_step1/0"><i class="fa-brands fa-github"></i></a>
				
				<a class="" title="See all posted comments" href="/home/issue_see/open"><i class="fa-solid fa-comments"></i></a>
					
				<a class="" target="_blank" title="Perform Speed Test" href="/home/speedtest/0"><i class="fa-solid fa-gauge"></i></a>
					
				<a class="" target="_blank" title="Copyright" href="https://www.freeukgenealogy.org.uk"><i class="fa-solid fa-copyright"></i> FreeUKGen 2020 - <?php echo date("Y"); ?></a>
	</footer>
  </body>
</html>
