@extends('layouts.master')
@section('title')
    Conectar ocn Woocommerce
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
    Conectar ocn Woocommerce
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
                                        <input id="urlRequest" name="urlRequest" placeholder="Ingresa la dirección de tu sitio Woocommerce"
                                            type="text" class="form-control">
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
                                        <div class="col-lg-4">

                                            <div class="mb-3">
                                                <label class="form-label" for="manufacturername">Manufacturer Name</label>
                                                <input id="manufacturername" name="manufacturername"
                                                    placeholder="Enter Manufacturer Name" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">

                                            <div class="mb-3">
                                                <label class="form-label" for="manufacturerbrand">Manufacturer Brand</label>
                                                <input id="manufacturerbrand" name="manufacturerbrand"
                                                    placeholder="Enter Manufacturer Brand" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="price">Price</label>
                                                <input id="price" name="price" placeholder="Enter Price"
                                                    type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="choices-single-default" class="form-label">Category</label>
                                                <select class="form-control" data-trigger name="choices-single-category"
                                                    id="choices-single-category">
                                                    <option value="">Select</option>
                                                    <option value="EL">Electronic</option>
                                                    <option value="FA">Fashion</option>
                                                    <option value="FI">Fitness</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="choices-single-specifications"
                                                    class="form-label">Specifications</label>
                                                <select class="form-control" data-trigger name="choices-single-category"
                                                    id="choices-single-specifications">
                                                    <option value="HI" selected>High Quality</option>
                                                    <option value="LE" selected>Leather</option>
                                                    <option value="NO">Notifications</option>
                                                    <option value="SI">Sizes</option>
                                                    <option value="DI">Different Color</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label" for="productdesc">Product Description</label>
                                        <textarea class="form-control" id="productdesc" placeholder="Enter Description" rows="4"></textarea>
                                    </div>
                                        <br>
                                    <div class="row mb-4">
                                        <div class="col text-end">
                                            <a href="#" class="btn btn-danger"> <i class="bx bx-x me-1"></i> Cancel </a>
                                            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#success-btn"> <i
                                                    class=" bx bx-file me-1"></i> Save </a>
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
        <script>
            // alert success
                
                $('#alert-success').click(function(){
                    alert('si');
                    var url = $('#urlRequest').val();
                    $.ajax({
                        url: '{{ route("validar-url") }}',
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}',
                            url: url
                        },
                        success: function(response){                            
                            $('#mensaje').text(response.success);
                        },
                        error: function(xhr, status, error){
                            var errors = JSON.parse(xhr.responseText).errors;                           
                            $('#mensaje').text(errors[0]);
                        }
                    });
                });
     

        </script>
        <!-- dropzone plugin -->
        <script src="{{ URL::asset('../../build/libs/dropzone/min/dropzone.min.js') }}"></script>

        <!-- init js -->
        <script src="{{ URL::asset('../../build/js/pages/ecommerce-choices.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ URL::asset('../../build/js/app.js') }}"></script>
    @endsection
