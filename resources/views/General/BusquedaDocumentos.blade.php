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
            <form id="buscarDocs" method="post" action="#">
                <div class="row">
                    <div class="col-lg-4 col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Numero</label>
                            <input type="text" class="form-control" placeholder="Ingrese numero" id="numero"
                                   name="numero">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Tipo de Documento</label>
                            <select class="form-control" id="tipoDocumento" name="tipoDocumento">
                                <option value="">--Seleccione una opcion --</option>
                                <option value="acta">Acta</option>
                                <option value="dictamen">Dictamen</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12 col-md-4">
                        <label>Periodo AGU</label>
                        <select class="form-control" id="periodo" name="periodo">
                            <option value="">--Seleccione una opcion --</option>
                            <option value="">2017-</option>
                            <option value="">2015-2017</option>
                            <option value="">2013-2015</option>
                        </select>
                    </div>
                    <div class="col-lg-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <label>Descripcion</label>
                            <input type="text" class="form-control" placeholder="Ingrese una palabra clave"
                                   id="descripcion" name="descripcion">
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
        <!-- /.box-body -->
    </div>

    <div class="box box-solid box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Resultados de Busqueda</h3>
        </div>
        <div class="box-body">
            <table id="resultadoDocs"
                   class="table table-striped table-bordered table-condensed table-hover dataTable text-center">
                <thead>
                <tr>
                    <th>Nombre Documento</th>
                    <th>Fecha Creacion</th>
                    <th>Opcion</th>
                </tr>
                </thead>

                <tbody id="cuerpoTabla">
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
                <tr>
                    <td>Documento 1</td>
                    <td>01/01/2017</td>
                    <td><a href="#" class="btn btn-block btn-success btn-xs">Descargar</a></td>
                </tr>
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

            });
        });
    </script>
@endsection