<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Recurso;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class ImportarProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:productos';

    /**z
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa productos con sus relaciones y los guarda en la base de datos';

    protected function configure()
    {
        $this->setName('importar:productos')
             ->setDescription('Importa productos de Syscom (añadida opción para filtrar por categoría)')
             ->addOption('categoria-id', null, InputArgument::OPTIONAL, 'ID de la categoría a importar');
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {

        // Retrieve the categoria_id argument
        $categoria_id = $this->argument('categoria_id');


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

                //procesamos los productos en la base de datos
                function procesarProductos($productosData,$client)
                {
                    $context = stream_context_create([
                        'http' => [
                            'timeout' => 500 // Set timeout to 30 seconds
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


                while (true) {
                    $catDataSubs = [];






                        $responseCatSubs = $client->get('https://developers.syscomcolombia.com/api/v1/categorias/'.$categoria_id);
                        $dataSubs = json_decode($responseCatSubs->getBody(), true);

                        $catDataSubs = array_merge($catDataSubs, $dataSubs);

                        foreach($catDataSubs['subcategorias'] as $catDataSub)
                        {

                                $response = $client->get('https://developers.syscomcolombia.com/api/v1/productos', [
                                    'query' => [
                                        'categoria' => $catDataSub['id'],
                                        'stock' => 0,
                                        'pagina' => $page,
                                    ],
                                ]);

                                $data = json_decode($response->getBody(), true);
                                $productosData = array_merge($productosData, $data['productos']);

                                if ($page >= $data['paginas']) {
                                    break;
                                }

                                // Dividir los productos en grupos de 100
                                $productosChunks = array_chunk($productosData, 100);

                                foreach ($productosChunks as $productoChunk) {
                                    // Procesar los productos en grupos de 100
                                    procesarProductos($productoChunk,$client);
                                }

                        }



                    $page++;
                }


                $this->info('Productos importados exitosamente.');
            } else {
                $this->error('Error al obtener el token de acceso de la API de SYSCOM Colombia.');
            }




    }



}
