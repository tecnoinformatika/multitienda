@extends('layouts.master')
@section('title')
    Conectar con facebook
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
    Conectar con Facebook
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
    <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Facebook</h4>
                        <p class="card-title-desc">Vincula tu catálogo de facebook</p>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="row g-4">
                           

                            <div class="col-xl-12">
                                <h5 class="font-size-15 mb-3">Serás redirigido a Facebook para confirmar la vinculación del catálogo.</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('auth.facebook') }}" class="btn btn-primary waves-effect btn-label waves-light"><i
                                            class="bx bx-smile label-icon"></i> Vincular</a>
                                   
                                </div>
                            </div><!-- end col -->

                        </div><!-- end row -->
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

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
