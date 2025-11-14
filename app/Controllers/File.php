<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;

class File extends BaseController
{
	#[NoReturn]
	public function upload(): string
	{
		// create the BMD upload file - this stores the csv file in the DB
		// $this->create_BMD_file();

		$validationRule = [
			'userfile' => [
				'label' => 'Quiz data file',
				'rules' => [
					'uploaded[userfile]',
					'is_image[userfile]',
					'mime_in[userfile,text/plain,text/csv]',
					'max_size[userfile,100]',
					'max_dims[userfile,1024,768]',
				],
			],
		];
		if (!$this->validateData([], $validationRule)) {
			$data = ['errors' => $this->validator->getErrors()];
			die(json_encode($data));
		}

		$img = $this->request->getFile('userfile');

		if (!$img->hasMoved()) {
			$filepath = WRITEPATH . 'uploads/' . $img->store();
			$data = ['ok' => true, 'uploaded_fileinfo' => new \CodeIgniter\Files\File($filepath)];
			die(json_encode($data));
		}

		$data = ['errors' => 'The file has already been moved.'];
		die(json_encode($data));
	}
}