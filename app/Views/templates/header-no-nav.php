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
</head>

<body>
  <div class="container">
      <header>
        <div class="image-container">
          <a href="/">
            <img
              alt="Free UK Genealogy - Human transcription of family history data"
              src="/images/freeukgen-icon.png"
            />
          </a>
        </div>
        <div class="image-container">
          <img
            alt="FreeComETT"
            src="/images/FreeComETT.png"
          />
          <p><strong>Com</strong>munity <strong>E</strong>ntity <strong>T</strong>ranscription <strong>T</strong>ool</p>
        </div>
      </header>
