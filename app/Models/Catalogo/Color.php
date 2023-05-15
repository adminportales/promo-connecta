<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'colors';

    protected $connection = 'mysql_catalogo';

    protected $fillable = ['color','slug'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Catalogo\Product', 'color_id', 'id');
    }

}
