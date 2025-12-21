<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Allocation_Images_Model;
use JetBrains\PhpStorm\NoReturn;
class Image extends BaseController
{
	function __construct()
	{
		helper('common');
	}

	/**
	 * POST /image/rotate
	 * Accepts POST params: image_index, degrees
	 * Rotates the image, saves a rotated copy under public/uploads/rotated/<allocation_index>/
	 * Updates the DB record and returns JSON {ok:true, image_url:...}
	 */
	public function rotate(): ResponseInterface
	{
		$imageIndex = (int)$this->request->getPost('image_index');
		$degrees = (int)$this->request->getPost('degrees');

		if (!is_numeric($imageIndex) || !is_numeric($degrees)) {
			return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'invalid_input']);
		}

		$imageIndex = (int) $imageIndex;
		$degrees = (int) $degrees;

		// normalize degrees to 0-359
		$degrees = ($degrees % 360 + 360) % 360;

		if ($degrees === 0) {
			return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'no_rotation']);
		}

		if ($degrees % 90 !== 0) {
			return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'invalid_degrees']);
		}

		$image = get_image_info($imageIndex);
		if (!$image) {
			return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => 'not_found']);
		}
//		$model = new Allocation_Images_Model();
//		$image = $model->find($imageIndex);

		$imageUrl = $image['image_url'] ?? '';
		$imageFile = $image['image_file_name'] ?? '';
		$allocationIndex = $image['allocation_index'] ?? 'unknown';

		log_message('info', 'URL:' . $imageUrl);
		log_message('info', 'FIL:' . $imageFile);

		if (str_contains($imageUrl, '://')) {
			$path = parse_url($imageUrl, PHP_URL_PATH);
		} else {
			$path = $imageUrl;
		}

		$publicPath = $path;
		//$publicPath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
		//if (!file_exists($publicPath)) {
		//	return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => 'file_not_found']);
		//}

		$info = @getimagesize($path);
		if ($info === false) {
			return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'invalid_image']);
		}

		$mime = $info['mime'] ?? '';
		switch ($mime) {
			case 'image/jpeg':
			case 'image/jpg':
				$src = @imagecreatefromjpeg($publicPath);
				break;
			case 'image/png':
				$src = @imagecreatefrompng($publicPath);
				break;
			case 'image/gif':
				$src = @imagecreatefromgif($publicPath);
				break;
			default:
				return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'unsupported_format']);
		}

		if ($src === false) {
			return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'error' => 'failed_to_load_image']);
		}

		// GD's imagerotate rotates counter-clockwise; convert degrees
		$ccwDegrees = (360 - ($degrees % 360)) % 360;

		$bg = 0;
		if (in_array($mime, ['image/png', 'image/gif'], true)) {
			imagealphablending($src, true);
			imagesavealpha($src, true);
			$bg = imagecolorallocatealpha($src, 0, 0, 0, 127);
		}

		$rotated = @imagerotate($src, $ccwDegrees, $bg);
		if ($rotated === false) {
			imagedestroy($src);
			return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'error' => 'rotate_failed']);
		}

		if (in_array($mime, ['image/png', 'image/gif'], true)) {
			imagealphablending($rotated, false);
			imagesavealpha($rotated, true);
		}

		// Save rotated copy under public/uploads/rotated/<allocation_index>/
//		$destDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'rotated' . DIRECTORY_SEPARATOR . $allocationIndex . DIRECTORY_SEPARATOR;
//		if (!is_dir($destDir)) {
//			mkdir($destDir, 0755, true);
//		}
		$destDir = getenv('app.userDir') . '/Rotated/';

		$ext = pathinfo($publicPath, PATHINFO_EXTENSION) ?: 'jpg';
		$newFilename = 'rotated_' . time() . '_' . $imageIndex . '.' . $ext;
		$destPath = $destDir . $newFilename;

		$saved = false;
		switch ($mime) {
			case 'image/jpeg':
			case 'image/jpg':
				$saved = imagejpeg($rotated, $destPath, 90);
				break;
			case 'image/png':
				$saved = imagepng($rotated, $destPath);
				break;
			case 'image/gif':
				$saved = imagegif($rotated, $destPath);
				break;
		}

		imagedestroy($src);
		imagedestroy($rotated);

		if (!$saved) {
			return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'error' => 'save_failed']);
		}

		// Build web-accessible url relative to public
		$newUrl = '/uploads/rotated/' . $allocationIndex . '/' . $newFilename;

		// Update DB record (image_file_name and image_url)
//		$model->update($imageIndex, [
//			'image_file_name' => $newFilename,
//			'image_url' => $newUrl,
//		]);

		return $this->response->setJSON(['ok' => true, 'image_url' => $newUrl]);
	}
}