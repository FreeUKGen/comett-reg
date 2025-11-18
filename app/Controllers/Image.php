<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;

class Image
{

	/**
	 * @TODO DS - currently only handles jpegs
	 * @endpoint POST /image/rotate
	 * @return void
	 */
	#[NoReturn]
	public function rotate(): void
	{
		$error = null;
		$filename = strip_tags($_POST['filename']);
		$degrees = (int)$_POST['degrees'];
		if (!$degrees)
			$error = 'No rotation to do';
		if (!$error && !$filename)
			$error = 'No file specified';
		if ($error && !is_file($filename))
			$error = 'File does not exist';

		if (!$error && is_file($filename)) {
			$source = imagecreatefromjpeg($filename);
			$rotatedImage = imagerotate($source, $degrees, 0);
			if ($rotatedImage) {
				// Yes - rotated!
				die(json_encode(['ok' => true]));
			}
			else
				$error = 'Rotate operation failed';
		}
		http_response_code(400);
		die(json_encode(['ok' => false, 'error' => $error]));
	}
}