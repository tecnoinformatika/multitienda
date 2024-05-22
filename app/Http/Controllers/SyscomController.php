<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str as Str;
use App\Models\Canal;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use Automattic\WooCommerce\Client as WooCommerceClient;
use Auth;
use Illuminate\Support\Facades\Log;
use Gemini\Laravel\Facades\Gemini;


class SyscomController extends Controller
{


    public function getToken($id,$secret)
    {
        $client = new Client();

        // Obtén las credenciales OAuth de tu archivo .env
        $clientId = $id;
        $clientSecret = $secret;
        $tokenUrl = 'https://developers.syscomcolombia.com/oauth/token';

        try {
            $response = $client->post($tokenUrl, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
                'headers' => [  // Agrega esta sección
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            $body = $response->getBody();
            $token = json_decode($body)->access_token;
            Cache::put('oauth_token', $token, 3600);

            return $token; // Devuelve el token de portador
        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerSubcategorias($categoriaId)
    {
        //$token1 = $this->gettoken();


            $token = Cache::get('oauth_token');


        // Crea una instancia del cliente Guzzle
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        try {
            // Realiza la solicitud GET para obtener todas las categorias
            $response = $client->get('https://developers.syscomcolombia.com/api/v1/categorias/'.$categoriaId);

            // Decodifica la respuesta JSON
            $subcategorias = json_decode($response->getBody(), true);
            return response()->json($subcategorias);

        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function obtenerProductoDetallado($productoid,$id,$secret)
    {

        $token = $this->getToken($id,$secret);

        // Crea una instancia del cliente Guzzle
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
        try {
            // Realiza la solicitud GET para obtener los productos de la categoría
            $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos/'.$productoid);

            // Decodifica la respuesta JSON
            $productos = json_decode($response->getBody()->getContents(), true);

            // Aquí puedes hacer lo que necesites con los productos obtenidos
            return $productos;
        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function productos($categoria_id,$canal)
    {
        $canal2 = Canal::find($canal);

        //dd($canal2);
        // Obtener todos los productos paginados de la categoría
        $productos = $this->obtenerProductosPaginados($categoria_id,$canal2->apikey,$canal2->secret);
        //dd($productos);
        return response()->json($productos);
    }
    public function sincronizarsyscom($idProducto,$canal,$canalelegido,$stock,$aumento)
    {
        //$canal = $request->input('canal_id');
        $stock = $stock;
        $aumento = $aumento;
        //$categoria_id = $request->input('subcategoria2');
        $canalfinal = $canalelegido;
        $canal2 = Canal::find($canal);


        $canal1 = Canal::find($canalfinal);


        $consumerKey = $canal1->apikey;
        $consumerSecret = $canal1->secret;
        $urlClient = $canal1->url;

        $woocommerce = new WooCommerceClient(
            $urlClient,
            $consumerKey,
            $consumerSecret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 60,
                'verify_ssl' => false, // Desactivar la verificación SSL

            ]
        );

        $token = $this->getToken($canal2->apikey,$canal2->secret);

        //dd($categoria_id);
        // Obtener todos los productos paginados de la categoría
        //$productos = $this->obtenerProductosPaginados($categoria_id,$canal2->apikey,$canal2->secret);

        // Procesar los productos obtenidos
        //foreach ($productos as $producto) {
            // Obtener información detallada del producto

            $productoDetallado = $this->obtenerProductoDetallado($idProducto,$canal2->apikey,$canal2->secret);


            try{
                // Iterar sobre las categorías y construir un array de categorías
                $categorias = [];
                foreach ($productoDetallado['categorias'] as $categoria) {
                    $categorias[] = [
                        'id' => $categoria['id'],
                        'nombre' => $categoria['nombre'],
                        'nivel' => $categoria['nivel']
                    ];
                }

                // Crear o actualizar las categorías en WooCommerce
                $this->crearCategoriasWooCommerce($categorias,$canalfinal);



                $this->crearActualizarProductoWooCommerce($productoDetallado,$stock,$aumento,$woocommerce);

                // Crear o actualizar el producto en WooCommerce

                return response()->json([
                    'status' => 'success',
                    'message' => 'El producto se sincronizó correctamente.'
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }



    }

    public function obtenerProductosPaginados($categoriaId,$id, $secret)
    {
        $token = $this->getToken($id,$secret);

        // Crea una instancia del cliente Guzzle
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        $productos = [];
        $pagina = 1;
        do {
            $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos', [
                'query' => [
                    'categoria' => $categoriaId,
                    'pagina' => $pagina,
                    'stock' => true
                ]
            ]);
            $productosPagina = json_decode($response->getBody()->getContents(), true);
            //dd($productosPagina);
            // Agregar los productos de la página actual al array de productos
            // Verificar si hay productos en la página actual
            if (isset($productosPagina['productos']) && !empty($productosPagina['productos'])) {
                // Iterar sobre los productos de la página y agregar solo los campos deseados al array $productos
                foreach ($productosPagina['productos'] as $producto) {
                    $productos[] = [
                        'producto_id' => $producto['producto_id'],
                        'titulo' => $producto['titulo'],
                        'marca' => $producto['marca'],
                        'modelo' => $producto['modelo'],
                    ];
                }
                //dd($productos);
            }

            // Incrementar el número de página para obtener la siguiente página de productos
            $pagina++;

            if($productosPagina['paginas'] < $pagina)
            {
                break;
            }

        } while (!empty($productosPagina)); // Continuar hasta que no haya más productos en la página
        //dd($productos);
        return $productos;
    }
    private function crearCategoriasWooCommerce($categorias,$canalfinal)
    {
        $canal = Canal::find($canalfinal);

        $consumerKey = $canal->apikey;
        $consumerSecret = $canal->secret;
        $urlClient = $canal->url;

        $woocommerce = new WooCommerceClient(
            $urlClient,
            $consumerKey,
            $consumerSecret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'verify_ssl' => false, // Desactivar la verificación SSL

            ]
        );
        $categoriasPadreCreadas = []; // Almacenar las categorías padres creadas
        $categoriasPadreCreadas2 = [];
        // Primero creamos las categorías padre
        foreach ($categorias as $categoria) {
            if ($categoria['nivel'] < 2) {
                // Verificar si la categoría ya existe
                $categoriaExistente = $this->obtenerCategoriaPorNombre($categoria['nombre'], $woocommerce);
                if (!$categoriaExistente) {
                    $categoriaNueva = [
                        'name' => $categoria['nombre'],
                        'slug' => Str::slug($categoria['nombre']),
                        // Otros campos de categoría que puedas necesitar
                    ];
                    $categoriaCreada = $woocommerce->post('products/categories', $categoriaNueva);
                    // Almacenar el ID de la categoría creada
                    $categoriasPadreCreadas['categoria_id'] = $categoriaCreada->id;
                } else {
                    // Si la categoría ya existe, almacenar su ID
                    $categoriasPadreCreadas['categoria_id'] = $categoriaExistente->id;
                }
            }
        }

        // Luego creamos las categorías hijas, asegurándonos de que sus padres existan
        foreach ($categorias as $categoria) {
            if ($categoria['nivel'] > 1 && $categoria['nivel'] < 3 ) {
                //dd($categoriasPadreCreadas['categoria_id']);
                $padreId = $categoriasPadreCreadas['categoria_id']; // Obtener el ID del padre de las categorías hijas

                if ($padreId) {
                    // Verificar si la categoría ya existe
                    $categoriaExistente2 = $this->obtenerCategoriaPorNombre($categoria['nombre'],$woocommerce);
                    if (!$categoriaExistente2) {
                        $categoriaNueva2 = [
                            'name' => $categoria['nombre'],
                            'slug' => Str::slug($categoria['nombre']),
                            'parent' => $padreId,
                            // Otros campos de categoría que puedas necesitar
                        ];
                        $categoriaCreada2 = $woocommerce->post('products/categories', $categoriaNueva2);
                        $categoriasPadreCreadas2['categoria_id'] = $categoriaCreada2->id;
                    } else {
                        // Si la categoría ya existe, almacenar su ID
                        $categoriasPadreCreadas2['categoria_id'] = $categoriaExistente2->id;
                    }
                } else {
                    // Manejar el caso en el que no se pueda encontrar el padre
                    // Puede registrar un error o realizar otra acción según sea necesario
                    // En este caso, simplemente lo estamos omitiendo
                }
            }

        }
        foreach ($categorias as $categoria)
        {
            if ($categoria['nivel'] > 2) {
                //dd($categoriasPadreCreadas2);
                $padreId2 = $categoriasPadreCreadas2['categoria_id']; // Obtener el ID del padre de las categorías hijas

                if ($padreId2) {
                    // Verificar si la categoría ya existe
                    // Verificar si la categoría ya existe
                    $categoriaExistente3 = $this->obtenerCategoriaPorNombre($categoria['nombre'],$woocommerce);
                    if (!$categoriaExistente3) {
                        $categoriaNueva3 = [
                            'name' => $categoria['nombre'],
                            'slug' => Str::slug($categoria['nombre']),
                            'parent' => $padreId2,
                            // Otros campos de categoría que puedas necesitar
                        ];
                        $woocommerce->post('products/categories', $categoriaNueva3);

                    } else {
                        // Si la categoría ya existe, almacenar su ID

                    }
                } else {
                    // Manejar el caso en el que no se pueda encontrar el padre
                    // Puede registrar un error o realizar otra acción según sea necesario
                    // En este caso, simplemente lo estamos omitiendo
                }
            }
        }
    }
    private function obtenerCategoriaPorNombre($nombre,$woocommerce)
    {
        // Obtener la categoría por su nombre (slug)
        $categoria = $woocommerce->get('products/categories', ['slug' => Str::slug($nombre)]);
        //dd($categoria);
        if (!empty($categoria)) {
            return $categoria[0]; // Devolver la primera categoría encontrada
        } else {
            return null; // Si no se encuentra la categoría, devolver null
        }
    }
    private function buscarCategoriaPadre($categorias, $nivel)
    {
        foreach ($categorias as $categoria) {
            if ($categoria['nivel'] == $nivel) {
                return $categoria;
            }
        }
        return null;
    }

    private function crearActualizarProductoWooCommerce($producto,$stock,$aumento,$woocommerce)
    {


        $attribute_name = 'Marca';

        $data = [
        'name' => $attribute_name,
        'slug' => 'pa_marca',
        'type' => 'select',
        'order_by' => 'menu_order',
        'has_archives' => true,
        'taxonomy' => 'pa_marca', // Nombre del taxonomía
        ];

        // Obtener el ID del atributo
        $response = $woocommerce->get('products/attributes', [
        'query' => [
            'search' => $attribute_name,
        ],
        ]);



        if ( isset($response) ) {

                // El atributo no existe, crearlo.
                $response = $woocommerce->post('products/attributes', $data);


                if ( !empty($response )) {
                    $attribute_id = $response->id;
                } else {
                    // Ha habido un error al crear el atributo.
                    return response()->json([
                        'success' => false,
                        'message' => 'Ha habido un error al crear el atributo.',
                        'errors' => $response->json(),
                    ]);
                }

        } else {

            $attribute_id = $response[0]->id;
        }
        //dd($producto);
        // Verificar si el producto ya existe en WooCommerce
        $productoWooCommerce = $woocommerce->get('products', ['sku' => $producto['modelo']]);
        //dd($productoWooCommerce);
            $existencia = $producto['existencia']['nuevo'];
            if (strpos($existencia, '+') !== false) {
                // Si encontramos el símbolo '+', reemplazamos por un espacio en blanco y sumamos 1
                $existencia = (int) str_replace('+', '', $existencia) + 1;
            } else {
                // Si no hay símbolo '+', simplemente convertimos a entero
                $existencia = (int) $existencia;
            }
        if (empty($productoWooCommerce)) {

                // Si el producto no existe, lo creamos

                $imagenes = $producto['imagenes'];
                $imagenesWooCommerce = [];
                $titulo = $producto['titulo'];
                $marca = $producto['marca'];
                $sku = $producto['modelo'];
                $context = [
                    "safetyRatings" => [
                        "url" => "https://es.wikipedia.org/wiki/Seguridad_del_autom%C3%B3vil",
                    ],
                ];
                $promtnombre = 'genera un nombre de producto en español entendible maximo 120 caracteres, que no lleve asteriscos, ni salto de pagina, que me permita entender al cliente rapidamente que producto es basandote en lo siguiente:'.$titulo. 'Dejando al final siempre esto: ,'.$marca.' '.$sku;
                $resultnombre = Gemini::geminiPro()->generateContent($promtnombre);
                $nombrepro = $resultnombre->text();
                $promtdesc = 'genera una descripcion de 2 parrafos bien explicados con titulo h2 de producto en español entendible, que me permita entender al cliente rapidamente que producto es basandote en lo siguiente:'.$titulo.'; Que sea compleatemente SEO compatible con esto: '.$nombrepro.'; no te olvides de incluir en el titulo la marca y la referencia o sku que son: '.$marca.' '.$sku;
                $resultdescripcion = Gemini::geminiPro()->generateContent($promtdesc);
                $descripcioncorta = $resultdescripcion->text();
                // $resultkeywords = Gemini::geminiPro()->generateContent('De acuerdo a lo siguiente: '.$descripcioncorta.'; generame las keywords para posicionamiento seo siguiente los parametros del algoritmo de google y pues basandote en el texto que te doy, maximo 3 keywords, separadas por coma, sin saltos de pagina y sin caracteres extraños');
                // $keywords = $resultkeywords->text();
                // $resultmetadesc = Gemini::geminiPro()->generateContent('De acuerdo a lo siguiente: '.$descripcioncorta.'; y a las keywords: '.$keywords.'; generame una meta descripción optimizada para seo segun los parametros del algoritmo del buscador de google de maximo 160 caracteres, esto es sumamente importante el tamaño para que no se corte, puedes generar emojis, si es posible');
                // $metadesc = $resultmetadesc->text();
                //dd($metadesc);
                if ($existencia > 0) {
                    // Si el inventario está disponible, establecer el estado del stock en "En stock"
                    $stock_status = 'instock';
                } else {
                    // Si el inventario no está disponible, establecer el estado del stock en "Fuera de stock"
                    $stock_status = 'outofstock';
                }
                //dd($producto['producto_id']);

                if(!empty($producto['img_portada'])){
                    try {
                        $tempDirectory = public_path('temp');
                        $tempImageFilePath = tempnam($tempDirectory, 'producto_img_');
                        file_put_contents($tempImageFilePath, file_get_contents($producto['img_portada']));

                        // Mover la imagen temporal al almacenamiento de Laravel (storage)
                        $nombreArchivo = $marca.''.$sku.''.uniqid() . '.jpg'; // Generar un nombre de archivo único
                        $rutaDestino = public_path('temp/' . $nombreArchivo);
                        rename($tempImageFilePath, $rutaDestino);
                        //Storage::disk('temp')->put($nombreArchivo, file_get_contents($tempImageFilePath));
                        chmod($rutaDestino, 0777);
                        // Obtener la URL pública del archivo guardado en la carpeta public/temp
                        $urlImagenTemporal = asset('temp/' . $nombreArchivo);

                        // Crear el array para la imagen principal en el formato requerido por WooCommerce
                        $imagenPrincipal = [
                            'src' => $urlImagenTemporal,
                            'alt' => 'Imagen principal de ' . $nombrepro . ' '. $producto['titulo'] .' Novatics Colombia, proveedores de tecnología en Colombia y LATAM',
                            'name' => $nombrepro .' Novatics Colombia, proveedores de tecnología en Colombia y LATAM',
                        ];

                        //  dd($imagenPrincipal);
                        // Añadir la imagen principal al array de imágenes de WooCommerce
                        $imagenesWooCommerce[] = $imagenPrincipal;
                    } catch (\Exception $e) {
                        // Manejar la excepción (por ejemplo, registrarla para futura revisión)
                        // En este caso, puedes decidir si omitir la imagen o manejar el error de otra manera
                    }
                }

                // Agregar imágenes de la galería
                // Recorrer todas las imágenes de la galería
                foreach ($producto['imagenes'] as $imagen) {
                    try {
                        if (!empty($imagen['imagen'])) {
                            // Descargar la imagen y guardarla en un archivo temporal
                            $tempDirectory = public_path('temp');
                            $tempImageFilePath = tempnam($tempDirectory, 'producto_img_');
                            file_put_contents($tempImageFilePath, file_get_contents($imagen['imagen']));

                            // Mover la imagen temporal a la carpeta public/temp
                            $nombreArchivo = $marca.''.$sku.''.uniqid() . '.jpg'; // Generar un nombre de archivo único
                            $rutaDestino = public_path('temp/' . $nombreArchivo);
                            rename($tempImageFilePath, $rutaDestino);
                            chmod($rutaDestino, 0777);
                            // Obtener la URL pública del archivo guardado en la carpeta public/temp
                            $urlImagenTemporal = asset('temp/' . $nombreArchivo);

                            // Crear el array para la imagen de la galería en el formato requerido por WooCommerce
                            $imagenGaleria = [
                                'src' => $urlImagenTemporal,
                                'alt' => 'Imagen de la galería de ' . $nombrepro .' Novatics Colombia, proveedores de tecnología en Colombia y LATAM',
                                'name' => $nombrepro .' Novatics Colombia, proveedores de tecnología en Colombia y LATAM',
                            ];

                            // Añadir la imagen de la galería al array de imágenes de WooCommerce
                            $imagenesWooCommerce[] = $imagenGaleria;
                        }
                    } catch (\Exception $e) {
                        // Manejar la excepción (por ejemplo, registrarla para futura revisión)
                        // En este caso, puedes decidir si omitir la imagen o manejar el error de otra manera
                        continue;
                    }
                }
                $porcentaje = $aumento; // El porcentaje que deseas convertir
                $decimal = $porcentaje / 100; // Convertir a forma decimal

                //dd($imagenesWooCommerce);
                $precio_descuento = ($producto['precios']['precio_descuento'] * $decimal) + $producto['precios']['precio_descuento'];
                $precio_especial = $producto['precios']['precio_especial'] * 1.2;
                //dd('es asi: '.$producto['precios']['precio_descuento'] . 'y quedo asi :'.$precio_descuento);
                // Redondear los precios a dos decimales
                $precio_descuento = number_format($precio_descuento, 2, '.', '');
                $precio_especial = number_format($precio_especial, 2, '.', '');
                //dd((double)($producto['precios']['precio_especial'] * 1.2));
                $productoNuevo = [
                    'name' => $nombrepro,
                    'sku' => $producto['modelo'],
                    'slug' => Str::slug($nombrepro),
                    'type' => 'simple',
                    'status' => 'publish',
                    'catalog_visibility' => 'visible',
                    'description' => $producto['descripcion'],
                    'short_description' => $descripcioncorta,
                    'regular_price' => $precio_descuento,
                    'purchasable' => true,
                    'downloads' => [],
                    'tax_status' => 'taxable',
                    'manage_stock' => true,
                    'stock_quantity' => $existencia,
                    'weight' => $producto['peso'],
                    'dimensions' => [
                        'length' => $producto['largo'],
                        'width' => $producto['ancho'],
                        'height' => $producto['alto']
                    ],
                    'shipping_required' => true,
                    'shipping_taxable' => false,
                    'reviews_allowed' => true,
                    'stock_status' => $stock_status,
                    'images' => $imagenesWooCommerce,
                    // Otros campos de producto que puedas necesitar
                ];
                // Añadir atributo de Marca al producto
                $productoNuevo['attributes'] = [
                    [
                        'id' => $attribute_id,
                        'name' => $attribute_name,
                        'options' => [$marca], // Valor del atributo (Nombre de la marca)
                    ],
                ];


                // Convertir los recursos del producto al formato de descargas de WooCommerce
                foreach ($producto['recursos'] as $recurso) {
                    $descarga = [
                        'name' => $recurso['recurso'], // Nombre de la descarga
                        'file' => $recurso['path'],    // URL del archivo
                    ];
                    $productoNuevo['downloads'][] = $descarga; // Agregar la descarga al array de descargas
                }

                // Asignar categorías al producto
                $categoriasProducto = [];
                foreach ($producto['categorias'] as $categoria) {
                    $categoriasProducto[] = ['id' => $this->obtenerIdCategoriaWooCommerce($categoria['nombre'],$woocommerce)];
                }

                $productoNuevo['categories'] = $categoriasProducto;

                $creado = $woocommerce->post('products', $productoNuevo);
                $product_id = $creado->id; // ID del producto


                if ($creado) {
                    //dd("probar");
                    Log::info('Producto: '.$nombrepro.' creado correctamente');
                    // El producto se ha creado correctamente
                    return response()->json([
                        'success' => true,
                        'message' => 'El producto se ha creado correctamente.',
                    ]);
                } else {
                    // Ha habido un error al crear el producto
                    return response()->json([
                        'success' => false,
                        'message' => 'Ha habido un error al crear el producto.',
                        'errors' => $creado->json(),
                    ]);
                }



        }else if ($existencia > 0) {
                $porcentaje = $aumento; // El porcentaje que deseas convertir
                $decimal = $porcentaje / 100; // Convertir a forma decimal

                //dd($imagenesWooCommerce);
                $precio_descuento = ($producto['precios']['precio_descuento'] * $decimal) + $producto['precios']['precio_descuento'];
                $precio_especial = $producto['precios']['precio_especial'] * 1.2;
                //dd('es asi: '.$producto['precios']['precio_descuento'] . 'y quedo asi :'.$precio_descuento);
                // Redondear los precios a dos decimales
                $precio_descuento = number_format($precio_descuento, 2, '.', '');
                $precio_especial = number_format($precio_especial, 2, '.', '');

            // Actualizar el stock en WooCommerce
            $woocommerce->post('products/' . $productoWooCommerce[0]->id, [
                'stock_quantity' => $existencia,
                'regular_price' => $precio_descuento
                // Aquí puedes añadir más campos para actualizar según tus necesidades
            ]);

            // Puedes añadir aquí más acciones si es necesario
        }


    }

    private function actualizarProductoWooCommerce($producto, $productoId, $existencia, $woocommerce)
    {
        try {
            $precio_descuento = $producto['precios']['precio_descuento'] * 1.2;
            $precio_descuento = number_format($precio_descuento, 2, '.', '');

            $data = [
                'stock_quantity' => $existencia,
                'regular_price' => $precio_descuento,
            ];

            $woocommerce->post('products/' . $productoId, $data);

            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            Log::error('Error en actualizarProductoWooCommerce: ' . $e->getMessage());
            return [
                'success' => false,
                'errors' => $e->getMessage(),
            ];
        }
    }

    function descargarImagen($url, $directorioTemporal) {
        $nombreArchivo = basename($url);
        $rutaArchivoTemporal = $directorioTemporal . '/' . $nombreArchivo;

        // Descargar la imagen
        $contenidoImagen = @file_get_contents($url);

        if ($contenidoImagen !== false) {
            // Guardar la imagen en el directorio temporal
            file_put_contents($rutaArchivoTemporal, $contenidoImagen);
            return $rutaArchivoTemporal; // Devolver la ruta del archivo descargado
        } else {
            return null; // La descarga falló, devolver null
        }
    }


    private function obtenerIdCategoriaWooCommerce($nombreCategoria,$woocommerce)
    {
        // Obtener el ID de la categoría en WooCommerce
        $categoriaWooCommerce = $woocommerce->get('products/categories', ['slug' => Str::slug($nombreCategoria)]);

        // Verificar si se encontró la categoría
        if (isset($categoriaWooCommerce[0]->id)) {
            return $categoriaWooCommerce[0]->id;
        } else {
            return null;
        }
    }

    //codigo para ir a la vista normal
    public function destinosyscom($id)
    {
        $canal = Canal::find($id);
        Cache::put('canal_syscom', $canal->id, 60);
        $canales = Canal::where('user_id', Auth::user()->id)->get();
        $token = $this->getToken($canal->apikey,$canal->secret);

        // Crea una instancia del cliente Guzzle
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        try {
            // Realiza la solicitud GET para obtener todas las categorias
            $response = $client->get('https://developers.syscomcolombia.com/api/v1/categorias');

            // Decodifica la respuesta JSON
            $categorias = json_decode($response->getBody(), true);

            return view('canal/destino-syscom')
                    ->with('canal',$canal)
                    ->with('canales',$canales)
                    ->with('categorias',$categorias);


        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }

    public function importSyscom($categoria_id)
    {
        $clientId = env('SYSCOM_CLIENT_ID');
        $clientSecret = env('SYSCOM_CLIENT_SECRET');

        // Verificar si se han definido las credenciales
        if (!$clientId || !$clientSecret) {
            return 'Las credenciales de cliente de SYSCOM Colombia no están definidas en el archivo .env.';
        }

        // Solicitar un token de acceso
        $tokenResponse = Http::asForm()->post('https://developers.syscomcolombia.com/oauth/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        // Verificar si la solicitud de token fue exitosa
        if ($tokenResponse->successful()) {
            $token = $tokenResponse->json('access_token');
            $client = new Client([
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30000, // Ajusta el tiempo de espera aquí (en segundos)
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            // Inicializar variables
            $productosData = [];
            $page = 1;
            $catDatas = [];
            $totalProductos = 0;

            while (true) {
                $catDataSubs = [];
                $responseCatSubs = $client->get('https://developers.syscomcolombia.com/api/v1/categorias/' . $categoria_id);
                $dataSubs = json_decode($responseCatSubs->getBody(), true);
                $catDataSubs = array_merge($catDataSubs, $dataSubs);

                foreach ($catDataSubs['subcategorias'] as $catDataSub) {
                    $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos', [
                        'query' => [
                            'categoria' => $catDataSub['id'],
                            'stock' => 0,
                            'pagina' => $page,
                        ],
                    ]);
                    $data = json_decode($response->getBody(), true);
                    $productosData = array_merge($productosData, $data['productos']);
                    $totalProductos += count($data['productos']); // Sumar la cantidad de productos de la subcategoría

                    if ($page >= $data['paginas']) {
                        break;
                    }

                    // Dividir los productos en grupos de 100
                    $productosChunks = array_chunk($productosData, 100);
                    foreach ($productosChunks as $productoChunk) {
                        // Procesar los productos en grupos de 100
                        procesarProductos($productoChunk, $client);
                    }
                }
                $page++;
            }

            return "Productos importados exitosamente. Total de productos en la categoría $categoria_id: $totalProductos";
        } else {
            return 'Error al obtener el token de acceso de la API de SYSCOM Colombia.';
        }
    }

    private function procesarProductos($productosData,$client)
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 3000 // Set timeout to 30 seconds
            ]
        ]);

        foreach ($productosData as $productoData) {

            $producto  = Producto::where('producto_id', $productoData['producto_id'])->first();

            if(empty($producto))
            {
                $tempDirectory = public_path('temp');

                $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos/'.$productoData['producto_id']);
                $producto = json_decode($response->getBody()->getContents(), true);

                // Generar un nombre de archivo único para la imagen de la marca
                $nombreArchivo = str_replace(' ', '_', $producto['marca']) . '.jpg';
                // Ruta completa para la imagen temporal
                $tempImageFilePath = $tempDirectory . '/' . $nombreArchivo;
                // Descargar y guardar la imagen de la marca en el directorio temporal
                file_put_contents($tempImageFilePath, @file_get_contents($producto['marca_logo'], false, $context));
                // Cambiar permisos de la imagen temporal
                chmod($tempImageFilePath, 0777);
                // Mover la imagen temporal al directorio de marcas
                $rutaDestino = public_path('images/marcas/' . $nombreArchivo);
                rename($tempImageFilePath, $rutaDestino);
                // Obtener la URL pública de la imagen guardada en la carpeta de marcas
                $urlImagenMarca = asset('images/marcas/' . $nombreArchivo);

                $marca = Marca::updateOrCreate(
                    ['nombre' => $producto['marca']],
                    ['logo' => $urlImagenMarca],
                    ['descripcion' => ''] // Ajustar según la estructura real de los datos

                );

                if($producto['img_portada'] != '')
                {
                    // Eliminar el archivo temporal
                    $tempDirectory = public_path('temp');
                    // Generar un nombre de archivo único para la imagen de portada del producto
                    $nombreArchivo = $producto['producto_id'] . '_portada_' . uniqid() . '.png';
                    // Ruta completa para la imagen temporal
                    $tempImageFilePath = $tempDirectory . '/' . $nombreArchivo;
                    // Descargar y guardar la imagen de portada del producto en el directorio temporal
                    file_put_contents($tempImageFilePath, @file_get_contents($producto['img_portada'], false, $context));
                    // Cambiar permisos de la imagen temporal
                    chmod($tempImageFilePath, 0777);
                    // Mover la imagen temporal al directorio de productos
                    $rutaDestino = public_path('images/productos/' . $nombreArchivo);
                    rename($tempImageFilePath, $rutaDestino);
                    // Obtener la URL pública de la imagen guardada en la carpeta de productos
                    $urlImagenPortadaProducto = asset('images/productos/' . $nombreArchivo);
                }
                else
                {
                    $urlImagenPortadaProducto = '';
                }
                // Crear el producto
                $productoI = Producto::updateOrCreate(
                    ['producto_id' => $producto['producto_id']], // Ajustar según la estructura real de los datos
                    [
                        'modelo' => $producto['modelo'],
                        'total_existencia' => $producto['total_existencia'],
                        'titulo' => $producto['titulo'],
                        'nombre' => $producto['titulo'],
                        'marca' => $producto['marca'],
                        'marca_logo' => $urlImagenMarca,
                        'sat_key' => $producto['sat_key'],
                        'img_portada' => $urlImagenPortadaProducto,
                        'marca_id' => $marca->id,
                        'descripcion' => $producto['descripcion'],
                        'pvol' => $producto['pvol'],
                        'peso' => $producto['peso'],
                        'existencia' => $producto['existencia']['nuevo'],
                        'alto' => $producto['alto'],
                        'largo' => $producto['largo'],
                        'ancho' => $producto['ancho'],
                        'caracteristicas' => json_encode($producto['caracteristicas']),
                        'precios' => json_encode($producto['precios']),
                        'unidad_de_medida' => json_encode($producto['unidad_de_medida']),
                        'categorias' => json_encode($producto['categorias']),

                        // Otros campos del producto
                    ]
                );
                // Crear un array asociativo para almacenar los precios

                foreach ($producto['categorias'] as $categoria) {
                    switch ($categoria['nivel']) {
                        case 1:
                            $productoI->update(['nivel1' => $categoria['id']]);
                            break;
                        case 2:
                            $productoI->update(['nivel2' => $categoria['id']]);
                            break;
                        case 3:
                            $productoI->update(['nivel3' => $categoria['id']]);
                            break;
                    }
                }

                foreach ($producto['categorias'] as $categoriaData) {
                    try{
                        $reponsecategoria = $client->get('https://developers.syscomcolombia.com/api/v1/categorias/'.$categoriaData['id']);
                        $categoriaAPI = json_decode($reponsecategoria->getBody()->getContents(), true);
                        $subcategorias = [];
                        foreach ($categoriaAPI['subcategorias'] as $subcategoria) {
                            $subcategorias[] = [
                                'id' => $subcategoria['id'],
                                'nombre' => $subcategoria['nombre'],
                                'nivel' => $subcategoria['nivel']
                            ];
                        }

                        $origenes = [];
                        foreach ($categoriaAPI['origen'] as $origen) {
                            $origenes[] = [
                                'id' => $origen['id'],
                                'nombre' => $origen['nombre'],
                                'nivel' => $origen['nivel']
                            ];
                        }

                        // Crear o actualizar la categoría
                        $categoria = Categoria::updateOrCreate(
                            ['categoria_id' => $categoriaData['id']],
                            [
                                'nombre' => $categoriaData['nombre'],
                                'nivel' => $categoriaData['nivel'],
                                'origen' => json_encode($origenes), // Guardar los orígenes como JSON
                                'subcategorías' => json_encode($subcategorias), // Guardar las subcategorías como JSON
                            ]
                        );


                    } catch (\Exception $e) {


                    }


                }

                $imagenes = [];
                // Guardar imágenes del producto en el servidor
                // Itera sobre todas las imágenes del producto
                foreach ($producto['imagenes'] as $imagenData) {
                    // Genera un nombre de archivo único para la imagen del producto
                    $nombreArchivo = $productoI->id . '_' . Str::random(10) . '.png';
                    // Ruta completa para la imagen del producto
                    $productoImagePath = public_path('images/productos/' . $nombreArchivo);

                    // Intenta descargar y guardar la imagen del producto en el directorio de productos
                    if (@file_put_contents($productoImagePath, @file_get_contents($imagenData['imagen'], false, $context)) === false) {
                        // Si falla la descarga, pasa a la siguiente imagen
                        continue;
                    }

                    // Cambia permisos de la imagen del producto
                    chmod($productoImagePath, 0777);

                    // Obtén la URL pública de la imagen guardada en el directorio de productos
                    $urlImagenProducto = asset('images/productos/' . $nombreArchivo);

                    // Actualiza el array de imágenes asociado al producto con la URL de la imagen guardada
                    $imagenes[] = [
                        'imagen' => $nombreArchivo,
                        'url' => $urlImagenProducto
                    ];
                }

                // Actualiza el producto con el nuevo array de imágenes
                $productoI->update(['imagenes' => $imagenes]);

            }
        }
    }

}
