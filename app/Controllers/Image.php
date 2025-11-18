<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;

class Image
{

	/**
	 * @TODO DS - currently only handles jpegs
	 * @param string $filename
	 * @param int $degrees
	 * @return void
	 */
	#[NoReturn]
	public function rotate(string $filename, int $degrees): void
	{
		$error = null;
		if (is_file($filename)) {
			$source = imagecreatefromjpeg($filename);
			$rotatedImage = imagerotate($source, $degrees, 0);
			if ($rotatedImage) {
				// Yes - rotated!
				die(json_encode(['ok' => true]));
			}
			else
				$error = 'Rotate operation failed';
		}
		else {
			$error = 'file does not exist';
		}
		http_response_code(400);
		die(json_encode(['ok' => false, 'error' => $error]));
	}
}