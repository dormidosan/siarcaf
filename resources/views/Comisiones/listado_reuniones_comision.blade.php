@extends('layouts.app')

@section('breadcrumb')
    <section>
        <ol class="breadcrumb">
            <li><a href="{{ route("inicio") }}"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a>Comisiones</a></li>
            <li><a href="{{ route("administrar_comisiones") }}">Listado de Comisiones</a></li>
            <li><a href="javascript:document.getElementById('trabajo_comision').submit();">Trabajo de Comision</a></li>
            <li class="active">Listado de Reuniones</li>
        </ol>
    </section>
@endsection

@section("content")
    <div class="box box-danger">
        <div class="box-header">
            <h3 class="box-title">Listado de Reuniones</h3>
        </div>
        <div class="box-body">
            <!-- forms utilizados para retornar a paginas previas con breadcrumbs !-->
            <div class="hidden">
                <form id="trabajo_comision" name="trabajo_comision" method="post"
                      action="{{ url("trabajo_comision") }}">
                    {{ csrf_field() }}
                    <input class="hidden" id="comision_id" name="comision_id" value="{{$comision->id}}">
                    <button class="btn btn-success btn-xs">Acceder</button>
                </form>

            </div>
            <div class="table-responsive">
                <table class="table text-center table-bordered hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Codigo</th>
                        <th>Lugar</th>
                        <th>Convocatoria</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th colspan="3">Accion</th>
                    </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                    @php $contador =1 @endphp
                    @forelse($reuniones as $reunion)
                        <form id="iniciar_reunion_comision" name="iniciar_reunion_comision" method="post"
                              action="{{ url("iniciar_reunion_comision") }}" class="text-center">
                            <tr>
                                <td class="hidden">{{ csrf_field() }}</td>
                                <td class="hidden">
                                    <input type="hidden" id="id_reunion" name="id_reunion" value="{{ $reunion->id }}">
                                </td>
                                <td class="hidden">
                                    <input type="hidden" id="id_comision" name="id_comision" value="{{ $comision->id }}">
                                </td>
                                <td>{{ $contador }} @php $contador++ @endphp</td>
                                <td>{{ $reunion->codigo }}</td>
                                <td>{{ $reunion->lugar }}</td>
                                <td>{{ \Carbon\Carbon::parse($reunion->convocatoria)->format('d-m-Y h:m:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reunion->inicio)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reunion->fin)->format('d-m-Y') }}</td>
                                @if($reunion->vigente == 1)
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-xs btn-block" disabled><i
                                                    class="fa fa-eye"></i> Ver
                                        </button>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-success btn-xs btn-block"><i
                                                    class="fa fa-eye"></i>
                                            Iniciar
                                        </button>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-success btn-xs btn-block" disabled><i
                                                    class="fa fa-eye"></i> Continuar
                                        </button>
                                    </td>
                                @else
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-xs btn-block" disabled><i
                                                    class="fa fa-eye"></i> Ver
                                        </button>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-success btn-xs btn-block" disabled><i
                                                    class="fa fa-eye"></i> Iniciar
                                        </button>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-success btn-xs btn-block" disabled><i
                                                    class="fa fa-eye"></i> Continuar
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        </form>
                    @empty
                        <tr>
                            <td colspan="7">No hay reuniones que mostrar</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
