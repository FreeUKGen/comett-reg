<?php $session = session(); ?>
    <main class="gutter">
        <section class="breadcrumb-trail row py-4">
            <div class="breadcrumb">
                <a href="#">Home</a>
                <img
                        src="/images/icon-breadcrumb-arrow.svg"
                        alt="breadcrumb trail next item indicator"
                />
                <a href="#">Section</a>
                <img
                        src="/images/icon-breadcrumb-arrow.svg"
                        alt="breadcrumb trail next item indicator"
                />
                <span>Current Page</span>
            </div>
            <div class="user-information col-5">
                <div class="row">
                    <span>Records transcribed - xxxxxxx</span>
                    <span>Victoria Erdelevskaya - in  test 3</span>
                </div>
            </div>
        </section>
        <h1 class="fg-bold">Load CSV file - select a file to download</h1>
        <section class="py-4">
            <div class="row mb-4">
                <div>
                    <h3 class="mb-2 fg-bold">Your CSV Files in FreeComETT</h3>
                    <p>Double click the header to sort by it</p>
                </div>
            </div>
            <div class="responsive-table-wrapper">
                <table class="stripped">
                    <thead>
                        <tr>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by CSV File Name"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> CSV File Name</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Place"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Place</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Church"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Church</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Entries"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Entries</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Start Year"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Start Year</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by End Year"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> End Year</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Processed"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Processed</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Locked TR"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Locked TR</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Locked SC"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Locked SC</div></th>
                        </tr>
                    </thead>
                    <tbody>
					<?php if (isset($session->physical_files)) : 
                    foreach ( $session->physical_files as $physical_file )
					{ ?>
                        <tr>
                            <td class="align-middle"><?= esc($physical_file['file_name'])?></td>

                            <td>Text Cell</td>
                            <td>Text Cell</td>
                            <td>21</td>
                            <td>xxxxxxxxxx</td>
                            <td>Text Cell</td>
                            <td class="align-middle"><?= esc($physical_file['proc_date'])?></td>
                            <td>Y</td>
                            <td>N</td>
                        </tr>
                        <?php
                    } ?>
                    </tbody>
					<?php endif; ?>
                </table>
            </div>
        </section>
        <section class="py-4">
            <div class="row mb-3">
                <div>
                    <h3 class="mb-2 fg-bold">Your CSV Files on the FreeREG server</h3>
                    <p>Double click the header to sort by it</p>
                </div>
                <input type="search" placeholder="Search" id="search" />
            </div>
            <div class="responsive-table-wrapper">
                <table class="stripped">
                    <thead>
                        <tr>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by CSV File Name"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> CSV File Name</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Place"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Place</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Church"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Church</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Entries"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Entries</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Start Year"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Start Year</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by End Year"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> End Year</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Processed"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Processed</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Locked TR"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Locked TR</div></th>
                            <th><div class="row"><span role="button" tabindex="0" aria-label="Sort by Locked SC"><img src="/css/images/freecomett-sort.svg" alt="" aria-hidden="true"></span> Locked SC</div></th>
                        </tr>
                    </thead>
                    <tbody>
					<?php if (isset($session->physical_files)) : 
                    foreach ( $session->physical_files as $physical_file )
                    { ?>
                        <tr>
                            <td class="align-middle"><?= esc($physical_file['file_name'])?></td>

                            <td>Text Cell</td>
                            <td>Text Cell</td>
                            <td>21</td>
                            <td>xxxxxxxxxx</td>
                            <td>Text Cell</td>
                            <td class="align-middle"><?= esc($physical_file['proc_date'])?></td>
                            <td>Y</td>
                            <td>N</td>
                        </tr>
                        <?php
                    } ?>
                    </tbody>
					<?php endif; ?>
                </table>
            </div>
        </section>
    </main>
    
  <footer class="row mt-3 ml-1">
    This is the demo footer
  </footer>
  </body>
</html>
