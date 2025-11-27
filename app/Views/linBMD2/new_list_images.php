	<?php $session = session(); ?>
	
	<div class="row">
		<p class="col-12 pl-0 my-2 text-center font-weight-bold" >
			<?php
				echo $session->current_project['allocation_text'].' List Images for -> '.$session->current_allocation[0]['BMD_allocation_name'];
			?>
		</p>
	</div>

    <div>
        <div class="title">
            <h1 class="mt-1">View Images</h1>
        </div>
        <div class="button-rotate-feature">
            <button id="rotate-clockwise">Rotate clockwise</button>
            <button class="ml-4" id="rotate-anticlockwise">Rotate anti-clockwise</button>
        </div>

        <section class="view-images-grid-container centre container px-0">
        <?php foreach ($session->allocation_images as $image): ?>

            <div class="view-images-image-wrapper centre col-4 col-md-3 col-lg-2">
                <img
                    alt="FreeREG image"
                    class="allocation-thumbnail"
                    src="<?= esc($image['image_url']) ?>"
                    data-image-index="<?= esc($image['image_index']) ?>"
                    data-image-filename="<?= esc($image['image_file_name']) ?>"
                />
                <p><?= esc($image['image_file_name']) ?></p>
            </div>
        <?php endforeach; ?>

        </section>

        <div class="button-cancel-save">
            <button id="cancel-btn" class="cancel">Cancel</button>
            <button id="save-btn" class="save">Save</button>
        </div>

    </div>
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('allocation/manage_allocations/0')); ?>">
			<?php echo $session->current_project['back_button_text']?>
		</a>
	</div>
