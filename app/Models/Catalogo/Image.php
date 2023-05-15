<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'images';

    protected $connection = 'mysql_catalogo';

    protected $fillable = ['image_url','product_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne('App\Models\Catalogo\Product', 'id', 'product_id');
    }

}
