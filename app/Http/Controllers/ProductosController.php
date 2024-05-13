<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Automattic\WooCommerce\Client as WooCommerceClient;
use App\Models\Canal;
use App\Models\Categoria;
use App\Models\Atributo;
use App\Models\TerminoAtributo;
use App\Models\GaleriaProducto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Pusher\Pusher;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Recurso;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class ProductosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function destinosyscom($id)
    {
        $canal = Canal::find($id);

        return view('canal/destino-syscom')->with('canal',$canal);
    }
    public function listarproductos() {
        $productos = Producto::get();
        $categorias = Categoria::where('nivel', 1)->get();
        $marcas = Marca::get();
        $productosAleatorios = Producto::inRandomOrder()->where('total_existencia','>', 0)->take(5)->get();

        return view('ecommerce-products')->with('productos',$productos)->with('marcas', $marcas)->with('categorias',$categorias)->with('productosAleatorios',$productosAleatorios);
    }

    public function obtenerProductosHTML(Request $request)
    {
        $productos = Producto::query();

        // Aplica filtros si se seleccionó una categoría o marca
        if ($request->has('nivel') && $request->has('categoria_id')) {
            $nivel = $request->input('nivel');
            $categoriaId = $request->input('categoria_id');
            $productos->where('nivel1', $categoriaId)->orWhere('nivel2', $categoriaId)->orWhere('nivel3', $categoriaId);
        }

        if ($request->has('marca_id')) {
            $productos->where('marca_id', $request->input('marca_id'));
        }

        $productos = $productos->get();
        
        $html = '';
        foreach ($productos as $producto) {
            $precio_descuento = isset($precios->precio_descuento) ? $precios->precio_descuento : '';
            $precio_especial = isset($precios->precio_especial) ? $precios->precio_especial : '';

            $html .= '<div class="col-xl-4 col-sm-6">';
            $html .= '    <div class="product-box rounded p-3 mt-4">';
            $html .= '        <div class="product-img bg-light p-3 rounded">';
            $html .= '            <img src="' . $producto->img_portada . '" class="img-fluid mx-auto d-block" alt="' . $producto->titulo . '">';
            $html .= '        </div>';
            $html .= '        <div class="product-content pt-3">';
            $html .= '            <p class="text-muted font-size-13 mb-0">' . $producto->marca . '</p>';
            $html .= '            <h5 class="mt-1 mb-0"><a href="' . $producto->link . '" class="text-dark font-size-16">' . $producto->titulo . '</a></h5>';
            $html .= '            <!-- Aquí puedes agregar las estrellas de calificación si las tienes -->';
            $html .= '            <a href="#" class="product-buy-icon bg-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar al carrito"><i class="mdi mdi-cart-outline text-white font-size-16"></i></a>';
            
            // Construir el precio según la disponibilidad de precio descuento y precio especial
            $html .= '            <h5 class="font-size-20 text-primary mt-3 mb-0">';
            if ($precio_descuento !== '') {
                $html .= 'Precio desde $' . $precio_descuento;
                if ($precio_especial !== '') {
                    $html .= ' <del class="text-muted font-size-15 fw-medium ps-1">$' . $precio_especial . '</del>';
                }
            } else {
                $html .= 'Precio: No disponible';
            }
            $html .= '            </h5>';
            $html .= '        </div>';
            $html .= '    </div>';
            $html .= '</div>';
        }

        return response()->json(['html' => $html]);
    }
    public function obtenerultimosProductos()
    {
        $productos = Producto::where('total_existencia','>', 0)->latest()->take(10)->get();
        
        $html = '';
        foreach ($productos as $producto) {
            $precios = json_decode($producto['precios']);
            $html .= '<div class="col-xl-4 col-sm-6">';
            $html .= '    <div class="product-box rounded p-3 mt-4">';
            $html .= '        <div class="product-img bg-light p-3 rounded">';
            $html .= '            <img src="' . $producto->img_portada . '" class="img-fluid mx-auto d-block" alt="' . $producto->titulo . '">';
            $html .= '        </div>';
            $html .= '        <div class="product-content pt-3">';
            $html .= '        <img src="'. $producto->marca_logo .'" style="height: 30;">';
            $html .= '            <p class="text-muted font-size-13 mb-0">' . $producto->marca . '</p>';
            $html .= '            <h5 class="mt-1 mb-0"><a href="' . $producto->link . '" class="text-dark font-size-16">' . $producto->titulo . '</a></h5>';
            $html .= '            <!-- Aquí puedes agregar las estrellas de calificación si las tienes -->';
            $html .= '            <a href="#" class="product-buy-icon bg-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar al carrito"><i class="mdi mdi-cart-outline text-white font-size-16"></i></a>';
            $html .= '            <h5 class="font-size-20 text-primary mt-3 mb-0">Precio desde $' . $precios->precio_descuento . ' <del class="text-muted font-size-15 fw-medium ps-1">$' . $precios->precio_especial . '</del></h5>';
            $html .= '        </div>';
            $html .= '    </div>';
            $html .= '</div>';
        }

        return response()->json(['html' => $html]);
    }

    public function sincronizarsyscom1()
    {

            $clientId = env('SYSCOM_CLIENT_ID');
            $clientSecret = env('SYSCOM_CLIENT_SECRET');

            // Verificar si se han definido las credenciales
            if (!$clientId || !$clientSecret) {
                $this->error('Las credenciales de cliente de SYSCOM Colombia no están definidas en el archivo .env.');
                return;
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
                        'timeout' => 30, // Ajusta el tiempo de espera aquí (en segundos)
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
                // Obtener todas las categorías
                $productosData = [];
                $page = 1;
                $catDatas = [];
                while (true) {
                    $catDataSubs = [];
                    $responseCat = $client->get('https://developers.syscomcolombia.com/api/v1/categorias');
                    $dataC = json_decode($responseCat->getBody(), true);

                    $catDatas = array_merge($catDatas, $dataC);

                    //recorriendo todas las categorias principales para obtener las de segundo nivel

                    foreach($catDatas as $catData)
                    {

                        $responseCatSubs = $client->get('https://developers.syscomcolombia.com/api/v1/categorias/'.$catData['id']);
                        $dataSubs = json_decode($responseCatSubs->getBody(), true);

                        $catDataSubs = array_merge($catDataSubs, $dataSubs);

                        foreach($catDataSubs['subcategorias'] as $catDataSub)
                        {

                            $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos', [
                                'query' => [
                                    'categoria' => $catDataSub['id'],
                                    'pagina' => $page,
                                ],
                            ]);

                            $data = json_decode($response->getBody(), true);
                            $productosData = array_merge($productosData, $data['productos']);

                            if ($page >= $data['paginas']) {
                                break;
                            }
                                // Iterar sobre los datos de los productos
                                foreach ($productosData as $productoData) {
                                    $tempDirectory = public_path('temp');

                                    $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos/'.$productoData['producto_id']);
                                    $producto = json_decode($response->getBody()->getContents(), true);






                                    // Generar un nombre de archivo único para la imagen de la marca
                                    $nombreArchivo = str_replace(' ', '_', $producto['marca']) . '.jpg';
                                    // Ruta completa para la imagen temporal
                                    $tempImageFilePath = $tempDirectory . '/' . $nombreArchivo;
                                    // Descargar y guardar la imagen de la marca en el directorio temporal
                                    file_put_contents($tempImageFilePath, file_get_contents($producto['marca_logo'], false, $context));
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
                                    // Eliminar el archivo temporal




                                    $tempDirectory = public_path('temp');
                                    // Generar un nombre de archivo único para la imagen de portada del producto
                                    $nombreArchivo = $producto['producto_id'] . '_portada_' . uniqid() . '.png';
                                    // Ruta completa para la imagen temporal
                                    $tempImageFilePath = $tempDirectory . '/' . $nombreArchivo;
                                    // Descargar y guardar la imagen de portada del producto en el directorio temporal
                                    file_put_contents($tempImageFilePath, file_get_contents($producto['img_portada'], false, $context));
                                    // Cambiar permisos de la imagen temporal
                                    chmod($tempImageFilePath, 0777);
                                    // Mover la imagen temporal al directorio de productos
                                    $rutaDestino = public_path('images/productos/' . $nombreArchivo);
                                    rename($tempImageFilePath, $rutaDestino);
                                    // Obtener la URL pública de la imagen guardada en la carpeta de productos
                                    $urlImagenPortadaProducto = asset('images/productos/' . $nombreArchivo);

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
                                            'categorias' => json_encode($producto['categorias'])
                                            // Otros campos del producto
                                        ]
                                    );
                                    // Crear un array asociativo para almacenar los precios



                                    foreach ($producto['categorias'] as $categoriaData) {
                                        try{
                                            $reponsecategoria = $client->get('https://developers.syscomcolombia.com/api/v1/categorias/'.$categoriaData['id']);
                                            $categoriaAPI = json_decode($reponsecategoria->getBody()->getContents(), true);

                                            // Crear o actualizar la categoría
                                            $categoria = Categoria::updateOrCreate(
                                                ['categoria_id' => $categoriaData['id']],
                                                [
                                                    'nombre' => $categoriaData['nombre'],
                                                    'nivel' => $categoriaData['nivel'],
                                                    'origen' => json_encode($categoriaAPI['origen']), // Guardar el origen como JSON
                                                    'subcategorías' => json_encode($categoriaAPI['subcategorias']), // Guardar las subcategorías como JSON
                                                ]
                                            );

                                        } catch (\Exception $e) {


                                        }


                                    }


                                    // Guardar imágenes del producto en el servidor
                                    foreach ($producto['imagenes'] as $imagenData) {


                                        // Generar un nombre de archivo único para la imagen del producto
                                        $nombreArchivo = $productoI->id . '_' . Str::random(10) . '.png';
                                        // Ruta completa para la imagen del producto
                                        $productoImagePath = public_path('images/productos/' . $nombreArchivo);
                                        // Descargar y guardar la imagen del producto en el directorio de productos
                                        file_put_contents($productoImagePath, file_get_contents($imagenData['imagen'], false, $context));
                                        // Cambiar permisos de la imagen del producto
                                        chmod($productoImagePath, 0777);
                                        // Actualizar el array de imágenes asociado al producto con la URL de la imagen guardada

                                        $imagenProducto = [
                                            'imagen' => $nombreArchivo,
                                            'url' => asset('images/productos/' . $nombreArchivo)
                                        ];
                                        $producto['imagenes'][] = $imagenProducto;
                                    }
                                    $productoI->update(['imagenes' => $producto['imagenes']]); // Cierre del foreach y actualización de la imagen

                                    // Guardar recursos del producto





                                }
                        }
                    }


                    $page++;
                }




                return ('Productos importados exitosamente.');
            } else {
                return  ('Error al obtener el token de acceso de la API de SYSCOM Colombia.');
            }
    }

    public function sincronizar($id)
    {
        $usuario = Auth::user();
        $canal = $usuario->canales()->findOrFail($id);

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

        $per_page = 10;
        $page = 1;
        $categorias = [];

        //categorias
        do {
            // Realizar la solicitud a la API de WooCommerce para obtener las categorías de la página actual
            $response = $woocommerce->get('products/categories', ['per_page' => $per_page, 'page' => $page]);

            // Verificar si hay categorías en la respuesta
            if (!empty($response)) {
                // Agregar las categorías de la página actual al arreglo de categorías
                $categorias = array_merge($categorias, $response);

                // Incrementar el número de página para obtener la siguiente página en la siguiente iteración
                $page++;
            } else {
                // No hay más categorías, sal del bucle
                break;
            }
        } while (true);

        // Ahora $categorias contiene todas las categorías


        $this->importarCategorias($categorias);

        // Obtener productos desde WooCommerce

        $pagep = 1;
        $productos = [];
        $per_pagep = 1;
        $response = $woocommerce->get('products');
        $lastRequest = $woocommerce->http->getResponse();
        $headers = $lastRequest->getHeaders();
        // Obtener el valor de X-WP-Total
        $xWpTotal = $headers['X-WP-Total'];

        $uploadedProducts = 0;


        do {
            // Realizar la solicitud a la API de WooCommerce para obtener las categorías de la página actual
            $response = $woocommerce->get('products', ['per_page' => $per_pagep, 'page' => $pagep]);
            // Enviar un mensaje inicial para establecer el total de productos
            // Verificar si existen encabezados de enlace en la respuesta

            // Verificar si hay categorías en la respuesta
            if (!empty($response)) {
                // Agregar las categorías de la página actual al arreglo de categorías
                $productos = array_merge($productos, $response);
                    foreach ($response as $index => $productoWooCommerce) {
                        //dd($canal->id);
                        $producto = Producto::firstOrCreate(
                            ['slug' => $productoWooCommerce->slug],
                            [
                            'nombre' => $productoWooCommerce->name,
                            'slug' =>$productoWooCommerce->slug,
                            'permalink' =>$productoWooCommerce->permalink,
                            'status' =>$productoWooCommerce->status,
                            'type' =>$productoWooCommerce->type,
                            'type' =>$productoWooCommerce->type,
                            'descripcion' =>$productoWooCommerce->description,
                            'resumen' =>$productoWooCommerce->short_description,
                            'sku' =>$productoWooCommerce->sku,
                            'precio' =>$productoWooCommerce->price,
                            'stock_status' =>$productoWooCommerce->stock_status,
                            'peso' =>$productoWooCommerce->weight,
                            'stock' => $productoWooCommerce->stock_quantity,
                            'categoria_id' => !empty($productoWooCommerce->categories) ? Categoria::where('slug', $productoWooCommerce->categories[0]->slug)->first()->id : null,
                            'canal' => "Woocommerce",
                            'canal_id' => $canal->id
                            ]
                        );
                        // Importar atributos y términos
                        foreach ($productoWooCommerce->attributes as $atributo) {
                            $nuevoAtributo = Atributo::firstOrCreate(
                                ['slug' => $atributo->slug],
                                [
                                'nombre' => $atributo->name,
                                'slug' => $atributo->slug
                            ]);

                            foreach ($atributo->options as $opcion) {
                                $nuevoTermino = TerminoAtributo::updateOrCreate([
                                    'atributo_id' => $nuevoAtributo->id,
                                    'nombre' => $opcion
                                ]);
                                // Aquí debes obtener los IDs de atributo y término creados en las iteraciones anteriores
                                $atributoId = $nuevoAtributo->id;
                                $terminoAtributoId = $nuevoTermino->id;
                                $nuevoAtributo->terminos()->attach($nuevoTermino->id);
                            }

                            $producto->marca = $nuevoTermino->nombre;
                            $producto->save();
                            $producto->atributos()->attach($atributoId, ['termino_atributo_id' => $terminoAtributoId]);
                        }
                        // Importar imágenes
                        if (!empty($productoWooCommerce->images)) {

                            foreach ($productoWooCommerce->images as $imagen) {
                                // Descargar la imagen y guardarla localmente
                                $rutaLocalImagen = 'imagenes/' . basename($imagen->src);

                                // Asegúrate de que la carpeta imagenes exista, si no existe, créala
                                if (!file_exists(storage_path('app/imagenes'))) {
                                    mkdir(storage_path('app/imagenes'), 0777, true);
                                }
                                file_put_contents(storage_path('app/' . $rutaLocalImagen), file_get_contents($imagen->src));

                                // Guardar la ruta de la imagen en tu base de datos
                                $producto->galeria()->firstOrCreate(
                                    ['imagen' => $rutaLocalImagen],
                                    [
                                    'imagen' => $rutaLocalImagen,
                                    // Otros campos necesarios para la tabla de imágenes de la galería
                                ]);
                            }
                        }

                        $uploadedProducts++;

                        $percentage = round(($uploadedProducts / $xWpTotal) * 100);

                        $pusher->trigger('multichannel', 'progress', ['percentage' => $percentage]);
                    }

                // Incrementar el número de página para obtener la siguiente página en la siguiente iteración
                $pagep++;
            } else {
                // No hay más categorías, sal del bucle
                break;
            }
        } while (true);

        return response()->json(['message' => 'All products uploaded successfully']);
    }

    function importarCategorias($categorias) {
        foreach($categorias as $categoria)
        {
            $nuevaCategoria = Categoria::updateOrCreate([
                'nombre' => $categoria->name,
                'slug' => $categoria->slug, // Genera el slug
                // Otros campos necesarios
            ]);



            // Si el ID del padre es diferente de 0, buscar la categoría padre correspondiente en Laravel
            if ($categoria->parent !== 0) {
                $categoriaPadre = Categoria::where('slug', $categoria->slug)->first();
                if ($categoriaPadre) {
                    $nuevaCategoria->categoria_padre_id = $categoriaPadre->id;
                    $nuevaCategoria->save();
                }
            }



            // Si la categoría actual tiene subcategorías, llamar recursivamente a la función para importarlas
            if (!empty($categoria->children)) {
                importarCategorias($categoria->children);
            }
        }
        // foreach ($categorias as $categoria) {
        //     // Crear o actualizar la categoría actual

        // }
    }

}
