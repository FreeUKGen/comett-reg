<?php namespace App\Models;

use CodeIgniter\Model;

class Document_Sources_Model extends Model
{
    protected $table = 'document_sources';
    protected $primaryKey = 'document_source';
    protected $allowedFields = ['document_source', 'document_source_popularity'];
    protected $returnType = 'array';
}
