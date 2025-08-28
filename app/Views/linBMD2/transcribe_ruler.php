<?php $session = session(); ?>	

<script>
// Ruler - thanks to Asís García - https://medium.com/trabe/an-html-ruler-with-vanilla-javascript-8d75ef9ffc8b		
function ruler() 
	{
		const ruler = document.createElement("div");

		ruler.style.cssText = `
			position: absolute;
			top: ${window.scrollY + window.innerHeight/2 - 20}px;
			left: ${window.scrollX + window.innerWidth/2 - 20}px;
			width: 200px;
			height: 20px;
			background: #ff00006e;
			outline: solid;
			z-index: 99999;
			  `;

		// D&D
		ruler.draggable = true;

		const dragstartHandler = e => 
			{
				e.dataTransfer.dropEffect = "move";

				const mouseOffset = 
					{
						x: e.offsetX,
						y: e.offsetY,
					};    

					e.dataTransfer.setData("text/plain", JSON.stringify(mouseOffset));
			};

		ruler.addEventListener("dragstart", dragstartHandler);

		const dragoverHandler = e => 
			{
				e.preventDefault();
			};

  document.addEventListener("dragover", dragoverHandler);

  const dropHandler = e => {
    e.preventDefault();

    const mouseOffset = JSON.parse(e.dataTransfer.getData("text/plain"));

    ruler.style.left = `${e.pageX - mouseOffset.x}px`;
    ruler.style.top = `${e.pageY - mouseOffset.y}px`;
  };

  document.addEventListener("drop", dropHandler);

  // Info
  const getInfo = () => {
    return `${ruler.clientWidth}x${ruler.clientHeight}`;
  };

  const printInfo = () => console.info(getInfo());

  const updateTitle = () => {
    ruler.title = getInfo();
  };

  // Sizing & position
  const changeSize = (x, y) => 
	{
		const newWidth = ruler.clientWidth + x;
		const newHeight = ruler.clientHeight + y;
		
		// get calibration stage
		var calibrateStage = <?php echo json_encode($session->calibrate); ?>;
		
		// get current zoom factor
		var zoomFactor = <?php echo json_encode($session->panzoom_z); ?>;
		
		// calculate scroll step if stage = 1
		if ( calibrateStage == '1' )
			{
				// calculate input_scroll_step
				var input_scroll_lines = $('#input_scroll_lines').val();
				var input_scroll_step = (newHeight / input_scroll_lines) / zoomFactor;
				input_scroll_step = Math.round(input_scroll_step * 10) / 10;
				$('#input_scroll_step').val(input_scroll_step);
				
				// calculate image height
				var input_height_lines = $('#input_height_lines').val();
				var input_height_image = (input_scroll_step * 150 / 100) * zoomFactor * input_height_lines;
				input_height_image = Math.round(input_height_image * 10) / 10;
				$('#input_height_image').val(input_height_image);
			}
			
		// calculate field widths if stage = 2
		if ( calibrateStage == '2' )
			{
				// calculate new width
				var width = Math.floor(newWidth);
				
				// get the ID of the selected input field
				var element = document.getElementById("input_field");
				
				// set the ID of the input field
				var inputFieldID = element.value;
				
				// set its new width
				$('#' + inputFieldID).val(width);
				
				// set its width on screen
				var inputField=document.getElementById(inputFieldID);
				inputField.style.width = width+"px";
			}
		
		ruler.style.width = `${Math.max(1, newWidth)}px`;
		ruler.style.height = `${Math.max(1, newHeight)}px`;
	};

  const moveTo = (x, y) => {
    ruler.style.left = `${x}px`;
    ruler.style.top = `${y}px`;
  }

  const changePosition = (x, y) => {
    const newLeft = ruler.offsetLeft + x;
    const newTop = ruler.offsetTop + y;

    moveTo(newLeft, newTop);
  };


  // Focus and keyboard
  ruler.tabIndex = 0;
  ruler.focus();

  const clickHandler = e => {
    ruler.focus();
  };

  ruler.addEventListener("click", clickHandler);

  const keydownHandler = e => {
    e.preventDefault();

    const { key, shiftKey, ctrlKey } = e;

    if (["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown", "j", "k", "h", "l", "J", "K", "H", "L"].includes(key)) {
      const mul = shiftKey ? 10 : 1;

      const x = ["ArrowLeft", "h", "H"].includes(key) ? -1 : ["ArrowRight", "l", "L"].includes(key) ? 1 : 0;

      const y = ["ArrowUp", "k", "K"].includes(key) ? -1 : ["ArrowDown", "j", "J"].includes(key) ? 1 : 0;

      if (ctrlKey) {
        changeSize(x * mul, y * mul);
      } else {
        changePosition(x * mul, y * mul);
      }
    }

    if (key === "Escape") {
      removeRuler();
    }

    if (key === "i") {
      printInfo();
    }

    if (key === "t") {
      tracking = !tracking;
    }

    if (key === "r") {
      const { width, height } = ruler.style;

      ruler.style.width = height;
      ruler.style.height = width;
    }
  };

  ruler.addEventListener("keydown", keydownHandler);

  // Mouse tracking. See also the document keydown event handler
  let tracking = false;

  const mousemoveHandler = e => {
    if (!tracking) {
      return
    }

    moveTo(e.pageX, e.pageY);
  }

  const stopTracking = () => tracking = false;

  ruler.addEventListener("click", stopTracking);

  document.addEventListener("mousemove", mousemoveHandler);

  document.body.appendChild(ruler);

  updateTitle();

  const removeRuler = () => {
    ruler.removeEventListener("dragstart", dragstartHandler);
    ruler.removeEventListener("click", clickHandler);
    ruler.removeEventListener("keydown", keydownHandler)
    ruler.removeEventListener("click", stopTracking);
    document.removeEventListener("dragover", dragoverHandler);
    document.removeEventListener("drop", dropHandler);
    document.removeEventListener("mousemove", mousemoveHandler);

    ruler.remove();
  };

  return removeRuler;
}

var removeRuler;

if (removeRuler) {
  removeRuler();
}

removeRuler = ruler(); 
</script>


