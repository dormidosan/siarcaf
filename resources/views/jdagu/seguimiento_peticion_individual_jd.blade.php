

@extends('layouts.app')
@section('styles')
<link href="{{ asset('libs/file/css/fileinput.min.css') }}" rel="stylesheet">
<link href="{{ asset('libs/file/themes/explorer/theme.min.css') }}" rel="stylesheet">
@endsection
@section("content")
<div class="box box-danger">
   <div class="box-header">
      <h3 class="box-title">Seguimiento</h3>
   </div>
   <div class="box-body">
      <div class="row">
         <div class="col-lg-4 col-sm-12 col-md-4">
            <div class="form-group">
               <label>Fecha inicio</label>
               <input name="nombre" type="text" class="form-control" id="nombre" value="{{ $peticion->fecha }}"
                  readonly>
            </div>
         </div>
         <div class="col-lg-4 col-sm-12 col-md-4">
            <div class="form-group">
               <label>Fecha Actual</label>
               <input name="nombre" type="text" class="form-control" id="nombre"
                  value="{{ Carbon\Carbon::now() }}" readonly>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-lg-4 col-sm-12 col-md-4">
            <div class="form-group">
               <label>Peticionario</label>
               <input name="nombre" type="text" class="form-control" id="nombre"
                  value="{{ $peticion->peticionario }}" readonly>
            </div>
         </div>
         <div class="col-lg-4 col-sm-12 col-md-4">
            <div class="form-group">
               <label>Direccion</label>
               <input name="nombre" type="text" class="form-control" id="nombre"
                  value="{{ $peticion->direccion }}" readonly>
            </div>
         </div>
         <div class="col-lg-4 col-sm-12 col-md-4">
            <div class="form-group">
               <label>Correo</label>
               <input name="nombre" type="text" class="form-control" id="nombre"
                  value="{{ $peticion->correo }}" readonly>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-lg-12 col-sm-12 col-md-4">
            <div class="form-group">
               <label>Descripcion</label>
               <textarea class="form-control" readonly>{{ $peticion->descripcion }}</textarea>
            </div>
         </div>
      </div>
      <div class="panel panel-default">
         <div class="panel-heading">
            <h3 class="panel-title">Seguimiento paso a paso</h3>
         </div>
         <div class="panel-body">
            <div class="table-responsive">
               <table class="table table-hover text-center">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Nombre comision</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Descripcion</th>
                        <th>Documento</th>
                        <th>Opcion</th>
                     </tr>
                  </thead>
                  <tbody id="cuerpoTabla" class="text-center">
                  @php $contador = 1 @endphp
                     @forelse($peticion->seguimientos as $seguimiento)
                     <tr>
                        <td>
                          {!! $contador !!}
                          @php $contador++ @endphp
                        </td>
                        <td>{{ $seguimiento->comision->nombre }}</td>
                        <td>{{ $seguimiento->inicio }}</td>
                        <td>{{ $seguimiento->fin }}</td>
                        <td>{{ $seguimiento->descripcion }}</td>
                        @if($seguimiento->documento)
                        <td>{{ $seguimiento->documento->tipo_documento->tipo }}</td>
                        <td>
                           <a class="btn btn-info btn-xs"
                              href="<?= $disco . $seguimiento->documento->path; ?>"
                              role="button">Ver</a>
                           <a class="btn btn-success btn-xs"
                              href="descargar_documento/<?= $seguimiento->documento->id; ?>"
                              role="button">Descargar</a>
                        </td>
                        @else
                        <td>
                           N/A
                        </td>
                        <td>
                           Sin documento
                        </td>
                        @endif
                     </tr>
                     @empty
                     <p style="color: red ;">No hay criterios de busqueda</p>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section("js")
<script src="{{ asset('libs/file/js/fileinput.min.js') }}"></script>
<script src="{{ asset('libs/file/themes/explorer/theme.min.js') }}"></script>
<script src="{{ asset('libs/file/js/locales/es.js') }}"></script>
@endsection
@section("scripts")
<script type="text/javascript">
   $(function () {
       $("#documento").fileinput({
           theme: "explorer",
           uploadUrl: "/file-upload-batch/2",
           language: "es",
           minFileCount: 1,
           maxFileCount: 3,
           allowedFileExtensions: ['docx', 'pdf'],
           showUpload: false,
           fileActionSettings: {
               showRemove: true,
               showUpload: false,
               showZoom: true,
               showDrag: false
           },
           hideThumbnailContent: true,
           showPreview: false
   
       });
   });
</script>
@endsection

