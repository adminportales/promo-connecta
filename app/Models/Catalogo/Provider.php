<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
	use HasFactory;

    public $timestamps = true;

    protected $table = 'providers';

    protected $connection = 'mysql_catalogo';

    protected $fillable = ['company','email','phone','contact','discount'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Catalogo\Product', 'provider_id', 'id');
    }

}
