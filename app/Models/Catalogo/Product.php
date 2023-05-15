<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'products';

    protected $connection = 'mysql_catalogo';

    protected $fillable = [
        'internal_sku',
        'sku_parent',
        'sku',
        'name',
        'price',
        'description',
        'producto_promocion',
        'descuento',
        'producto_nuevo',
        'precio_unico',
        'stock',
        'type_id',
        'color_id',
        'provider_id',
        'visible'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\Catalogo\Image', 'product_id', 'id');
    }

    public function firstImage()
    {
        return $this->hasOne('App\Models\Catalogo\Image', 'product_id', 'id')->oldestOfMany();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productAttributes()
    {
        return $this->hasMany('App\Models\Catalogo\ProductAttribute', 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productCategories()
    {
        return $this->hasMany('App\Models\Catalogo\ProductCategory', 'product_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Catalogo\Category', 'product_category', 'product_id', 'category_id');
    }

    public function precios()
    {
        return $this->hasMany('App\Models\Catalogo\Price', 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Catalogo\Provider');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sitesProducts()
    {
        return $this->belongsToMany(Site::class, 'sites_products', 'product_id', 'site_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne('App\Models\Catalogo\Type', 'id', 'type_id');
    }
}
