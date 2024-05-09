@extends('layouts.master')
@section('title')
    Canales disponibles
@endsection
@section('css')
    <!-- dropzone css -->
    <link href="{{ URL::asset('build/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('page-title')
    Canales disponibles
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <div class="d-xl-flex">
            <div class="w-100">
                <div class="d-xl-flex">
                    <div class="card filemanager-sidebar me-md-3">
                        <div class="card-body">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4">
                                    <div class="mb-3">
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle w-100" type="button"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-plus me-1"></i> Conectar con más canales
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#"><i class="bx bx-folder me-1"></i>
                                                    Folder</a>
                                                <a class="dropdown-item" href="#"><i class="bx bx-file me-1"></i>
                                                    File</a>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled categories-list">
                                        <li>
                                            <div class="custom-accordion">
                                                <a class="text-body fw-medium py-1 d-flex align-items-center"
                                                    data-bs-toggle="collapse" href="#categories-collapse" role="button"
                                                    aria-expanded="false" aria-controls="categories-collapse">
                                                    <i class="mdi mdi-shopping-search font-size-20 text-warning me-2"></i> Mis canales
                                                    <i class="mdi mdi-chevron-up accor-down-icon ms-auto"></i>
                                                </a>
                                                <div class="collapse show" id="categories-collapse">
                                                    <div class="card border-0 shadow-none ps-2 mb-0">
                                                        <ul class="list-unstyled mb-0">
                                                            @if($miscanales != '')

                                                            @foreach ($miscanales as $micanal)

                                                            <li><a href="/ver/{{$micanal->Canal}}/{{$micanal->id}}" class="d-flex align-items-center"><span
                                                                        class="me-auto">{{ $micanal->Canal }}</span></a></li>
                                                            @endforeach
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body d-flex align-items-center">
                                                <i class="mdi mdi-google-drive font-size-20 text-muted me-2"></i> <span
                                                    class="me-auto">Google Drive</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body d-flex align-items-center">
                                                <i class="mdi mdi-dropbox font-size-20 me-2 text-primary"></i> <span
                                                    class="me-auto">Dropbox</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body d-flex align-items-center">
                                                <i class="mdi mdi-share-variant font-size-20 me-2"></i> <span
                                                    class="me-auto">Shared</span> <i
                                                    class="mdi mdi-circle-medium text-danger ms-2"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body d-flex align-items-center">
                                                <i class="mdi mdi-star-outline text-muted font-size-20 me-2"></i> <span
                                                    class="me-auto">Starred</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body d-flex align-items-center">
                                                <i class="mdi mdi-trash-can text-danger font-size-20 me-2"></i> <span
                                                    class="me-auto">Trash</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body d-flex align-items-center">
                                                <i class="mdi mdi-cog text-muted font-size-20 me-2"></i> <span
                                                    class="me-auto">Setting</span><span
                                                    class="badge bg-success rounded-pill ms-2">01</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="mt-4 pt-3 mt-auto text-center">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <div class="px-4">
                                                <img src="{{ URL::asset('build/images/Upgrade-img.png') }}" class="img-fluid" alt="">
                                            </div>
                                            <h5 class="mt-4">Upgrade Features</h5>
                                            <p class="pt-1">4 integrations, 30 team members, advanced features </p>
                                            <div class="text-center pt-2">
                                                <button type="button" class="btn btn-primary w-100">Upgrade <i
                                                        class="mdi mdi-arrow-right ms-1"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- filemanager-leftsidebar -->

                    <div class="w-100">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="font-size-16 me-3 mb-0">Canales disponibles</h5>
                                <br>
                                @foreach($canales->unique('tipo_canal')->pluck('tipo_canal') as $tipoDeCanal)
                                <h5 class="font-size-16 me-3 mb-0">{{$tipoDeCanal}}</h5>
                                <div class="row mt-4">
                                    @foreach($canales->where('tipo_canal', $tipoDeCanal) as $canal)
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="card">
                                                <div class="card-body p-3">

                                                    <div class="">

                                                        <div class="d-flex align-items-center overflow-hidden">

                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar align-self-center">
                                                                    <div
                                                                        class="avatar-title rounded bg-soft-info text-info font-size-24" style="border-radius: 51px !important;">
                                                                       {!!$canal->svg!!}
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="flex-grow-1">
                                                                <h5 class="font-size-15 mb-1 text-truncate">{{$canal->name}}</h5>
                                                                <a href=""
                                                                    class="font-size-14 text-muted text-truncate"><u>Ninguna tienda aún</u></a>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 pt-1">
                                                            <a href="{{$canal->url}}" class="btn btn-primary w-100" type="button"
                                                                >
                                                                <i class="mdi mdi-plus me-1"></i> Conectar
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    @endforeach
                                </div>
                                <!-- end row -->
                                @endforeach

                                <!-- End row -->

                                <h5 class="font-size-16 me-3 mb-0">Recent Files</h5>


                                <div class="mx-n4 px-4 mt-4" data-simplebar style="max-height: 350px;">
                                    <div class="table-responsive">

                                        <table class="table align-middle table-nowrap table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Date modified</th>
                                                    <th scope="col">Size</th>
                                                    <th scope="col" colspan="2">Members</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="javascript: void(0);" class="text-dark fw-medium"><i
                                                                class="mdi mdi-file-document font-size-20 align-middle text-primary me-2"></i>
                                                            index</a></td>
                                                    <td>12-10-2021</td>
                                                    <td>09 KB</td>
                                                    <td>
                                                        <div class="avatar-group">
                                                            <div class="avatar-group-item">
                                                                <a href="javascript: void(0);" class="d-inline-block">
                                                                    <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}"
                                                                        alt="" class="rounded-circle avatar-sm">
                                                                </a>
                                                            </div>
                                                            <div class="avatar-group-item">
                                                                <a href="javascript: void(0);" class="d-inline-block">
                                                                    <img src="{{ URL::asset('build/images/users/avatar-5.jpg') }}"
                                                                        alt="" class="rounded-circle avatar-sm">
                                                                </a>
                                                            </div>
                                                            <div class="avatar-group-item">
                                                                <a href="javascript: void(0);" class="d-inline-block">
                                                                    <div class="avatar-sm">
                                                                        <span
                                                                            class="avatar-title rounded-circle bg-success text-white font-size-16">
                                                                            A
                                                                        </span>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a class="font-size-16 text-muted" role="button"
                                                                data-bs-toggle="dropdown" aria-haspopup="true">
                                                                <i class="mdi mdi-dots-horizontal"></i>
                                                            </a>

                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="#">Open</a>
                                                                <a class="dropdown-item" href="#">Edit</a>
                                                                <a class="dropdown-item" href="#">Rename</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item" href="#">Remove</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                              
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end w-100 -->
                </div>
            </div>

            <div class="card filemanager-sidebar ms-lg-3">
                <div class="card-body">
                    <div class="d-flex flex-column h-100">

                        <div class="mb-4">
                            <h5 class="font-size-16 me-3 mb-0">Usage Storage</h5>
                            <div class="text-center mt-3">
                                <div id="chart-radialBar" class="apex-charts" data-colors='["#1f58c7"]'></div>
                                <p class="text-muted mt-3 pt-1">48.02 GB (76%) of 64 GB used</p>
                            </div>

                            <h5 class="font-size-16 mb-0 mt-5">Recent Files</h5>

                            <div class="mt-4">
                                <div class="pb-2 mb-2">
                                    <a href="javascript: void(0);" class="text-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar align-self-center me-3">
                                                <div
                                                    class="avatar-title rounded bg-soft-success text-success font-size-24">
                                                    <i class="mdi mdi-image"></i>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden me-auto">
                                                <h5 class="font-size-15 text-truncate mb-1">Images</h5>
                                                <p class="text-muted text-truncate mb-0">1,876 Files</p>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted font-size-14">8.4 GB</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="py-2 mb-2">
                                    <a href="javascript: void(0);" class="text-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar align-self-center me-3">
                                                <div class="avatar-title rounded bg-soft-danger text-danger font-size-24">
                                                    <i class="mdi mdi-play-circle-outline"></i>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden me-auto">
                                                <h5 class="font-size-15 text-truncate mb-1">Video</h5>
                                                <p class="text-muted text-truncate mb-0">45 Files</p>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted font-size-14">4.1 GB</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="py-2 mb-2">
                                    <a href="javascript: void(0);" class="text-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar align-self-center me-3">
                                                <div class="avatar-title rounded bg-soft-info text-info font-size-24">
                                                    <i class="mdi mdi-music"></i>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden me-auto">
                                                <h5 class="font-size-15 text-truncate mb-1">Music</h5>
                                                <p class="text-muted text-truncate mb-0">21 Files</p>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted font-size-14">3.2 GB</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="py-2 mb-2">
                                    <a href="javascript: void(0);" class="text-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar align-self-center me-3">
                                                <div
                                                    class="avatar-title rounded bg-soft-primary text-primary font-size-24">
                                                    <i class="mdi mdi-file-document"></i>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden me-auto">
                                                <h5 class="font-size-15 text-truncate mb-1">Document</h5>
                                                <p class="text-muted text-truncate mb-0">21 Files</p>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted font-size-14">2 GB</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="py-2 mb-2">
                                    <a href="javascript: void(0);" class="text-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar align-self-center me-3">
                                                <div
                                                    class="avatar-title rounded bg-soft-warning text-warning font-size-24">
                                                    <i class="mdi mdi-folder"></i>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden me-auto">
                                                <h5 class="font-size-15 text-truncate mb-1">Others</h5>
                                                <p class="text-muted text-truncate mb-0">20 Files</p>
                                            </div>
                                            <div class="ms-2">
                                                <p class="text-muted font-size-14">1.4 GB</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 mt-auto">
                            <form action="#" class="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple="multiple">
                                </div>
                                <div class="dz-message needsclick">
                                    <div class="mb-3">
                                        <i class="display-4 text-muted mdi mdi-cloud-upload"></i>
                                    </div>

                                    <h5>Import Files</h5>
                                </div>
                            </form>
                        </div>

                    </div>


                </div>
            </div>
        </div>
        <!-- end row -->
    @endsection
    @section('scripts')
        <!-- apexcharts -->
        <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

            <!-- dropzone plugin -->
            <script src="{{ URL::asset('build/libs/dropzone/min/dropzone.min.js') }}"></script>

            <!-- file-manager js -->
            <script src="{{ URL::asset('build/js/pages/file-manager.init.js') }}"></script>
            <!-- App js -->
            <script src="{{ URL::asset('build/js/app.js') }}"></script>
        @endsection
