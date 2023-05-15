<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'subcategories';

    protected $connection = 'mysql_catalogo';

    protected $fillable = ['subfamily', 'slug', 'category_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne('App\Models\Catalogo\Category', 'id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productCategories()
    {
        return $this->hasMany('App\Models\Catalogo\ProductCategory', 'subcategory_id', 'id');
    }
}
