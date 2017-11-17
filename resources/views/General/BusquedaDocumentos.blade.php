@extends('layouts.app')

@section("styles")
    <!-- Datatables-->
    <link rel="stylesheet" href="{{ asset('libs/adminLTE/plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet"
          href="{{ asset('libs/adminLTE/plugins/datatables/responsive/css/responsive.bootstrap.min.css') }}">

@endsection

@section('content')
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Busqueda de Documentos</h3>
        </div>
        <div class="box-body">

            <form id="buscarDocs" method="post" action="{{ url('buscar_documento') }}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Nombre Documento</label>
                            <input type="text" class="form-control" placeholder="Ingrese nombre" id="nombre_documento"
                                   name="nombre_documento">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-12 col-md-3">
                        <div class="form-group">
                            <label>Tipo de Documento</label>
                            <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                                <option value="">--Seleccione una opcion--</option>
                                @foreach($tipo_documentos as $tipo_documento)
                                    <option value="{{ $tipo_documento->id }}">{{ $tipo_documento->tipo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-12 col-md-3">
                        <label>Periodo AGU</label>
                        <select class="form-control" id="periodo" name="periodo">
                            <option value="">--Seleccione una opcion --</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}">{{ $periodo->nombre_periodo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <label>Descripcion</label>
                            <textarea type="text" class="form-control" placeholder="Ingrese palabras clave"
                                      id="descripcion" name="descripcion"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-center">
                        <button type="submit" id="buscar" name="buscar" class="btn btn-primary">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box box-solid box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Resultados de Busqueda</h3>
        </div>
        <div class="box-body table-responsive">
            <table id="resultadoDocs"
                   class="table table-striped table-bordered table-hover text-center">
                <thead>
                <tr>
                    <th>Nombre Documento</th>
                    <th>Tipo de Documento</th>
                    <th>Fecha Creacion</th>
                    <th>Visualizar</th>
                    <th>Descargar</th>
                </tr>
                </thead>

                <tbody id="cuerpoTabla">

                @foreach($documentos as $documento)
                    <tr>
                        <td>
                            {!! $documento->nombre_documento !!}

                        </td>
                        <td>
                            {!! $documento->tipo_documento->tipo !!}
                        </td>
                        <td>
                            {!! $documento->fecha_ingreso !!}
                        </td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-block" href="<?= $disco . $documento->path; ?>"
                               role="button"><i class="fa fa-eye"></i> Ver</a>
                        </td>
                        <td>
                            <a class="btn btn-success btn-xs btn-block"
                               href="descargar_documento/<?= $documento->id; ?>" role="button"><i
                                        class="fa fa-download"></i> Descargar</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection


@section("js")
    <!-- Datatables -->
    <script src="{{ asset('libs/adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/adminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

@endsection

<style>
    .dataTables_wrapper.form-inline.dt-bootstrap.no-footer > .row {
        margin-right: 0;
        margin-left: 0;
    }
</style>

@section("scripts")
    <script type="text/javascript">
        $(function () {
            var oTable = $('#resultadoDocs').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                responsive: true

            });
        });
    </script>

    <script>
        var currentOption = null;
        for(var i=0; i!=document.querySelector("#tipo_documento").querySelectorAll("option").length; i++)
        {
            currentOption = document.querySelector("#tipo_documento").querySelectorAll("option")[i];
            if(currentOption.getAttribute("value") == "{{ old("tipo_documento") }}")
            {
                currentOption.setAttribute("selected","selected");
            }
        }
    </script>
@endsection