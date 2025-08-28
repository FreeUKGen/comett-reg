<?php

use App\Models\Firstname_Model;
use App\Models\Surname_Model;
use App\Models\Occupation_Model;
use App\Models\Parish_Model;

function update_firstnames($name)
	{
		// initialise
		$session = session();
		$firstname_model = new Firstname_Model();
		// exclude names with . in them as these are probably initials and double barrel
		
		// exists already?
		if ( strlen($name) > 2 AND strpos($name, '.') === false AND strpos($name, '-') === false )
			{
				$found_name = $firstname_model->where('Firstname',  $name)->findAll();
				if ( ! $found_name )
					{
						$data = 	[
											'Firstname' => $name,
											'Firstname_popularity' => 1,
										];
						$firstname_model->insert($data);
					}
				else
					{
						$data = ['Firstname_popularity' => $found_name[0]['Firstname_popularity'] + 1,];
						$firstname_model->update($name, $data);
					}
			}
	}
	
function update_surnames($name)
	{
		// initialise
		$session = session();
		$surname_model = new Surname_Model();
		// exists already?
		if ( strlen($name) > 2 )
			{
				$found_name = $surname_model->where('Surname',  $name)->findAll();
				if ( ! $found_name )
					{
						$data = 	[
											'Surname' => $name,
											'Surname_popularity' => 1,
										];
						$surname_model->insert($data);
					}
				else
					{
						$data = ['Surname_popularity' => $found_name[0]['Surname_popularity'] + 1,];
						$surname_model->update($name, $data);
					}
			}
	}
	
function update_occupations($name)
	{
		// initialise
		$session = session();
		$occupation_model = new Occupation_Model();
		// exists already?
		if ( strlen($name) > 2 )
			{
				$found_name = $occupation_model->where('Occupation',  $name)->findAll();
				if ( ! $found_name )
					{
						$data = 	[
											'Occupation' => $name,
											'Occupation_popularity' => 1,
										];
						$occupation_model->insert($data);
					}
				else
					{
						$data = ['Occupation_popularity' => $found_name[0]['Occupation_popularity'] + 1,];
						$occupation_model->update($name, $data);
					}
			}
	}
	
function update_parishes($name)
	{
		// initialise
		$session = session();
		$parish_model = new Parish_Model();
		// exists already?
		if ( strlen($name) > 2 )
			{
				$found_name = $parish_model->where('Parish',  $name)->findAll();
				if ( ! $found_name )
					{
						$data = 	[
											'Parish' => $name,
											'Parish_popularity' => 1,
										];
						$parish_model->insert($data);
					}
				else
					{
						$data = ['Parish_popularity' => $found_name[0]['Parish_popularity'] + 1,];
						$parish_model->update($name, $data);
					}
			}
	}
