		<?php 
			$session = session();
		?>
		
		<!-- show sharpen slider Need this to stop javascript from crashing-->
		<div class="">
			<input class="" type="hidden" id="sharpen-slider" min="1" max="5" step=".5" value="$session->sharpen" />
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
		
		<div class="alert row mt-2 d-flex justify-content-between">
			<button type="submit" class="col-3 btn btn-outline-primary btn-lg mr-0">
				<span>Confirm modification of this line</span>	
			</button>
			
			<a class="col-8 btn btn-warning btn-lg mr-0" style="-webkit-touch-callout: none !important;			-webkit-user-select: none !important; " id="verifyonthefly" href="<?php echo(base_url('transcribe/verify_onthefly_confirm')) ?>">
			<span>Have you VERIFIED this line? Click ONCE to confirm...</span>
			</a>
		</div>
	</form>
