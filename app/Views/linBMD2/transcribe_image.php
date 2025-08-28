<?php $session = session(); ?>

<?php
	if ( ! isset($session->feh_show) )
		{
			$session->set('feh_show', 1);
			$file = getcwd().'/Users/'.$session->user[0]['BMD_user'].'/Scans/'.$session->transcribe_header[0]['BMD_scan_name'];	
			$session->set('image', $session->transcribe_header[0]['BMD_scan_name']);
			$session->set('fileData', exif_read_data($file));
			$session->set('mime_type', $session->fileData['MimeType']);
			$session->set('fileEncode', base64_encode(file_get_contents($file)));
		}
?>


<div class="panzoom-wrapper row">
    <div class="panzoom">
        <!-- Inject initial values for Panzoom here (x, y, zoom) -->
        <?php echo "<img src=\"data:$session->mime_type;base64,$session->fileEncode\" alt=\"$session->image\" data-x=\"$session->panzoom_x\" data-y=\"$session->panzoom_y\" data-zoom=\"$session->panzoom_z\" data-s=\"$session->sharpen\"  data-scroll=\"$session->scroll_step\"/>"; ?>
    </div>
</div>

<div class="row d-flex justify-content-center">
	<h6 class="col-1 small text-muted">Sharpen</h6>
	<input class="col-2" type="range" id="sharpen-slider" min="1" max="5" step=".5" value="$session->sharpen" />
</div>

<!-- Sharpen filter for image using SVG -->
<svg id="filters">
    <defs>
        <filter id="unsharpy" x="0" y="0" width="100%" height="100%">
            <feGaussianBlur result="blurOut" in="SourceGraphic" stdDeviation="1" />
            <feComposite operator="arithmetic" k1="0" k2="4" k3="-3" k4="0" in="SourceGraphic" in2="blurOut" />
        </filter>
    </defs>
</svg>

<br>
