<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportesRequest;
use App\Http\Requests\ReportesPermisosTemporalesRequest;
use App\Http\Requests\ReportesPermisosPermanentesRequest;
use App\Http\Requests\BuscarBitacoraCorrespRequest;
use App\Http\Requests\ReportesAsistenciasRequest;
use App\Http\Requests\ReportesConsolidadosRentaRequest;
use App\Http\Requests\ReporteAsambleistaPeriodoRequest;
use Illuminate\Support\Facades\DB;
use Response;
use App\Asambleista;
use Carbon\Carbon;
use App\Peticion;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use App\Clases\Mensaje;
<<<<<<< HEAD
use App\Periodo;
=======
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2

class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

<<<<<<< HEAD
 public function buscar_periodo()
    {
      
       $periodo = null;
       $resultados=NULL;
       $periodos = Periodo::where('id', '!=', '0')->pluck('nombre_periodo', 'id'); 

        return view('Reportes.Reporte_Asambleistas_Periodo')        
        ->with('periodo', $periodo)
        ->with('periodos', $periodos)
        ->with('resultados',$resultados);


    }

public function buscar_cumple()
    {
      
       $periodo = null;
       $resultados=NULL;
       $periodos = Periodo::where('id', '!=', '0')->pluck('nombre_periodo', 'id'); 

        return view('Reportes.Reporte_Asambleistas_Cumple')        
        ->with('periodo', $periodo)
        ->with('periodos', $periodos)
        ->with('resultados',$resultados);


    }

public function buscar_asambleistas_cumple(ReporteAsambleistaPeriodoRequest $request){



  $periodo = $request->get("periodo");
  $mes=$request->get("mes");

  $periodos = Periodo::where('id', '!=', '0')->pluck('nombre_periodo', 'id'); 
//dd($periodo);

$resultados=DB::table('asambleistas')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('periodos','asambleistas.periodo_id','=','periodos.id')
        ->where('periodos.id','=',$periodo)
        ->whereMonth('personas.nacimiento','=',$mes)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asambleistas.propietario','personas.dui','personas.nit','users.email','periodos.nombre_periodo')
        ->limit(1)
        ->get();
        ;

if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}
    
         return view("Reportes.Reporte_Asambleistas_Cumple")         
         ->with('resultados',$resultados)
         ->with('periodo',$periodo)
         ->with('periodos',$periodos)
         ->with('mesnom',$this->numero_mes($mes))
         ->with('mes',$mes)
         ;

    }

public function buscar_asambleistas_periodo(ReporteAsambleistaPeriodoRequest $request){



  $periodo = $request->get("periodo");
  $periodos = Periodo::where('id', '!=', '0')->pluck('nombre_periodo', 'id'); 
//dd($periodo);

$resultados =DB::table('asambleistas')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->join('periodos','asambleistas.periodo_id','=','periodos.id')
        ->where('periodos.id','=',$periodo)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','facultades.nombre as NomFac','asambleistas.propietario','personas.dui','personas.nit','users.email','periodos.nombre_periodo')
        ->limit(1)
        ->get();
        ;

if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}
    
         return view("Reportes.Reporte_Asambleistas_Periodo")         
         ->with('resultados',$resultados)
         ->with('periodo',$periodo)
         ->with('periodos',$periodos)
         ;

    }

 public function Reporte_Asambleistas_Cumple($tipo){

       $parametros = explode('.', $tipo);
       $tipodes=$parametros[0];
       $periodo=$parametros[1];
       $mes=$parametros[2];

        $nombre_periodos=DB::table('periodos')
        ->where('periodos.id','=',$periodo)
        ->first();

$resultados =DB::table('asambleistas')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->join('periodos','asambleistas.periodo_id','=','periodos.id')
        ->where('periodos.id','=',$periodo)
        ->whereMonth('personas.nacimiento','=',$mes)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','facultades.nombre as NomFac','asambleistas.propietario','personas.dui','personas.nit','users.email','periodos.nombre_periodo')
        ->orderBy('facultades.nombre', 'desc')
        ->orderBy('personas.nacimiento','asc')
        ->get();
        
        //dd($resultados);

         $mesnom=$this->numero_mes($mes);
    
         $view =  \View::make('Reportes/Reporte_Asambleistas_Cumple_pdf', compact('resultados','nombre_periodos','mesnom'))->render();
         $pdf = \App::make('dompdf.wrapper');   
         $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

           if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

    }

    public function Reporte_Asambleista_Periodo($tipo){

       $parametros = explode('.', $tipo);
       $tipodes=$parametros[0];
       $periodo=$parametros[1];

        $nombre_periodos=DB::table('periodos')
        ->where('periodos.id','=',$periodo)
        ->first();

$resultados =DB::table('asambleistas')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->join('periodos','asambleistas.periodo_id','=','periodos.id')
        ->where('periodos.id','=',$periodo)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','facultades.nombre as NomFac','asambleistas.propietario','personas.dui','personas.nit','users.email','periodos.nombre_periodo')
        ->orderBy('facultades.nombre', 'desc')
        ->get();
        
        //dd($resultados);

         $view =  \View::make('Reportes/Reporte_Asambleistas_Periodo_pdf', compact('resultados','nombre_periodos'))->render();
         $pdf = \App::make('dompdf.wrapper');   
         $pdf->loadHTML($view)->setPaper('letter','landscape')->setWarnings(false);

           if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

    }


    public function Reporte_permisos_temporales($tipo) 
    {
=======
    public function Reporte_permisos_temporales($tipo) 
    {
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2


        $parametros = explode('.', $tipo);
        $tipodes=$parametros[0];
        $idagenda=$parametros[1];
        $idperiodo=$parametros[2];
        $fecha=$parametros[3];

        $nombreperiodo1=DB::table('periodos')
        ->where('periodos.id','=',$idperiodo)
        ->select('periodos.nombre_periodo')
        ->get();

        $nombreperiodo=$nombreperiodo1[0]->nombre_periodo;
        //dd($nombreperiodo);

        

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',$idagenda)//1 por ser permisos temporales 
        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','asistencias.salida','asistencias.propietaria')
        ->get();
        


       
        $view =  \View::make('Reportes/Reporte_permisos_temporales_pdf', compact('resultados','fecha'))->render();
<<<<<<< HEAD

        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true); //estas ondas no furulan :v
        $pdf->getDomPDF()->set_option('margin-top',0);
        $pdf->getDomPDF()->set_option('margin-bottom',0);
        $pdf->getDomPDF()->set_option('margin-left',0);
        $pdf->getDomPDF()->set_option('margin-right',0);
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

=======

        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true); //estas ondas no furulan :v
        $pdf->getDomPDF()->set_option('margin-top',0);
        $pdf->getDomPDF()->set_option('margin-bottom',0);
        $pdf->getDomPDF()->set_option('margin-left',0);
        $pdf->getDomPDF()->set_option('margin-right',0);
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2
        if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }



    }

    public function Reporte_permisos_permanentes($tipo) 
    {

      //dd($tipo);
      $parametros = explode('.', $tipo);
        $tipodes=$parametros[0];
        $fechainicial=$parametros[1];
        $fechafinal=$parametros[2];


$resultados = DB::table('permisos')
->join('asambleistas','permisos.asambleista_id','=','asambleistas.id')
->join('users','asambleistas.user_id','=','users.id')   
->join('personas','users.persona_id','=','personas.id')
->where
([
  ['permisos.fecha_permiso','>=',$fechainicial],
  ['permisos.fecha_permiso','<=',$fechafinal]
])
->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
'personas.segundo_nombre','personas.dui','personas.nit','personas.afp','personas.cuenta','permisos.motivo',
'permisos.fecha_permiso','permisos.inicio','permisos.fin')
->get();

//dd($resultados);

        $view =  \View::make('Reportes/Reporte_permisos_permanentes_pdf', compact('resultados','fechainicial','fechafinal'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }

    

        public function Reporte_asistencias_sesion_plenaria($tipo) 
    {
      
       
        $parametros = explode('.', $tipo);
        $tipodes=$parametros[0];
        $sector=$parametros[1];
        $idagenda=$parametros[2];
        $fecheperiodo=$parametros[3];
        $idperiodo=$parametros[4];

        $nombreperiodo1=DB::table('periodos')
        ->where('periodos.id','=',$idperiodo)
        ->select('periodos.nombre_periodo')
        ->get();

        $nombreperiodo=$nombreperiodo1[0]->nombre_periodo;
        //dd($nombreperiodo);

        if($sector=='E'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
         ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//3 por ser asistencias 

        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
      
        ->where('asambleistas.sector_id','=',1)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','tiempos.salida','asistencias.propietaria','facultades.nombre')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='ESTUDIANTIL';
}

<<<<<<< HEAD
=======

        if($sector=='D'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
         ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//3 por ser asistencias 

        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
      
        ->where('asambleistas.sector_id','=',2)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','tiempos.salida','asistencias.propietaria','facultades.nombre')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='DOCENTE';
}


        if($sector=='ND'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
         ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//3 por ser asistencias 

        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
      
        ->where('asambleistas.sector_id','=',3)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','tiempos.salida','asistencias.propietaria','facultades.nombre')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='NO DOCENTE';
}
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2

        if($sector=='D'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
         ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//3 por ser asistencias 

        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
      
        ->where('asambleistas.sector_id','=',2)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','tiempos.salida','asistencias.propietaria','facultades.nombre')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='DOCENTE';
}


<<<<<<< HEAD
        if($sector=='ND'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
         ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//3 por ser asistencias 

        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
      
        ->where('asambleistas.sector_id','=',3)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','tiempos.salida','asistencias.propietaria','facultades.nombre')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='NO DOCENTE';
}



=======
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2




        $view =  \View::make('Reportes/Reporte_asistencias_sesion_plenaria_pdf', compact('resultados','sector','nombreperiodo'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
         $pdf->loadHTML($view)->setPaper('letter','landscape')->setWarnings(false);

        if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }

          public function Reporte_inasistencias_sesion_plenaria_pdf($tipo) 
    {
      
        $data = $this->getData();
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('Reportes/Reporte_inasistencias_sesion_plenaria_pdf', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view)->setPaper('letter','landscape')->setWarnings(false);

        if($tipo==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipo==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }


    

      public function Reporte_bitacora_correspondencia($tipo) 
    {
      
      //dd($tipo);

         $parametros = explode('.', $tipo);

  
         $tipodes=$parametros[0];
         $fechainicial=$parametros[1];
         $fechafinal= $parametros[2];


        $resultados = DB::table('peticiones')
        ->where
        ([
        ['peticiones.fecha','>=',$fechainicial],
        ['peticiones.fecha','<=',$fechafinal]
        ])
        ->get();

        $view =  \View::make('Reportes/Reporte_bitacora_correspondencia_pdf', 
                  compact('resultados', 'fechainicial', 'fechafinal'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        //$pdf->loadHTML($view)->setPaper('a4')->setOrientation('landscape'); // cambiar tamaño y orientacion del papel
         $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }

public function buscar_consolidados_renta(ReportesConsolidadosRentaRequest $request){
//dd($request->all());
  
        $mes=$this->numero_mes($request->fecha1);
<<<<<<< HEAD

        $mesnum=$request->fecha1;

        $tipodoc=0;

       //dd($agenda);


if($request->tipoDocumento=='E'){
 $tipodoc=1;   
}
  if($request->tipoDocumento=='D'){
 $tipodoc=2;
     
}
  if($request->tipoDocumento=='ND'){
$tipodoc=3;
   
}
     

$resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $request->anio)
        ->where('sectores.id','=', $tipodoc)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre')
        ->limit(1)
        ->get();

if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}
=======

        $mesnum=$request->fecha1;

        $tipodoc=0;

       //dd($agenda);


if($request->tipoDocumento=='E'){
 $tipodoc=1;   
}
  if($request->tipoDocumento=='D'){
 $tipodoc=2;
     
}
  if($request->tipoDocumento=='ND'){
$tipodoc=3;
   
}
     

$resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $request->anio)
        ->where('sectores.id','=', $tipodoc)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre')
        ->limit(1)
        ->get();
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2

if($resultados==NULL){

<<<<<<< HEAD

=======
 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}



>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2
         return view("Reportes.Reporte_consolidados_renta")
         ->with('resultados',$resultados)
         ->with('mes',$mes)
         ->with('tipo',$request->tipoDocumento);


         
      
     return view("Reportes.Reporte_consolidados_renta",['resultados'=>NULL]);

}
  



    public function buscar_planilla_dieta(ReportesRequest $request){

       //dd($request->all());
   
        
        $mes=$this->numero_mes($request->fecha1);

        $mesnum=$request->fecha1;



       //dd($agenda);

       if($request->tipoDocumento=='A'){



        $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.anio','=', $request->anio)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre','dietas.asambleista_id')->limit(1)->get();


        //dd($resultados);

        
}

if($request->tipoDocumento=='E'){
 
        $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $request->anio)
        ->where('sectores.id','=', 1)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre')->limit(1)->get();

    
}

  if($request->tipoDocumento=='D'){
 
        $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $request->anio)
        ->where('sectores.id','=', 2)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre')->limit(1)->get();

         
}

  if($request->tipoDocumento=='ND'){

        $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $request->anio)
        ->where('sectores.id','=', 3)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre')->limit(1)->get();

        
}
       /* $dieta = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->whereColumn(['users.name','like', $request->nombre],
                      ['dietas.mes','=',$mes])->select('users.name')->first();*/
<<<<<<< HEAD

        
        if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");
=======

        
        if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}


            //echo($dieta->name);

<<<<<<< HEAD
            //echo($dieta->name);

=======
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2
         
         return view("Reportes.Reporte_planilla_dieta")
         ->with('resultados',$resultados)
         ->with('mesnum',$mesnum)
         ->with('tipo',$request->tipoDocumento);
      
     return view("Reportes.Reporte_planilla_dieta",['resultados'=>NULL]);
    }


       public function buscar_permisos_temporales(ReportesPermisosTemporalesRequest $request){

        
      //  dd($request->all());


        $fechainicial=$request->fecha1;
        $fechafinal=$request->fecha2;
        
        
       // $fechainicial=date('Y-m-d');
       //$fechafinal=date('Y-m-d');
       // dd($fechafinal);
//if($request->nombre==''){


      /*  $resultados = DB::table('asambleistas')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('permisos','permisos.asambleista_id','=','asambleistas.id')
        ->whereBetween('permisos.fecha_permiso', [$fechainicial,$fechafinal])
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre')->limit(1)->get();*/



         $resultados=DB::table('agendas')
        ->join('asistencias','asistencias.agenda_id','=','agendas.id')
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',1)//1 por ser permisos temporales 
        ->where
([
  ['agendas.fecha','>=',$this->convertirfecha($fechainicial)],
  ['agendas.fecha','<=',$this->convertirfecha($fechafinal)]
])
->select('agendas.id','agendas.fecha','agendas.periodo_id')
->distinct()
        ->get();
 

if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}

                    
    
        return view("Reportes.Reporte_permisos_temporales")
         ->with('resultados',$resultados);

      // dd($resultados);


      return view("Reportes.Reporte_permisos_temporales",['resultados'=>NULL]);
    }


 public function buscar_asistencias(ReportesAsistenciasRequest $request){

        
       // dd($request->all());


        $fechainicial=$request->fecha1;
        $fechafinal=$request->fecha2;
        
        $sector=$request->tipoDocumento;
        $tipo=$request->tipoDocumento;
        
      


     if($sector=='E'){

     
        $sector='ESTUDIANTIL';
}


        if($sector=='D'){

        
        $sector='DOCENTE';
}


        if($sector=='ND'){

     
        $sector='NO DOCENTE';
}

     



        $resultados=DB::table('agendas')
        ->join('asistencias','asistencias.agenda_id','=','agendas.id')
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//1 por ser permisos temporales 
        
        ->where
([
  ['agendas.fecha','>=',$this->convertirfecha($fechainicial)],
  ['agendas.fecha','<=',$this->convertirfecha($fechafinal)]
])
->select('agendas.id','agendas.fecha','agendas.periodo_id')
->distinct()
        ->get();


if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}


        
        return view("Reportes.Reporte_asistencias_sesion_plenaria")
         ->with('resultados',$resultados)
         ->with('sector',$sector)
         ->with('tipo',$tipo);

 

      return view("Reportes.Reporte_asistencias_sesion_plenaria",['resultados'=>NULL]);
    }


    

<<<<<<< HEAD


=======


>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2
 public function buscar_bitacora_correspondencia(BuscarBitacoraCorrespRequest $request){

$fechainicial=$request->fecha1;
//dd($fechainicial);
//$fechainicial=str_replace('/','-',$fechainicial);
//$fecha = DateTime::createFromFormat('Y-m-d', $fechainicial);
//$fechainicial = $fecha->format('Y-m-d');

/*$fecha1conver= explode('/', $fechainicial); si sirve
$fechatrans=$fecha1conver[2].'-'.$fecha1conver[1].'-'.$fecha1conver[0];
$fechainicial = date('Y-m-d', strtotime($fechatrans));*/

//dd($this->convertirfecha($fechainicial));

//$fechainicial =strtotime($fechainicial);
$fechafinal=$request->fecha2;

//$fechafinal=str_replace('/','-',$fechafinal);
//$fechafinal = date('Y-m-d', strtotime($fechafinal));


<<<<<<< HEAD
=======

$resultados = DB::table('peticiones')
->where
([
  ['peticiones.fecha','>=',$this->convertirfecha($fechainicial)],
  ['peticiones.fecha','<=',$this->convertirfecha($fechafinal)]
])
->limit(1)
->get();
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2

$resultados = DB::table('peticiones')
->where
([
  ['peticiones.fecha','>=',$this->convertirfecha($fechainicial)],
  ['peticiones.fecha','<=',$this->convertirfecha($fechafinal)]
])
->limit(1)
->get();



<<<<<<< HEAD

=======
>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2
/*$resultados = DB::table('peticiones')
->where('peticiones.id','=',1)
->get();*/

//dd($fechainicial);
//dd($request->all());
//dd($resultados);


<<<<<<< HEAD
=======

if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}

>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2

if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}




    $uno=$this->convertirfecha($fechainicial);
    $dos=$this->convertirfecha($fechafinal);
         return view("Reportes.Reporte_bitacora_correspondencia")
         ->with('fechainicial',$uno)
         ->with('fechafinal',$dos)
         ->with('resultados',$resultados);




return view('Reportes.Reporte_bitacora_correspondencia',['resultados'=>NULL]);



 }

<<<<<<< HEAD
=======

    $uno=$this->convertirfecha($fechainicial);
    $dos=$this->convertirfecha($fechafinal);
         return view("Reportes.Reporte_bitacora_correspondencia")
         ->with('fechainicial',$uno)
         ->with('fechafinal',$dos)
         ->with('resultados',$resultados);




return view('Reportes.Reporte_bitacora_correspondencia',['resultados'=>NULL]);



 }

>>>>>>> 37933456c62873145bd0726da8a2bed21f723ef2
public function porcAsistencia($idAsambleista,$idSesion,$tipoasistencia){

    $horasreunion=DB::table('reuniones')
        ->selectRaw('ABS(sum(time_to_sec(timediff(inicio,fin)))/3600) as suma') 
        ->where('reuniones.id','=',$idSesion) //por el momento solo filtro por el id 
        ->where('reuniones.vigente','<>',1) //este where tiene que ir para no mostrar reuniones no terminadas        
        ->get();


    $horasasistencia=DB::table('asistencias')
        ->selectRaw('ABS(sum(time_to_sec(timediff(entrada,salida)))/3600) as suma') 
        ->where('asistencias.asambleista_id','=',$idAsambleista) 
        ->where('asistencias.agenda_id','=',$idSesion)//por el momento solo filtro por el id
        ->where('asistencias.estado_asistencia_id','=',$tipoasistencia) 
        ->get();
        
$porcAsistencia=($horasasistencia[0]->suma/$horasreunion[0]->suma)*100;


return $porcAsistencia; 

}


      public function Reporte_planilla_dieta($tipo) 
    {
      
        $parametros = explode('.', $tipo); //se reciben id asambleista mes y año de la dieta separados por un espacio
        $verdescar=$parametros[0];
        $id=$parametros[1];
        $mes=$parametros[2];
        $anio=$parametros[3];
        $mesnum=$parametros[4];



       /* $agenda=DB::table('agendas')     //agendas del mes y año seleccionado
        ->whereMonth('fecha','=',$mesnum)
        ->whereYear('fecha','=',$anio)
        ->get();

        dd($agenda);*/



    /*    $busqueda = DB::table('asambleistas')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('asambleistas.id','=', $id)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','sectores.nombre','personas.dui',
                 'personas.nit','personas.afp','personas.cuenta')->first();*/
      

         
/*
          $busqueda = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->where('dietas.anio','=', $anio)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.anio','sectores.id','sectores.nombre', DB::raw('SUM(dietas.anio) as total_sales'))
        ->groupBy('personas.primer_nombre')
        ->limit(3)
        ->get();*/


$busqueda=DB::table('asambleistas')
->join('users','asambleistas.user_id','=','users.id')
->join('sectores','asambleistas.sector_id','=','sectores.id')
->join('personas','users.persona_id','=','personas.id')
->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
         'personas.segundo_nombre','sectores.nombre','personas.dui',
         'personas.nit','personas.afp','personas.cuenta','asambleistas.id',DB::raw('0 AS dieta'),DB::raw('0 AS renta'))
->get(); //todos los asambleistas

//dd($busqueda);

$iva=0.0;
$porcentaje_asistencia=0.0;
$renta=0.0;
$monto_dieta=0.0;


//dd($parametros);
//dd($prueba);
//dd($iva,$porcentaje_asistencia,$renta,$monto_dieta);

//if($porcAsistencia<=$porcentaje_asistencia){ // se generara planilla de dieta si alcanza el porcentage de asistencia
                                               //toda la conprobacion del 80 porciento tiene que hacerse en otro lado

//retorn

//}
$cuenta=0;
foreach ($busqueda as $busq) {
   
$agendas_anio=DB::table('agendas') //todas las agendas vigentes del año seleccionado en las que participo cada hdp
->join('asistencias','asistencias.agenda_id','=','agendas.id')
->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
->whereYear('fecha','=',$anio)
->where('asambleistas.id','=',$busq->id)
->where('agendas.vigente','<>',1)
->get();

//dd($agendas_anio);

foreach ($agendas_anio as $agendas) {
    
    $parametros=DB::table('parametros')->get();

foreach ($parametros as $parametro) {

if($parametro->nombre_parametro=='iva'){ 
    $iva=$parametro->valor;
}

if($parametro->nombre_parametro=='porcentaje_asistencia'){ 
    $porcentaje_asistencia=($parametro->valor)*100;
}

if($parametro->nombre_parametro=='renta'){ 
    $renta=$parametro->valor;
}

if($parametro->nombre_parametro=='monto_dieta'){ 
    $monto_dieta=$parametro->valor;
}
//echo($parametro->nombre_parametro);
//$prueba=$parametro->nombre_parametro;
}


        $horasreunion=DB::table('agendas')
        ->selectRaw('ABS(sum(time_to_sec(timediff(inicio,fin)))/3600) as suma') 
        ->where('agendas.id','=',$agendas->id) //por el momento solo filtro por el id 
        ->where('agendas.vigente','<>',1) //este where tiene que ir para no mostrar reuniones no terminadas        
        ->get();



$horasasistencia=DB::table('asistencias')
        ->selectRaw('ABS(sum(time_to_sec(timediff(tiempos.entrada,tiempos.salida)))/3600) as suma') 
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//3 por ser asistencia normal 
        ->where('asistencias.asambleista_id','=',$busq->id) 
        ->where('asistencias.agenda_id','=',$agendas->id)//por el momento solo filtro por el id       
        ->get();
        


if($horasreunion[0]->suma>0.0){

$porcAsistencia=($horasasistencia[0]->suma/$horasreunion[0]->suma)*100;
}
else{

$porcAsistencia=0.0;

}


//dd($porcAsistencia);


if($porcAsistencia>=$porcentaje_asistencia){

$cantDiet = DB::table('dietas')  
->where('dietas.asambleista_id','=',$busq->id)
->where('dietas.anio','=',$anio)
->selectRaw('SUM(asistencia) AS asistencia')
->get();


$asistencianum=$cantDiet[0]->asistencia;

//dd($asistencianum);


if($asistencianum==0)
{
$monto_dieta=0.0;
}
else{

$monto_dieta=$monto_dieta*$asistencianum;

}


$busqueda[$cuenta]->dieta=$busqueda[$cuenta]->dieta+$monto_dieta;

$renta=$monto_dieta*$renta;
$renta=round($renta,2);
$busqueda[$cuenta]->renta=$busqueda[$cuenta]->renta+$renta;
}

    }



if($busqueda[$cuenta]->dieta==0.0){
unset($busqueda[$cuenta]);
}



    $cuenta=$cuenta+1;

}


/*
        $horasreunion=DB::table('agendas')
        ->selectRaw('ABS(sum(time_to_sec(timediff(inicio,fin)))/3600) as suma') 
        ->where('agendas.id','=',1) //por el momento solo filtro por el id 
        ->where('agendas.vigente','<>',1) //este where tiene que ir para no mostrar reuniones no terminadas        
        ->get();

        

$horasasistencia=DB::table('asistencias')
        ->selectRaw('ABS(sum(time_to_sec(timediff(tiempos.entrada,tiempos.salida)))/3600) as suma') 
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//1 por ser permisos temporales 
        ->where('asistencias.asambleista_id','=',$id) 
        ->where('asistencias.agenda_id','=',1)//por el momento solo filtro por el id       
        ->get();
        
*/
//$porcAsistencia=($horasasistencia[0]->suma/$horasreunion[0]->suma)*100;

//$porcAsistencia=this->porcAsistencia($id,);

//dd($porcAsistencia);





 //debe devolver siempre un registro porque ya se realizao previamente esta busqueda

//dd($cantDiet);



//dd($monto_dieta);



//dd($renta);
    //dd($horasreunion);
    //dd($horasasistencia);
     

       /* $nombre1=$busqueda->primer_nombre;
        $nombre2=$busqueda->segundo_nombre;
        $apellido1=$busqueda->primer_apellido;
        $apellido2=$busqueda->segundo_apellido;*/

      /*  $sector=$busqueda->nombre;
        $dui=$busqueda->dui;
        $nit=$busqueda->nit;
        $afp=$busqueda->afp;
        $cuenta=$busqueda->cuenta;*/

        $sector=0;
        $dui=0;
        $nit=0;
        $afp=0;
        $cuenta=0;

      //  dd($busqueda);

      /*  $nombrecompleto=$nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;*/

        //dd($nombrecompleto,$dui,$nit,$mes,$anio,$sector);

        $view =\View::make('Reportes/Reporte_planilla_dieta_pdf', compact('busqueda','sector','nit', 'mes', 'anio','horasreunion','monto_dieta','renta'))->render();
      //$view =\View::make('Reportes/Reporte_planilla_dieta_pdf', compact('nombrecompleto','sector','nit', 'mes', 'anio','horasreunion','monto_dieta','renta'))->render();
     
        $pdf =\App::make('dompdf.wrapper');      
        //$pdf->loadHTML($view)->setPaper('a4')->setOrientation('landscape'); // cambiar tamaño y orientacion del papel
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

         if($verdescar==1)
        {
            return $pdf->stream('reporte');
        }
        if($verdescar==2)
        {
            return $pdf->download('reporte.pdf'); 
        }
        //return $pdf->stream('reporte.pdf'); //mostrar pdf en pagina
        //return $pdf->download('reporte.pdf'); // descargar el archivo pdf
    } 


public function Reporte_planilla_dieta_prof_Est_pdf($tipo) 
    {
      
      
        $parametros = explode('.', $tipo); //se reciben id asambleista mes y año de la dieta separados por un espacio
        $verdescar=$parametros[0];
        $mes=$parametros[1];
        $anio=$parametros[2];


     $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $anio)
        ->where('sectores.id','=', 1)  //serctor 2 por ser profesional docente
        ->select('asambleistas.id','personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre as nom_sect',
                 'facultades.nombre as nom_fact','dietas.asistencia')->orderBy('nom_fact', 'desc')->get();


      /*  $agenda=DB::table('agendas')     //agendas del mes y año seleccionado
        ->whereMonth('fecha','=',$mes)
        ->whereYear('fecha','=',$anio)
        ->where('vigente','=',0) //cero para agendas que estan vigentes 
        ->orderBy('fecha', 'asc')
        ->get();//esto devolvera un numero N (0-4) de reuniones hechas en el mes
*/

     
       /* $agendas=DB::table('agendas')
        ->join('asistencias','asistencias.agenda_id','=','agendas.id')
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//1 por ser permisos temporales 
        ->whereMonth('agendas.fecha','=',$mes)
        ->whereYear('agendas.fecha','=',$anio)
        ->select('asistencias.asambleista_id','agendas.id')
        ->get();*/

        $monto_dieta=DB::table('parametros')
        ->where('parametros.parametro','=','mdi')
        ->select('parametros.valor')
        ->first();
        
       // dd($monto_dieta->valor);

        $view =  \View::make('Reportes/Reporte_planilla_dieta_prof_Est_pdf', compact('resultados','mes','anio','monto_dieta'))->render();
        $pdf = \App::make('dompdf.wrapper');    
        $pdf->getDomPDF()->set_option("enable_php", true);  
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($verdescar==1)
        {
            return $pdf->stream('reporte');
        }
        if($verdescar==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }









public function Reporte_planilla_dieta_prof_Doc_pdf($tipo) 
    {
      
      
        $parametros = explode('.', $tipo); //se reciben id asambleista mes y año de la dieta separados por un espacio
        $verdescar=$parametros[0];
        $mes=$parametros[1];
        $anio=$parametros[2];


     $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $anio)
        ->where('sectores.id','=', 2)  //serctor 2 por ser profesional docente
        ->select('asambleistas.id','personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre as nom_sect',
                 'facultades.nombre as nom_fact','dietas.asistencia')->orderBy('nom_fact', 'desc')->get();


      /*  $agenda=DB::table('agendas')     //agendas del mes y año seleccionado
        ->whereMonth('fecha','=',$mes)
        ->whereYear('fecha','=',$anio)
        ->where('vigente','=',0) //cero para agendas que estan vigentes 
        ->orderBy('fecha', 'asc')
        ->get();//esto devolvera un numero N (0-4) de reuniones hechas en el mes
*/

     
       /* $agendas=DB::table('agendas')
        ->join('asistencias','asistencias.agenda_id','=','agendas.id')
        ->join('tiempos','asistencias.id','=','tiempos.asistencia_id')
        ->join('estado_asistencias','tiempos.estado_asistencia_id','=','estado_asistencias.id')
        ->where('estado_asistencias.id','=',3)//1 por ser permisos temporales 
        ->whereMonth('agendas.fecha','=',$mes)
        ->whereYear('agendas.fecha','=',$anio)
        ->select('asistencias.asambleista_id','agendas.id')
        ->get();*/

        $monto_dieta=DB::table('parametros')
        ->where('parametros.parametro','=','mdi')
        ->select('parametros.valor')
        ->first();
        
       // dd($monto_dieta->valor);

        $view =  \View::make('Reportes/Reporte_planilla_dieta_prof_Doc_pdf', compact('resultados','mes','anio','monto_dieta'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($verdescar==1)
        {
            return $pdf->stream('reporte');
        }
        if($verdescar==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }


     public function Reporte_planilla_dieta_prof_noDocpdf($tipo) 
    {
        
        $parametros = explode('.', $tipo); //se reciben id asambleista mes y año de la dieta separados por un espacio
        $verdescar=$parametros[0];
        $mes=$parametros[1];
        $anio=$parametros[2];

       $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $anio)
        ->where('sectores.id','=', 3)
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'facultades.nombre as nom_fact','personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre as nom_sect','dietas.asistencia'
                 )->orderBy('nom_fact', 'desc')->get();
        

        $monto_dieta=DB::table('parametros')
        ->where('parametros.parametro','=','mdi')
        ->select('parametros.valor')
        ->first();



       
        $view =  \View::make('Reportes/Reporte_planilla_dieta_prof_noDocpdf', compact('resultados','mes','anio','monto_dieta'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($verdescar==1)
        {
            return $pdf->stream('reporte');
        }
        if($verdescar==2)
        {
            return $pdf->download('reporte.pdf'); 
        }
        

    }



 


      public function Reporte_consolidados_renta($tipo) //No docente
    {
      
        

 
        $parametros = explode('.', $tipo); //se reciben id asambleista mes y año de la dieta separados por un espacio
        $tipodes=$parametros[0];
        $sector=$parametros[1];
        $mes=$parametros[2];
        $anio=$parametros[3];

      

        $monto_dieta=DB::table('parametros')
        ->where('parametros.parametro','=','mdi')
        ->select('parametros.valor')
        ->first();
        
        $renta=DB::table('parametros')
        ->where('parametros.parametro','=','ren')
        ->select('parametros.valor')
        ->first();
        

        //dd($tipo);

       




        if($sector=='E'){

           $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $anio)
        ->where('sectores.id','=', 1)  //serctor 2 por ser profesional docente
        ->select('asambleistas.id','personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre as nom_sect',
                 'facultades.nombre as nom_fact','dietas.asistencia','personas.nit')->orderBy('nom_fact', 'desc')->get();
          //dd($resultados);
        $sector='ESTUDIANTIL';
}

        if($sector=='D'){

           $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $anio)
        ->where('sectores.id','=', 2)  //serctor 2 por ser profesional docente
        ->select('asambleistas.id','personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre as nom_sect',
                 'facultades.nombre as nom_fact','dietas.asistencia','personas.nit')->orderBy('nom_fact', 'desc')->get();
          //dd($resultados);
        $sector='DOCENTE';
}


        if($sector=='ND'){

           $resultados = DB::table('dietas')
        ->join('asambleistas','dietas.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('sectores','asambleistas.sector_id','=','sectores.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('dietas.mes','=', $mes)
        ->where('dietas.anio','=', $anio)
        ->where('sectores.id','=', 3)  //serctor 2 por ser profesional docente
        ->select('asambleistas.id','personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','dietas.mes','dietas.anio','sectores.id','sectores.nombre as nom_sect',
                 'facultades.nombre as nom_fact','dietas.asistencia','personas.nit')->orderBy('nom_fact', 'desc')->get();

        $sector='NO DOCENTE';
}







        $view =  \View::make('Reportes/Reporte_consolidados_renta_pdf', compact('resultados','sector','monto_dieta','renta','mes','anio'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
         $pdf->loadHTML($view)->setPaper('letter','landscape')->setWarnings(false);

        if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }



    }
    
    
    
      public function Reporte_consolidados_renta_docente($tipo) 
    {
      
     
  


        //dd($tipo);


        $parametros = explode('.', $tipo);
        $tipodes=$parametros[0];
        $sector=$parametros[1];
        $idagenda=$parametros[2];
        $fecheperiodo=$parametros[3];
        $idperiodo=$parametros[4];

        $nombreperiodo1=DB::table('periodos')
        ->where('periodos.id','=',$idperiodo)
        ->select('periodos.nombre_periodo')
        ->get();

        $nombreperiodo=$nombreperiodo1[0]->nombre_periodo;
        //dd($nombreperiodo);

       

        if($sector=='D'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
        ->where('asistencias.estado_asistencia_id','=',3)//3 por ser asistencias normales 
        ->where('asambleistas.sector_id','=',2)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','asistencias.salida','asistencias.propietario','facultades.nombre','personas.nit')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='DOCENTE';
}


        if($sector=='ND'){

         $resultados=DB::table('asistencias')
        ->join('asambleistas','asistencias.asambleista_id','=','asambleistas.id')
        ->join('users','asambleistas.user_id','=','users.id')
        ->join('personas','users.persona_id','=','personas.id')
        ->join('facultades','asambleistas.facultad_id','=','facultades.id')
        ->where('asistencias.agenda_id','=',$idagenda)//por el momento solo filtro por el id
        ->where('asistencias.estado_asistencia_id','=',3)//3 por ser asistencias normales 
        ->where('asambleistas.sector_id','=',3)//sector estudiantil
        ->select('personas.primer_apellido','personas.primer_nombre','personas.segundo_apellido',
                 'personas.segundo_nombre','asistencias.entrada','asistencias.salida','asistencias.propietario','facultades.nombre','personas.nit')
        ->orderBy('facultades.nombre', 'desc')

        ->get();
        $sector='NO DOCENTE';
}







        $view =  \View::make('Reportes/Reporte_consolidados_renta_docente_pdf', compact('resultados','sector','nombreperiodo'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
         $pdf->loadHTML($view)->setPaper('letter','landscape')->setWarnings(false);

        if($tipodes==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipodes==2)
        {
            return $pdf->download('reporte.pdf'); 
        }



    }
    

       public function Reporte_constancias_renta($tipo) 
    {
      
        $data = $this->getData();
        $date = date('Y-m-d');
        $invoice = "2222";

        dd($tipo);
        $view =  \View::make('Reportes/Reporte_permisos_permanentes_pdf', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');     
        $pdf->getDomPDF()->set_option("enable_php", true); 
        //$pdf->loadHTML($view)->setPaper('a4')->setOrientation('landscape'); // cambiar tamaño y orientacion del papel
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($tipo==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipo==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }

    

    
       public function Reporte_constancias_renta_JD($tipo) 
    {
      
        $data = $this->getData();
        $date = date('Y-m-d');
        $invoice = "2222";
        dd($tipo);
        $view =  \View::make('Reportes/Reporte_permisos_permanentes_pdf', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        //$pdf->loadHTML($view)->setPaper('a4')->setOrientation('landscape'); // cambiar tamaño y orientacion del papel
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($tipo==1)
        {
            return $pdf->stream('reporte');

        }
        if($tipo==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }

     
       public function Reporte_Convocatorias($tipo) 
    {
      
        $data = $this->getData();
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('Reportes/Reporte_Convocatorias_pdf', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');      
        $pdf->getDomPDF()->set_option("enable_php", true);
        //$pdf->loadHTML($view)->setPaper('a4')->setOrientation('landscape'); // cambiar tamaño y orientacion del papel
        $pdf->loadHTML($view)->setPaper('letter','portrait')->setWarnings(false);

        if($tipo==1)
        {
            return $pdf->stream('reporte');
        }
        if($tipo==2)
        {
            return $pdf->download('reporte.pdf'); 
        }

        //return $pdf->stream('invoice.pdf'); //mostrar pdf en pagina
        //return $pdf->download('invoice.pdf'); // descargar el archivo pdf


    }

    public function buscar_permisos_permanentes(ReportesPermisospermanentesRequest $request){



$fechainicial=$request->fecha1;
//dd($fechainicial);
//$fechainicial=str_replace('/','-',$fechainicial);
//$fecha = DateTime::createFromFormat('Y-m-d', $fechainicial);
//$fechainicial = $fecha->format('Y-m-d');

/*$fecha1conver= explode('/', $fechainicial); si sirve
$fechatrans=$fecha1conver[2].'-'.$fecha1conver[1].'-'.$fecha1conver[0];
$fechainicial = date('Y-m-d', strtotime($fechatrans));*/

//dd($this->convertirfecha($fechainicial));

//$fechainicial =strtotime($fechainicial);
$fechafinal=$request->fecha2;

//$fechafinal=str_replace('/','-',$fechafinal);
//$fechafinal = date('Y-m-d', strtotime($fechafinal));



$resultados = DB::table('permisos')
->where
([
  ['permisos.fecha_permiso','>=',$this->convertirfecha($fechainicial)],
  ['permisos.fecha_permiso','<=',$this->convertirfecha($fechafinal)]
])
->limit(1)
->get();




/*$resultados = DB::table('peticiones')
->where('peticiones.id','=',1)
->get();*/

//dd($fechainicial);
//dd($request->all());
//dd($resultados);
if($resultados==NULL){

 $request->session()->flash("warning", "No se encontraron registros");

}
else{
 $request->session()->flash("success", "Busqueda terminada con exito");
}
    $uno=$this->convertirfecha($fechainicial);
    $dos=$this->convertirfecha($fechafinal);
         return view("Reportes.Reporte_permisos_permanentes")
         ->with('fechainicial',$uno)
         ->with('fechafinal',$dos)
         ->with('resultados',$resultados);





return view("Reportes.Reporte_permisos_permanentes",['resultados'=>NULL]);
    }

    

    public function Mensaje(ReportesPermisosTemporalesRequest $request)
    {
      

       // dd($request->all());

           
          $respuesta = new \stdClass();
          // $respuesta->mensaje = (new Mensaje("Exito", "Comision: establecida como inactiva", "warning"))->toArray();  
          $respuesta->mensaje = (new Mensaje("Exito", "Comision:  establecida como activa", "success"))->toArray();
          $request->session()->flash("success", "Comision agregada con exito");

         return view("Reportes.Reporte_permisos_temporales")
         ->with('resultados',NULL);

    }

   // public function listado(){
   // return view("Reportes.listado_reportes");
  //  }


    public function getData() 
    {

        $data=Array(1);
        $data =  [
            'quantity'      => '1' ,
            'description'   => 'some ramdom text',
            'price'   => '500',
            'total'     => '500'
        ];

        
      

        return $data;
    }
    

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function convertirfecha($fecha){

$fecha1conver= explode('/', $fecha);
$fechatrans=$fecha1conver[2].'-'.$fecha1conver[1].'-'.$fecha1conver[0];
$fechainicial = date('Y-m-d', strtotime($fechatrans));

        return $fechainicial;
    }


    public function numero_mes($mesnum)
    {
        
        $mes=' ';
        if($mesnum==1){

            $mes='enero';
        }
        if($mesnum==2){

            $mes='febrero';
        }
        if($mesnum==3){

            $mes='marzo';
        }
        if($mesnum==4){

            $mes='abril';
        }
        if($mesnum==5){

            $mes='mayo';
        }
        if($mesnum==6){

            $mes='junio';
        }
        if($mesnum==7){

            $mes='julio';
        }
        if($mesnum==8){

            $mes='agosto';
        }
        if($mesnum==9){

            $mes='septiembre';
        }
        if($mesnum==10){

            $mes='octubre';
        }
        if($mesnum==11){

            $mes='noviembre';
        }
        if($mesnum==12){

            $mes='diciembre';
        }

        return $mes;
    }
}
