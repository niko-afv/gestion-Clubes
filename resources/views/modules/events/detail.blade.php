@extends('layouts.app')

@section('title', 'Lista de Clubes')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $event->name }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Principal</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('events_list') }}">Eventos</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{ $event->name }}</strong>
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
                            <!--<a href="#" class="btn btn-white btn-xs float-right">Edit project</a>-->
                            <h2>{{ $event->name }}</h2>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right"><dt>Estado:</dt> </div>
                            <div class="col-sm-8 text-sm-left"><dd class="mb-1"><span class="label {{($event->active)?'label-primary':'label-danger'}}">{{ ($event->active)?'Activo':'Inactivo' }}</span></dd></div>
                        </dl>
                        <dl class="row mb-0">

                            <div class="col-sm-4 text-sm-right"><dt>Zona(s):</dt> </div>
                            <div class="col-sm-8 text-sm-left">
                                @foreach($event->zones as $zone)
                                    <span class="tag label label-primary">{{ strtoupper($zone->name) }}</span>
                                @endforeach
                            </div>
                        </dl>

                    </div>
                    <div class="col-lg-6" id="cluster_info">

                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Fecha inicio:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{ \Carbon\Carbon::create($event->start)->format('d-M-Y') }}</dd>
                            </div>
                        </dl>
                        <dl class="row mb-0">
                            <div class="col-sm-4 text-sm-right">
                                <dt>Fecha término:</dt>
                            </div>
                            <div class="col-sm-8 text-sm-left">
                                <dd class="mb-1">{{ \Carbon\Carbon::create($event->end)->format('d-M-Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <dl class="row mb-0">
                            <div class="col-sm-2 text-sm-right"><dt>Description:</dt> </div>
                            <div class="col-sm-10 text-sm-left"><dd class="mb-1">{{ $event->description }}</dd> </div>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                    </div>
                    <div class="col-lg-5 col-lg-push-2">
                        <dl class="row mb-0">
                            &nbsp;
                            @can('see-my-club')
                                @if($event->active)
                                    <a href="{{ route('show_inscribe', ['event_id' => $event->id]) }}" class="btn btn-block btn-primary" style="color: #fff;"><i class="fa fa-hand-o-up"></i>PARTICIPAR</a>
                                @else
                                    <button type="button" disabled class="btn btn-block btn-primary" style="color: #fff;"><i class="fa fa-hand-o-up"></i>PARTICIPAR</button>
                                @endif
                            @endcan
                        </dl>
                    </div>
                </div>

                @can('crud-events')
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="ibox ">
                                <div class="ibox-title">
                                    <!--
                                    <span class="label label-success float-right"></span>
                                    -->
                                    <h5>Clubes</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins">{{ $event->clubs->count() }}</h1>
                                    <!--
                                    <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                                    -->
                                    <small><a href="{{ route('event_clubs',[$event->id]) }}">Clubes inscritos</a></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="ibox ">
                                <div class="ibox-title">
                                    <!--
                                    <span class="label label-success float-right"></span>
                                    -->
                                    <h5>Unidades</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins">{{ $event->units->count() }}</h1>
                                    <!--
                                    <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                                    -->
                                    <small>Unidades inscritas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="ibox ">
                                <div class="ibox-title">
                                    <!--
                                    <span class="label label-success float-right"></span>
                                    -->
                                    <h5>Personas</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins">{{ $event->members->count() }}</h1>
                                    <!--
                                    <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                                    -->
                                    <small>Personas inscritas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Miembros</th>
                                    <th>Club</th>
                                    <th>Zona</th>
                                    <th>Código</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($event->units as $unit)
                                    <tr class="">
                                        <td>{{ $unit->name }}</td>
                                        <td>{{ $unit->members->count() }}</td>
                                        <td>{{ $unit->club->name }}</td>
                                        <td>{{ $unit->club->zone->name }}</td>
                                        <td>{{ $unit->code }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endcan

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
      });

    </script>
@endsection