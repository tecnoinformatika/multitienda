@extends('layouts.master')
@section('title')
    Products
@endsection
@section('css')
    <!-- swiper css -->
    <link rel="stylesheet" href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}">

    <!-- nouisliderribute css -->
    <link rel="stylesheet" href="{{ URL::asset('build/libs/nouislider/nouislider.min.css') }}">
@endsection
@section('page-title')
    Products
@endsection
@section('body')

    <body>
    @endsection
    <style>
        /* Tooltip container */
        .tooltip {
          position: relative;
          display: inline-block;
          border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
        }

        /* Tooltip text */
        .tooltip .tooltiptext {
          visibility: hidden;
          width: 120px;
          background-color: black;
          color: #fff;
          text-align: center;
          padding: 5px 0;
          border-radius: 6px;

          /* Position the tooltip text - see examples below! */
          position: absolute;
          z-index: 1;
        }

        /* Show the tooltip text when you mouse over the tooltip container */
        .tooltip:hover .tooltiptext {
          visibility: visible;
        }
        .tooltip .tooltiptext {
            top: -5px;
            left: 105%;
        }
        </style>
    @section('content')
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Filters</h5>
                    </div>

                    <div>
                        <div class="custom-accordion p-4">
                            <h5 class="font-size-14 mb-0"><a href="#categories-collapse" class="text-dark d-block"
                                    data-bs-toggle="collapse">Categorias <i
                                        class="mdi mdi-chevron-up float-end accor-down-icon"></i></a></h5>

                            <div class="collapse show mt-4" id="categories-collapse">
                                @foreach($categorias as $categoria)
                                @php
                                    $cadena =  $categoria->nombre;
                                    // Quitar acentos
                                    $cadenaSinAcentos = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
                                    // Reemplazar espacios con guiones bajos
                                    $cadenaSinEspacios = str_replace("'", '', $cadenaSinAcentos);
                                    $cadenaSinEspacios = str_replace(",", '', $cadenaSinEspacios);
                                    $cadenaSinEspacios1 = str_replace(' ', '', $cadenaSinEspacios);
                                    // Convertir a minúsculas
                                    $cadenaFinal = strtolower($cadenaSinEspacios1);
                                @endphp
                                <div class="categories-group-card">
                                    <a href="#{{ $cadenaFinal }}" class="text-body fw-semibold pb-3 d-block collapsed"
                                        data-bs-toggle="collapse" aria-expanded="false" aria-controls="{{ $cadenaFinal }}">
                                        <i class="
                                        @if($cadenaFinal == 'videovigilancia')
                                        bx bx-camera
                                        @elseif ($cadenaFinal == 'radiocomunicacion')
                                        bx bx-radio
                                        @elseif ($cadenaFinal == 'automatizacioneintrusion')
                                        bx bx-usb
                                        @elseif ($cadenaFinal == 'controldeacceso')
                                        bx bx-lock
                                        @elseif ($cadenaFinal == 'redesyaudio-video')
                                        bx bx-wifi
                                        @endif
                                        font-size-16 align-middle me-2"></i>
                                        {{ $categoria->nombre }}
                                        <i class="mdi mdi-chevron-up float-end accor-down-icon"></i>
                                    </a>
                                    @php
                                        $subcategoriasArray = json_decode($categoria->subcategorías, true);

                                    @endphp
                                    <div id="{{ $cadenaFinal }}" class="collapse" data-parent="#accordion">
                                        <div class="card p-2 border shadow-none">
                                            <ul class="list-unstyled categories-list mb-0">
                                                @foreach($subcategoriasArray as $subcategoria)
                                               
                                                @php
                                                    $cadenaS =  $subcategoria['nombre'];
                                                    // Quitar acentos
                                                    $cadenaSinAcentosS = iconv('UTF-8', 'ASCII//TRANSLIT', $cadenaS);
                                                    // Reemplazar espacios con guiones bajos
                                                    $cadenaSinEspaciosS = str_replace("'", '', $cadenaSinAcentosS);
                                                    $cadenaSinEspaciosS = str_replace(",", '', $cadenaSinEspaciosS);
                                                    $cadenaSinEspaciosS = str_replace("/", '', $cadenaSinEspaciosS);
                                                    $cadenaSinEspaciosS = str_replace("(", '', $cadenaSinEspaciosS);
                                                    $cadenaSinEspaciosS = str_replace(")", '', $cadenaSinEspaciosS);
                                                    $cadenaSinEspacios1S = str_replace(' ', '', $cadenaSinEspaciosS);
                                                    // Convertir a minúsculas
                                                    $cadenaFinalS = strtolower($cadenaSinEspacios1S);
                                                @endphp

                                                <li>
                                                    <a href="#{{ $cadenaFinalS }}" class="text-body fw-semibold pb-3 d-block collapsed"
                                                        data-bs-toggle="collapse" aria-expanded="false" aria-controls="{{ $cadenaFinalS }}">
                                                            {{ $subcategoria['nombre']}}
                                                        <i class="mdi mdi-chevron-up float-end accor-down-icon"></i>
                                                    </a>

                                                    <!--
                                                        aca comienza a mostrar las subcategorias nivel 3
                                                    -->
                                                    @php


                                                        $cat = [];
                                                        $terceras = App\Models\Categoria::where('categoria_id',$subcategoria['id'])->first();
                                                        if($terceras != ''){
                                                            $tercera = json_decode($terceras['subcategorías']);
                                                           }
                                                        else{
                                                            $tercera = [];
                                                        }
                                                        


                                                    @endphp


                                                    <div id="{{ $cadenaFinalS }}" class="collapse" data-parent="#accordion">
                                                        <div class="card p-2 border shadow-none">
                                                            <ul class="list-unstyled categories-list mb-0">
                                                            
                                                                
                                                            @foreach($tercera as $subcategoria3)

                                                                <li>
                                                                    <a class="text-body fw-semibold pb-3">
                                                                        {{ $subcategoria3->nombre}}
                                                                    
                                                                </a>
                                                                </li>
                                                            @endforeach
                                                            
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <!--
                                                        aca finaliza de mostrar las subcategorias nivel 3
                                                    -->
                                                </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    </div>

                                </div>
                                @endforeach

                            </div>

                        </div>

                        <div class="p-4 border-top">
                            <div>
                                <h5 class="font-size-14 mb-3">Precio</h5>
                                <div id="priceslider" class="mb-4"></div>
                            </div>
                        </div>

                        <div class="custom-accordion">


                            <div class="p-4 border-top">
                                <div>
                                    <h5 class="font-size-14 mb-0"><a href="#filterprodductcolor-collapse"
                                            class="text-dark d-block" data-bs-toggle="collapse">Marcas <i
                                                class="mdi mdi-chevron-up float-end accor-down-icon"></i></a>
                                    </h5>

                                    <div class="collapse show" id="filterprodductcolor-collapse">
                                        <div class="mt-4">
                                            @foreach($marcas as $marca)
                                                <div class="form-check mt-2">
                                                    <input type="checkbox" class="form-check-input marca-checkbox" id="marca-{{ $marca->id }}" data-id="{{ $marca->id }}">
                                                    <label class="form-check-label" for="marca-{{ $marca->id }}"><img src="{{ $marca->logo }}" height="30px" /></label>

                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="swiper-container slider rounded">
                            <div class="swiper-wrapper" dir="ltr">
                                @foreach($productosAleatorios as $producto)
                                    <div class="swiper-slide rounded overflow-hidden ecommerce-slied-bg">
                                        <div class="row justify-content-center">
                                            <div class="col-xl-11 col-lg-11">
                                                <div class="row align-items-center">

                                                    <div class="col-md-6">
                                                        <div class="p-4 p-xl-0">
                                                            <img src="{{ $producto->img_portada }}"
                                                                class="img-fluid" alt="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="p-4 p-xl-0">
                                                            <h3 class="mb-2 text-truncate"><a
                                                                    href="ecommerce-product-detail">{{ $producto->titulo }}</a></h3>
                                                            <h5 class="fw-normal font-size-16 mt-1 text-truncate">
                                                                SKU: {{ $producto->modelo }}</h5>

                                                            <ul class="list-unstyled px-0 mb-0 mt-4">
                                                                <li>
                                                                    <p class="text-muted mb-1 text-truncate"><i
                                                                            class="mdi mdi-circle-medium align-middle me-1"></i>
                                                                        Marca : <img src="{{ $producto->marca_logo }}" height="50px" >
                                                                    </p>
                                                                </li>
                                                                <li>
                                                                    <p class="text-muted mb-1 text-truncate"><i
                                                                            class="mdi mdi-circle-medium align-middle me-1"></i>
                                                                        Disponible: {{ $producto->existencia }}</p>
                                                                </li>
                                                            </ul>
                                                            @php
                                                                $precios = json_decode($producto->precios);


                                                            @endphp
                                                            <h2 class="mb-0 mt-4 text-truncate"><span
                                                                    class="font-size-20">Precio desde</span>
                                                                    @if(isset($precios->precio_especial))
                                                                    <b>${{ $precios->precio_especial }}</b>
                                                                    @endif 
                                                                    @if(isset($precios->precio_descuento))
                                                                        <span class="text-muted me-2">
                                                                            <del class="font-size-20 fw-normal">${{ $precios->precio_descuento }}</del>
                                                                        </span>
                                                                    @endif
                                                            </h2>
                                                            <div class="mt-4">
                                                                <button type="button"
                                                                    class="btn btn-success w-lg waves-effect waves-light">Buy
                                                                    Now</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-none d-lg-block">
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <h5>Mostrando resultados "Chairs"</h5>
                                        <ol class="breadcrumb p-0 bg-transparent mb-2">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Furniture</a></li>
                                            <li class="breadcrumb-item active">Chairs</li>
                                        </ol>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-inline float-md-end">
                                        <div class="search-box ms-2">
                                            <div class="position-relative">
                                                <input type="text" class="form-control bg-light border-light rounded"
                                                    placeholder="Search...">
                                                <i class="bx bx-search search-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav nav-tabs nav-tabs-custom mt-3 mb-2 ecommerce-sortby-list">
                                <li class="nav-item">
                                    <a class="nav-link disabled fw-medium" href="#" tabindex="-1"
                                        aria-disabled="true">Sort by:</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#popularity" role="tab"
                                        href="#">Selección</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#newest" role="tab"
                                        href="#">Ultimos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#discount" role="tab"
                                        href="#">Descuentos</a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                <div class="tab-pane active" id="popularity" role="tabpanel">
                                    <div class="row" id="div-productos">


                                        


                                    </div>
                                    <!-- end row -->
                                </div>

                                <div class="tab-pane" id="newest" role="tabpanel">
                                    <div class="row">
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge">New</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-7.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Chair</p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Tuition Classes
                                                            Chair Crime</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$410
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$340</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge">New</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-8.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Gray, Chair</p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Dining Table And
                                                            Chair</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star-half text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$260
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$280</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge">New</span>
                                                </div>
                                                <div class="pricing-badge">
                                                    <span class="badge">New</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-9.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Gray, Chair</p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Home & Office
                                                            Chair Crime</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star-half text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$260
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$280</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge">New</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-6.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Gray, Chair</p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Home & Office
                                                            Chair Crime</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star-half text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$260
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$280</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- end row -->
                                </div>
                                <div class="tab-pane" id="discount" role="tabpanel">
                                    <div class="row">
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge bg-danger"> 20 %</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-9.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Gray, Chair</p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Home & Office
                                                            Chair Crime</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star-half text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$260
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$280</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge bg-danger"> 20 %</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-6.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Black, Chair
                                                    </p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Sofa Home Chair
                                                            Black</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$180
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$200</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="product-box rounded p-3 mt-4">
                                                <div class="pricing-badge">
                                                    <span class="badge bg-danger"> 20 %</span>
                                                </div>
                                                <div class="product-img bg-light p-3 rounded">
                                                    <img src="{{ URL::asset('build/images/product/img-5.png') }}"
                                                        alt="" class="img-fluid mx-auto d-block">
                                                </div>
                                                <div class="product-content pt-3">
                                                    <p class="text-muted font-size-13 mb-0">Chair</p>
                                                    <h5 class="mt-1 mb-0"><a href="#"
                                                            class="text-dark font-size-16">Tuition Classes
                                                            Chair Crime</a></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                        <i class="bx bxs-star text-warning font-size-12"></i>
                                                    </p>
                                                    <a href="" class="product-buy-icon bg-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                        data-bs-original-title="Add To Cart">
                                                        <i class="mdi mdi-cart-outline text-white font-size-16"></i>
                                                    </a>
                                                    <h5 class="font-size-20 text-primary mt-3 mb-0">$410
                                                        <del class="text-muted font-size-15 fw-medium ps-1">$340</del>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-6">
                                    <div>
                                        <p class="mb-sm-0">Page 2 of 84</p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="float-sm-end">
                                        <ul class="pagination pagination-rounded mb-sm-0">
                                            <li class="page-item disabled">
                                                <a href="#" class="page-link"><i
                                                        class="mdi mdi-chevron-left"></i></a>
                                            </li>
                                            <li class="page-item active">
                                                <a href="#" class="page-link">1</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">2</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">3</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">4</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">5</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link"><i
                                                        class="mdi mdi-chevron-right"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <!-- swiper js -->
        <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

        <!-- nouisliderribute js -->
        <script src="{{ URL::asset('build/libs/nouislider/nouislider.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/wnumb/wNumb.min.js') }}"></script>

        <!-- init js -->
        <script src="{{ URL::asset('build/js/pages/product-filter-range.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script>
           function obtenerProductos(categoriaId = null, marcaId = null) {
                $.ajax({
                    url: '/obtener-productos-html',
                    type: 'GET',
                    data: {
                        categoria_id: categoriaId,
                        nivel: nivel,
                        marca_id: marcaId
                    },
                    success: function(response) {
                        $('#div-productos').html(response.html);
                    }
                });
            }
            function obtenerUltimosProductos()
            {
                $.ajax({
                    url: '/obtener-ultimos-productos',
                    type: 'GET',
                    success: function(response) {
                        $('#div-productos').html(response.html);
                    }
                });
            }
            $(document).ready(function() {
                obtenerUltimosProductos();
                obtenerProductos();

                // Actualizar los productos cuando se seleccione una categoría
                $('#categoria').change(function() {
                    var categoriaId = $(this).val();
                    obtenerProductos(categoriaId, null);
                });

                // Actualizar los productos cuando se seleccione una marca
                $('.marca-checkbox').change(function() {
                    var marcaId = $(this).data('id');
                    obtenerProductos(null, marcaId);
                });
            });
        </script>
    @endsection
