<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmoCRMData extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'amocrmtable';
    protected $fillable = ['key', 'value'];
}