<!DOCTYPE html>
<html>
<head>	
	<!-- initialise session -->
	<?php $session = session(); ?>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes, user-scalable=yes" />
	<title><?= esc($session->title); ?></title>

	<?php include 'header-assets3.php'; ?>

	<link rel="stylesheet" href="<?php echo base_url().'css/fc-reg.css'; ?>">
	<style>
		table {
			font-size:smaller; 
		}
	</style>

</head>

<body>
<div class="container-fluid">
    <header>
      <nav role="navigation">
        <a class="logo" href="https://reg.dev2.freecomett.org.uk/">
          <img src="/css/images/freecomett.svg" alt="FreeComETT logo">
        </a>
        <ul>
          <li>File
            <ul>
				<li><a href="<?php echo base_url('allocation/new_create_assignment')?>">New</a></li>
				<li><a href="<?php echo base_url('allocation/manage_allocations/2')?>">Edit</a></li>
				<li><a href="<?php echo base_url('home/signout')?>">Quit</a></li>
            </ul>
          </li>
          <li>Tools
			<ul>
				<li>Upload CSV</li>
				<li>View Upload Status</li>
				<!---
				<li><a href="<?php echo base_url('allocation/list_images')?>">List Images</a></li>
				-->
				<li>List Images</li>
				<li>Refresh Page</li>
				<li>Log into FreeREG</li>
				<li>Administration</li>
			</ul>
			</li>
          <li>Settings
			<ul>
				<li>Column Layout (admin)</li>
				<li>Refresh Image Load</li>
			</ul>
			</li>
	
          <li>Support
            <ul>
              <li>Help</li>
              <li>Report/View Issues</li>

				<li>Post Comment (LIa)</li>
              <li>Message Coordinator</li>
            </ul>
          </li>
        </ul>
        <a class="logo" href="https://reg.dev2.freecomett.org.uk/">
          <img src="/css/images/freereg.svg" alt="FreeREG logo">
        </a>
      </nav>
    </header>
