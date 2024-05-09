@extends('layouts.master')
@section('title')
    Conectar con Woocommerce
@endsection
@section('css')
    <!-- choices css -->
    <link href="{{ URL::asset('../../build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('../../build/libs/alertifyjs/build/css/alertify.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- dropzone css -->
    <link href="{{ URL::asset('../../build/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('../../build/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ URL::asset('../../build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ URL::asset('../../build/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endsection
@section('page-title')
    Conectar con Woocommerce
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <div class="row">
            <div class="col-lg-12">
                <div id="addproduct-accordion" class="custom-accordion">
                    <div class="card">
                        <a  class="text-dark"
                           >
                            <div class="p-4">

                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <h5 class="text-primary font-size-17 mb-0">01</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="font-size-16 mb-1">Crear conexión</h5>
                                        <p class="text-muted text-truncate mb-0">Completa los campos a continuación</p>
                                    </div>


                                </div>

                            </div>
                        </a>

                        <div >
                            <div class="p-4 border-top">
                                <form id="form-woocommcerce" method="POST" action="{{ route('crear-woocommerce') }}">
                                @csrf

                                    <div class="mb-3">
                                        <label class="form-label" for="urlRequest">Dirección de la tienda virtual</label>
                                        <input id="urlRequest" name="urlRequest" type="url" placeholder="Ingresa la dirección de tu sitio Woocommerce"
                                            type="text" class="form-control" required>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0">

                                            <tbody>

                                                <tr>
                                                    <td>Valida la url de tu tienda</td>
                                                    <td>
                                                        <a id="alert-success"
                                                            class="btn btn-primary btn-sm waves-effect waves-light">Validar</a>
                                                    </td>
                                                    <td id="mensaje"></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label class="form-label" for="consumer_key">Consumer Key</label>
                                                <input id="consumer_key" name="consumer_key" required
                                                    placeholder="consumer key" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label class="form-label" for="consumer_secret">Consumer Secret</label>
                                                <input id="consumer_secret" name="consumer_secret" required
                                                    placeholder="Consumer Secret" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <a id="btnValidar" onclick="validarCredenciales()" class="btn btn-success"> <i
                                            class=" bx bx-file me-1"></i> Validar credenciales </a>
                                    </div>

                                        <br>
                                    <div class="row mb-4" id="guardar">
                                        <div class="col text-end">
                                            <a href="{{ url()->previous() }}" class="btn btn-danger"> <i class="bx bx-x me-1"></i> Cancel </a>
                                            <button type="submit" class="btn btn-success"> <i
                                                    class=" bx bx-file me-1"></i> Save </button>
                                        </div> <!-- end col -->
                                    </div> <!-- end row-->
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- end row -->

    @endsection
    @section('scripts')
        <!-- choices js -->
        <script src="{{ URL::asset('../../build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
        <script src="{{ URL::asset('../../build/libs/alertifyjs/build/alertify.min.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>      
        <!-- dropzone plugin -->
        <script src="{{ URL::asset('../../build/libs/dropzone/min/dropzone.min.js') }}"></script>

        <!-- init js -->
        <script src="{{ URL::asset('../../build/js/pages/ecommerce-choices.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ URL::asset('../../build/js/app.js') }}"></script>
        <script>
            // alert success
            $('#guardar').hide();
                    $('#alert-success').click(function(){

                        var url = $('#urlRequest').val();
                        $.ajax({
                            url: '{{ route("validar-url") }}',
                            type: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}',
                                url: url
                            },
                            success: function(response) {
                                if (response.success) {
                                    // La URL existe
                                    $('#mensaje').html('<div><span class="text-sm text-80 text-success"> La dirección de tu página parece ser correcta. Para obtener automáticamente tu Consumer Key y Consumer Secret, haz click en el siguiente botón: <br></span><a onclick="autorizarWooCommerce()" href="#" id="btnAutorizarWooCommerce" class="mt-4 btn btn-sm btn-outline btn-success">Autorizar WooCommerce</a></div>');

                                } else if (response.error) {
                                    // Se produjo un error al validar la URL
                                    $('#mensaje').text('Error: ' + response.error);
                                } else {
                                    // Manejar otros casos si es necesario
                                    $('#mensaje').text('Error: Algo fallo con tu url');
                                }
                            },
                            error: function(xhr, status, error) {
                                // Manejar errores de AJAX
                                alert('Error en la solicitud AJAX: ' + error);
                            }
                        });
                    });


                        // Maneja el clic en el botón para autorizar WooCommerce
                        function autorizarWooCommerce() {

                            // Realiza una solicitud AJAX para obtener el enlace de autorización
                            var url1 = $('#urlRequest').val();
                            $.ajax({
                                url: '/generar-enlace-autorizacion', // Ruta de tu controlador Laravel
                                type: 'GET',
                                data: {
                                    url1: url1
                                },
                                success: function(response) {
                                    // Redirige al usuario al enlace de autorización obtenido
                                    window.location.href = response.authorization_link;
                                },
                                error: function(xhr, status, error) {
                                    // Maneja los errores de la solicitud AJAX, si es necesario
                                    console.error(error);
                                    alert('Error al generar el enlace de autorización');
                                }
                            });
                        };
                        function validarCredenciales() {
                            var consumerKey = document.getElementById('consumer_key').value;
                            var consumerSecret = document.getElementById('consumer_secret').value;
                            var urlClient = document.getElementById('urlRequest').value;
                            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


                            // Realizar la solicitud AJAX
                            jQuery.ajax({
                                url: '/validar-credencialesWoo',
                                method: 'POST',
                                data: {
                                    consumer_key: consumerKey,
                                    consumer_secret: consumerSecret,
                                    urlClient: urlClient,
                                    _token: token
                                },
                                success: function(response) {
                                    // Si las credenciales son válidas, cambia la clase del botón a 'success'
                                    console.log(response);
                                    if (response.valid) {
                                        $('#btnValidar').text('Credenciales válidas');
                                        $('#guardar').show();
                                    } else {
                                        // Si las credenciales no son válidas, mantén la clase del botón como 'error'
                                        $('#btnValidar').text('Credenciales inválidas, probar nuevamente');
                                        $('#guardar').hide();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Manejar errores de AJAX
                                    console.error('Error al realizar la solicitud AJAX:', error);
                                    $('#guardar').hide();
                                }
                            });
                        }

        </script>
    @endsection
