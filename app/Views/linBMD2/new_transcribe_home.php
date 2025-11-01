<?php $session = session();
use App\Models\Transcription_Comments_Model;
?>

<main>
      <div class="nav-container">
        <div class="sub-nav">
          <div class="sub-nav-left">
            <p>Assignment - STS198_174927493857810</p>
          </div>
          <div class="img-tools">
            <input type="image" src="./btn-left.png" />
            <div>Image 1 of 4</div>
            <input type="image" src="./btn-right.png" />
            <input type="image" src="./contrast-symbol.png" />

            <input type="image" src="./triangle-symbol.png" width="17px" />

            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 20 20"
              strokeWidth="{1.5}"
              stroke="currentColor"
              className="size-6"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
              />
            </svg>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
              <path
                d="M561.4 65.8C552.4 62.1 542.1 64.1 535.2 71L483.4 122.8C382.8 39.3 233.3 44.7 139.1 139C39.1 239 39.1 401 139.1 501C239.1 601 401.2 601 501.1 501C516 486.1 528.7 469.8 539.2 452.5C546.1 441.2 542.4 426.4 531.1 419.5C519.8 412.6 505 416.3 498.1 427.6C489.6 441.6 479.3 454.9 467.1 467C385.9 548.2 254.2 548.2 172.9 467C91.6 385.8 91.7 254.1 172.9 172.8C248.4 97.3 367.5 92 449.1 156.8L399.1 207C392.2 213.9 390.2 224.2 393.9 233.2C397.6 242.2 406.4 248 416.1 248L552.2 248C565.5 248 576.2 237.3 576.2 224L576.2 88C576.2 78.3 570.4 69.5 561.4 65.8zM528.2 145.9L528.2 200L474.1 200L528.2 145.9z"
              />
            </svg>

            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6"
              />
            </svg>

            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM13.5 10.5h-6"
              />
            </svg>
          </div>
          <div class="sub-nav-right">
            <p>Records transcribed - xxxxxxxxx</p>
          </div>
        </div>
      </div>

      <div class="wrap-container container">
        <div class="image-container">
          <img id="image" class="image" src="./burials.jpeg" />
        </div>
      </div>
      <div class="x">
        <form class="table-search-bar">
          <div class="marriages">
            <p class="form-selected">Marriages</p>

            <label class="marriage">
              <p>Marriage</p>
              <input type="image" src="./down-icon.png" />
            </label>
            <label class="baptism">
              <p>Baptism</p>
              <input type="image" src="./down-icon.png" />
            </label>
            <label class="burial">
              <p>Burial</p>
              <input type="image" src="./down-icon.png" />
            </label>
          </div>
          <div class="edit-search">
            <button class="adjust-fields"><p>Adjust fields</p></button>
            <input
              class="search"
              type="text"
              placeholder="Search  &#x1F50E;&#xFE0E;"
            />
          </div>
        </form>
        <div class="container">
          <div class="table-scroll">
            <table>
                <thead>
                <tr>
                    <th>Entry</th>
                    <th>Reg Number</th>
                    <th><?php echo $session->current_project[0]['allocation_text'].' Name' ?></th>
                    <th>Document Source</th>
                    <th>Image Source</th>
                    <th>Image Count</th>
                    <th>Current Scan</th>
                    <th>N° lines trans</th>
                    <th>Start Date</th>
                    <th>Last change date/time</th>
                    <th>Upload Date</th>
                    <th>Status</th>
                    <th>Comments</th>
                    <th>Last Action Performed</th>
                </tr>
                </thead>
                <tbody>

                <tbody id="user_table">
                <?php $n=0; foreach ($session->transcriptions as $transcription)
                {
                    if ( $transcription['BMD_header_index'] == $session->current_header_index )
                    { ?>
                        <tr class="alert alert-success">
                        <?php
                    }
                    else
                    { ?>
                        <tr class="alert alert-light">
                        <?php
                    } ?>
                    <td><?php echo $n+=1; ?></td>
                    <td
                            class="edit_assignment"
                            title="ClickMe to edit assignment if in FreeREG"
                            data-id="<?=esc($transcription['BMD_allocation_index'])?>"
                            data-action='CHGEA'>
                        <?= esc($transcription['BMD_allocation_name'])?>
                    </td>
                    <td><?= esc($transcription['BMD_file_name'])?></td>

                        <td class="next_action"
                            title="<?=esc($transcription['source_text'])?>"
                            data-id="<?=esc($transcription['BMD_header_index'])?>"
                            data-action='UPCOM'>
                            <?php
                            if ( !is_null($transcription['source_text']) )
                            {
                                echo esc(ellipsize($transcription['source_text'], 100, .5, '...'));
                            }
                            else
                            {
                                echo esc($transcription['source_text']);
                            }
                            ?>
                        </td>
                        <td><?= esc($transcription['image_source'])?></td>
                        <td><?= esc($transcription['image_count'])?></td>
                        <?php
                    } ?>

                    <td><?= esc($transcription['BMD_scan_name'])?></td>
                    <td class="next_action"
                        title="ClickMe to Transcribe from Scan"
                        data-id='<?=esc($transcription['BMD_header_index'])?>'
                        data-action='INPRO'>
                        <?= esc($transcription['BMD_records'])?>
                    </td>
                    <td><?= esc($transcription['BMD_start_date'])?></td>
                    <td><?= esc($transcription['Change_date'])?></td>
                    <td><?= esc($transcription['BMD_submit_date'])?></td>
                    <td class="next_action"
                        title="ClickMe for Upload detail"
                        data-id='<?=esc($transcription['BMD_header_index'])?>'
                        data-action='UPDET'>
                        <?= esc($transcription['BMD_submit_status'])?>
                    </td>

                    <td class="next_action"
                        title="<?=esc($transcription['comment_text'])?>"
                        data-id='<?=esc($transcription['BMD_header_index'])?>'
                        data-action='UPCOM'>
                        <?php
                        if ( !is_null($transcription['comment_text']) )
                        {
                            echo esc(ellipsize($transcription['comment_text'], 100, .5, '...'));
                        }
                        else
                        {
                            echo esc($transcription['comment_text']);
                        }
                        ?>
                    </td>

                    <td><?= esc($transcription['BMD_last_action'])?></td>

                    <?php
                    if ( $session->status == '0' )
                    {
                        ?>
                        <td>
                            <select class="box" name="next_action" id="next_action">
                                <?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
                                    <?php if ( $transcription_cycle['BMD_cycle_type'] == 'TRANS' ): ?>
                                        <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
                                            <?= esc($transcription_cycle['BMD_cycle_name'])?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <?php
                    }
                    ?>

                    </tr>
                </tbody>
          </div>
        </div>
      </div>
    </main>

    <script src="./script.js"></script>
  </body>
</html>