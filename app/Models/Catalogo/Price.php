<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'prices';

    protected $connection = 'mysql_catalogo';

    protected $fillable = ['product_id','price','escala'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne('App\Models\Catalogo\Product', 'id', 'product_id');
    }

}
