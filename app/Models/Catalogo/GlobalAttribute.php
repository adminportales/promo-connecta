<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAttribute extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'globalattributes';

    protected $connection = 'mysql_catalogo';

    protected $fillable = ['attribute','value'];

}
