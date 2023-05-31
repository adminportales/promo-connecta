<?php

namespace App\Http\Controllers;

use App\Models\Catalogo\Category;
use App\Models\Catalogo\GlobalAttribute;
use App\Models\Catalogo\Provider as CatalogoProvider;
use App\Models\Catalogo\Product as CatalogoProduct;
use App\Models\Catalogo\ProductCategory;
use App\Models\Catalogo\Type;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public $nombre, $sku, $proveedor, $color, $category, $type, $precioMax, $precioMin, $stockMax, $stockMin, $orderStock = '', $orderPrice = '';
    public $search;

    public function __construct()
    {

        $utilidad = GlobalAttribute::find(1);
        $utilidad = (float) $utilidad->value;
        $price = DB::connection('mysql_catalogo')->table('products')->max('price');
        $this->precioMax = round($price + $price * ($utilidad / 100), 2);
        $this->precioMin = 0;
        $stock = DB::connection('mysql_catalogo')->table('products')->max('stock');
        $this->stockMax = $stock;
        $this->stockMin = 0;

        try {
            $this->search = session()->get('busqueda', "");
            session()->put('busqueda', '');
            $this->nombre = $this->search;
        } catch (Exception $th) {
            //throw $th;
        }

        $utilidad = GlobalAttribute::find(1);
        $utilidad = (float) $utilidad->value;

        $price = DB::connection('mysql_catalogo')->table('products')->max('price');
        $this->precioMax = round($price + $price * ($utilidad / 100), 2);
        $this->precioMin = 0;
        $stock = DB::connection('mysql_catalogo')->table('products')->max('stock');
        $this->stockMax = $stock;
        $this->stockMin = 0;
        $this->type = 1;
    }

    public function catalogo()
    {
        $utilidad = GlobalAttribute::find(1);
        $utilidad = (float) $utilidad->value;

        // Agrupar Colores similares
        $types = Type::find([1, 2]);
        $price = DB::connection('mysql_catalogo')->table('products')->max('price');
        $price = round($price + $price * ($utilidad / 100), 2);
        $stock = DB::connection('mysql_catalogo')->table('products')->max('stock');
        $proveedores = CatalogoProvider::where('id', "<", 15)->where('id', "<>", 13)->get();
        $nombre = '%' . $this->nombre . '%';
        $sku = '%' . $this->sku . '%';
        $color = $this->color;
        $category = $this->category;
        $type =  $this->type == null ? "" : $this->type;
        $precioMax = $price;
        if ($this->precioMax != null) {
            $precioMax =  round($this->precioMax / (($utilidad / 100) + 1), 2);
        }
        $precioMin = 0;
        if ($this->precioMin != null) {
            $precioMin =  round($this->precioMin / (($utilidad / 100) + 1), 2);
        }
        $stockMax =  $this->stockMax;
        $stockMin =  $this->stockMin;
        if ($stockMax == null) {
            $stockMax = $stock;
        }
        if ($stockMin == null) {
            $stockMin = 0;
        }
        $orderPrice = $this->orderPrice;
        $orderStock = $this->orderStock;

        if ($type == '2') {
            $this->proveedor = null;
        }

        $products  = CatalogoProduct::with(['images', 'color', 'productAttributes', 'category'])->leftjoin('product_category', 'product_category.product_id', 'products.id')
            ->leftjoin('categories', 'product_category.category_id', 'categories.id')
            ->leftjoin('colors', 'products.color_id', 'colors.id')
            ->where('products.name', 'LIKE', $nombre)
            ->where('products.visible', '=', true)
            ->where('products.sku', 'LIKE', $sku)
            ->whereBetween('products.price', [$precioMin, $precioMax])
            ->whereBetween('products.stock', [$stockMin, $stockMax])
            ->when($type == '1', function ($query, $proveedor) {
                $query->where('products.provider_id', 'LIKE', $this->proveedor);
                // $query->orderBy('products.stock', $this->orderStock);
            })
            ->where('products.type_id', 'LIKE', $type)
            ->when($orderStock !== '', function ($query, $orderStock) {
                $query->orderBy('products.stock', $this->orderStock);
            })
            ->when($orderPrice !== '', function ($query, $orderPrice) {
                $query->orderBy('products.price', $this->orderPrice);
            })
            ->where('products.price', '>', 0)
            ->when($color !== '' && $color !== null, function ($query, $color) {
                $newColor  = '%' . $this->color . '%';
                $query->where('colors.color', 'LIKE', $newColor);
            })
            ->when($category !== '' && $category !== null, function ($query, $category) {
                $newCat  = '%' . $this->category . '%';
                $query->where('categories.family', 'LIKE', $newCat);
            })
            ->select('products.*')
            ->paginate(32);
        return response()->json([
            'products' => $products,
            'utilidad' => $utilidad,
            'types' => $types,
            'price' => $price,
            'priceMax' => $precioMax,
            'priceMin' => $precioMin,
            'stock' => $stock,
            'stockMax' => $stockMax,
            'stockMin' => $stockMin,
            'orderStock' => $orderStock,
        ], 200);
    }

    public function verProducto(CatalogoProduct $product)
    {
        $utilidad = GlobalAttribute::find(1);
        $utilidad = (float) $utilidad->value;
        $msg = '';
        // Consultar las existencias de los productos en caso de ser de Doble Vela.
        if ($product->provider_id == 5) {
            $cliente = new \nusoap_client('http://srv-datos.dyndns.info/doblevela/service.asmx?wsdl', 'wsdl');
            $error = $cliente->getError();
            if ($error) {
                echo 'Error' . $error;
            }
            //agregamos los parametros, en este caso solo es la llave de acceso
            $parametros = array('Key' => 't5jRODOUUIoytCPPk2Nd6Q==', 'codigo' => $product->sku_parent);
            //hacemos el llamado del metodo
            $resultado = $cliente->call('GetExistencia', $parametros);
            $msg = '';
            if (array_key_exists('GetExistenciaResult', $resultado)) {
                $informacionExistencias = json_decode(utf8_encode($resultado['GetExistenciaResult']))->Resultado;
                if (count($informacionExistencias) > 1) {
                    foreach ($informacionExistencias as $productExistencia) {
                        if ($product->sku == $productExistencia->CLAVE) {
                            $product->stock = $productExistencia->EXISTENCIAS;
                            $product->save();
                            break;
                        }
                        $msg = "Este producto no se encuentra en el catalogo que esta enviado DV via Servicio WEB";
                    }
                } else {
                    $msg = "Este producto no se encuentra en el catalogo que esta enviado DV via Servicio WEB";
                }
            } else {
                $msg = "No se obtuvo informacion acerca del Stock de este producto. Es posible que los datos sean incorrectos";
            }
        }

        return response()->json([
            'product' => $product,
            // 'utilidad' => $utilidad,
            'msg' => $msg
        ], 200);
    }
}
