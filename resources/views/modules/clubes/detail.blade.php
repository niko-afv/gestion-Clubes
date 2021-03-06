@extends('layouts.app')

@section('title', 'Lista de Clubes')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $club->name }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Principal</a>
                </li>
                <li class="breadcrumb-item">
                    @if(Auth::user()->profile->level < 3)
                        <a href="{{ route('clubes_list') }}">Clubes</a>
                    @else
                        <a href="">Mi Club</a>
                    @endif
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{ $club->name }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="m-b-md">
                            <!--<a href="javascript:void(0)" class="btn btn-white btn-xs float-right">Edit project</a>-->
                            <h2>{{ $club->name }}</h2>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right"><dt>Estado:</dt> </div>
                            <div class="col-sm-8 text-sm-left"><dd class="mb-1"><span class="label {{($club->active)?'label-primary':'label-danger'}}">{{ ($club->active)?'ACTIVO':'INACTIVO' }}</span></dd></div>
                        </dl>
                        <dl class="row mb-0">
                                <div class="col-sm-4 text-sm-right"><dt>Director:</dt> </div>
                                <div class="col-sm-8 text-sm-left"><dd class="mb-1">{{ ($club->hasDirector())?$club->director->name:'Director no asociado' }}</dd> </div>
                        </dl>
                        <dl class="row mb-0">
                                <div class="col-sm-4 text-sm-right"><dt>Unidades:</dt> </div>
                                <div class="col-sm-8 text-sm-left"> <dd class="mb-1">  {{ $club->units->count() }}</dd></div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right"><dt>Zona:</dt> </div>
                            <div class="col-sm-8 text-sm-left"> <dd class="mb-1"><a href="javascript:void(0)" class="text-navy">{{ $club->zone->name }}</a> </dd></div>
                        </dl>

                    </div>
                </div>

                <div class="row m-t-sm">
                    <div class="col-lg-12">
                        <div class="ibo">
                        <div class="ibox-content">
                            <div class="clients-list">
                                <ul class="nav nav-tabs">
                                    <li><a class="nav-link active" data-toggle="tab" href="#tab-1"><i class="fa fa-user"></i>Miembros</a></li>
                                    <li><a class="nav-link" data-toggle="tab" href="#tab-2"><i class="fa fa-briefcase"></i>Unidades</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-1" class="tab-pane active">
                                        <div class="full-height-scroll">
                                            <div class="">
                                                <p>

                                                </p>
                                                <p style="text-align: right">
                                                    @can('crud-club-members')
                                                    <a href="{{ route('add_member') }}" class="btn btn-outline btn-primary"><i class="fa fa-plus"></i>&nbsp;Nuevo Miembro</a>
                                                    &nbsp
                                                    <a href="{{ route('import_member') }}" class="btn btn-outline btn-primary"><i class="fa fa-table"></i>&nbsp;Importar</a>
                                                    @endcan
                                                    @if( Gate::allows('add-club-director') && !$club->hasDirector() )
                                                        <a href="{{ route('add_club_director', $club->id) }}" class="btn btn-outline btn-primary"><i class="fa fa-plus"></i>&nbsp;Añadir Director</a>
                                                    @endcan
                                                </p>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover dataTables-example">
                                                    <thead>
                                                    <tr>
                                                        <td>Nombre</td>
                                                        <td>Rut</td>
                                                        <td>Edad</td>
                                                        <td></td>
                                                        <td>Telefono</td>
                                                        <td></td>
                                                        <td>E-mail</td>
                                                        <td>Cargos</td>
                                                        @if(Gate::allows('crud-club-members') || Gate::allows('add-club-director'))
                                                        <td>Acciones</td>
                                                        @endif
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($club->members as $member)
                                                    <tr>
                                                        <!--
                                                        <td class="client-avatar"><img alt="image" src="{{ $member->avatar }}"> </td>
                                                        -->
                                                        <td><a  class="client-link">{{ $member->getName() }}</a></td>
                                                        <td><a  class="client-link">{{ $member->dni }}</a></td>
                                                        <td><a  class="client-link">{{ ($member->age() == 0)?'No Especificado':$member->age() }}</a></td>
                                                        <td class="contact-type"><i class="fa fa-phone"> </i></td>
                                                        <td>{{ $member->phone }}</td>
                                                        <td class="contact-type"><i class="fa fa-envelope"> </i></td>
                                                        <td>{{ $member->email }}</td>
                                                        <td>
                                                            @foreach($member->positions as $position)
                                                                <span class="tag label label-primary">{{ strtoupper($position->name) }}</span>
                                                            @endforeach
                                                        </td>
                                                        @if(Gate::allows('crud-club-members') || Gate::allows('add-club-director') )
                                                        <td>
                                                            @include('partials.action_links')
                                                        </td>
                                                        @endif
                                                        <!--
                                                        <td class="client-status"><span class="label label-primary">Active</span></td>
                                                        -->
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-2" class="tab-pane">
                                        @if(Auth::user()->profile->level >=3)
                                            <div class="">
                                                <p>

                                                </p>
                                                <p style="text-align: right">
                                                    <a href="{{ route('add_unit') }}" class="btn btn-outline btn-primary"><i class="fa fa-plus"></i>&nbsp;Nueva Unidad</a>
                                                </p>
                                            </div>
                                        @endif
                                        <div class="full-height-scroll">
                                            <div class="container">
                                                <div class="row">

                                                    @foreach($club->units as $unit)

                                                        <div class="col-lg-3">
                                                            <div class="ibox ">
                                                                <div class="ibox-title">
                                                                    <p></p>
                                                                    <p>
                                                                        @if(Auth::user()->profile->level >= 3)
                                                                            <a href="{{ route('edit_unit', $unit->id) }}" class="btn btn-outline pull-right" title="Modificar"><i class="fa fa-edit fa-2x"></i></a>
                                                                        @endif
                                                                        @if(Auth::user()->profile->level == 6)
                                                                            <a data-url="{{ route('sync_unit', $unit->id) }}" class="btn btn-outline pull-right unit_sync" title="Sincroizar"><i class="fa fa-repeat fa-2x"></i></a>
                                                                        @endif
                                                                    </p>

                                                                    <h5>{{ $unit->name }}</h5>
                                                                </div>
                                                                <div class="ibox-content">
                                                                    <h3>Fuerza:</h3>
                                                                    <h1 class="no-margins">{{ $unit->members->count() }}</h1>
                                                                    <!--
                                                                    <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                                                                    -->

                                                                    <small>Miembros</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                            {{ csrf_field() }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
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
    <script>
        $(document).ready(function(){

            $('.dataTables-example').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp'
            });

            $(document).on('click','.set-director',function (e) {
                e.preventDefault();
                var url = $(this).data('url');
                console.log(url);
                var token = $("input[name='_token']").val();

                $.post(url, { _token: token }, function (response) {
                  if (response.error == true){
                    toastr.warning(response.message, 'Cuidado');
                  }else {
                    toastr.success(response.message, 'Excelente');

                  }
                });
              });
        });

    </script>
@endsection