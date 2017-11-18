<?php

namespace App\Http\Controllers;

use App\Asambleista;
use App\Cargo;
use App\Clases\Mensaje;
use App\Comision;
use App\Peticion;
use App\Reunion;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ComisionRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

;

class ComisionController extends Controller
{

    //funcion generica para obtener la comision, los integrantes de dicha comision y todos los asambleistas en la app
    public function obtener_datos(Request $request)
    {
        $comision = Comision::find($request->get("comision_id"));

        //se obtiene todos los asambleistas que pertenecen a la comision
        $resultados = Cargo::where("comision_id", $request->get("comision_id"))->where("activo", 1)->get();
        $asambleistas_ids = array();

        //array con los id de los asambleistas
        foreach ($resultados as $resultado)
            array_push($asambleistas_ids, $resultado->asambleista->id);

        $asambleistas = Asambleista::where("asambleistas.activo", "=", 1)
            ->whereNotIn("asambleistas.id", $asambleistas_ids)
            ->get();

        //obtener los integrantes de la comision y que esten activos en el periodo activo
        $integrantes = Cargo::join("asambleistas", "cargos.asambleista_id", "=", "asambleistas.id")
            ->join("periodos", "asambleistas.periodo_id", "=", "periodos.id")
            ->where("cargos.comision_id", $request->get("comision_id"))
            ->where("asambleistas.activo", 1)
            ->where("periodos.activo", 1)
            ->where("cargos.activo", 1)
            ->get();

        return ["comision" => $comision, "integrantes" => $integrantes, "asambleistas" => $asambleistas];
    }

    /******************** METODOS GET *********************************/

    //mostrar las comisiones activas e inactivas
    public function mostrar_comisiones()
    {
        //se obtienen todas las comisiones en orden alfabetico
        $comisiones = Comision::orderBy("nombre", "asc")->get();
        $cargos = Cargo::all();
        return view("Comisiones.CrearComision", ['comisiones' => $comisiones, 'cargos' => $cargos]);
    }

    //mostrar las comisiones activas
    public function administrar_comisiones()
    {
        //obtener las comisiones, omitiendo la JD
        $comisiones = Comision::where("activa", 1)
            ->where("id","!=",1)
            ->get();
        $cargos = Cargo::all();
        return view("Comisiones.AdministrarComision", ['comisiones' => $comisiones, 'cargos' => $cargos]);
    }

    /******************** METODOS POST *********************************/

    public function listado_peticiones_comision(Request $request)
    {
        //obtengo una comision
        $comision = Comision::find($request->get("comision_id"));
        $peticiones = $comision->peticiones;
        return view("Comisiones.listado_peticiones_comision", ["comision" => $comision, "peticiones" => $peticiones]);
    }

    //mostrar listado de las comisiones, con su total de integrantes
    public function gestionar_asambleistas_comision(Request $request)
    {
        $datos = $this->obtener_datos($request);
        return view("Comisiones.AdministrarIntegrantes", ["comision" => $datos["comision"], "integrantes" => $datos["integrantes"], "asambleistas" => $datos["asambleistas"]]);
    }

    //funcion que se encarga de crear una comision
    public function crear_comision(ComisionRequest $request)
    {
        $comision = new Comision();
        $comision->codigo = $request->get("codigo");
        $comision->nombre = $request->get("nombre");
        $comision->permanente = 0; //0: transitoria, 1: permanente
        $comision->descripcion = $request->get("nombre");
        $comision->activa = 1;
        $comision->save();

        $request->session()->flash("success", "Comision " . $comision->nombre . " agregada con exito");
        return redirect()->route("mostrar_comisiones");
    }

    //funcion para actualizar una comision
    public function actualizar_comision(Request $request)
    {
        if ($request->ajax()) {
            //se obtiene la comision que coincida con el id enviado
            $comision = Comision::find($request->get("id"));
            $respuesta = new \stdClass();

            //se actualiza el estado de la comision, dependiendo de su previo estado
            if ($comision->activa == 1) {
                $comision->activa = 0;
                $respuesta->mensaje = (new Mensaje("Exito", "Comision: " . $comision->nombre . " establecida como inactiva", "warning"))->toArray();
            } else {
                $comision->activa = 1;
                $respuesta->mensaje = (new Mensaje("Exito", "Comision: " . $comision->nombre . " establecida como activa", "success"))->toArray();
            }

            //una vez efectuado el cambio, se realiza el cambio en la BD
            $comision->save();

            //se genera la respuesta json
            return new JsonResponse($respuesta);
        }
    }

    //funcion para agregar asambleistas a una comision
    public function agregar_asambleistas_comision(Request $request)
    {
        $asambleistas = $request->get("asambleistas");
        $comision = Comision::find($request->get("comision_id"));

        foreach ($asambleistas as $asambleista) {
            $cargo = new Cargo();
            $cargo->comision_id = $request->get("comision_id");
            $cargo->asambleista_id = $asambleista;
            $cargo->inicio = Carbon::now();
            $cargo->cargo = "Asambleista";
            $cargo->activo = 1;
            $cargo->save();
            Cache::flush();
        }

        //$request->session()->flash("success", "Asambleista(s) agregado(s) con exito " .$cargo->id);
        $request->session()->flash("success", "Asambleista(s) agregado(s) con exito ");

        $datos = $this->obtener_datos($request);
        return view("Comisiones.AdministrarIntegrantes", ["comision" => $datos["comision"], "integrantes" => $datos["integrantes"], "asambleistas" => $datos["asambleistas"]]);
    }

    public function retirar_asambleista_comision(Request $request)
    {

        $asambleista_id = $request->get("asambleista_id");
        $comision_id = $request->get("comision_id");

        $asambleista_comision = Cargo::where("asambleista_id", $asambleista_id)
            ->where("comision_id", $comision_id)
            ->where("activo", 1)
            ->first();

        $asambleista_comision->activo = 0;
        $asambleista_comision->fin = Carbon::now();
        $asambleista_comision->save();
        Cache::flush();

        $request->session()->flash("success", "Asambleista retirado de la comision con exito");

        $datos = $this->obtener_datos($request);
        return view("Comisiones.AdministrarIntegrantes", ["comision" => $datos["comision"], "integrantes" => $datos["integrantes"], "asambleistas" => $datos["asambleistas"]]);
    }

    public function trabajo_comision(Request $request)
    {
        $comision = Comision::find($request->get("comision_id"));
        return view("Comisiones.TrabajoComision", ["comision" => $comision]);

    }

    public function listado_reuniones_comision(Request $request){
        $reuniones = Reunion::where('id','!=',0)->where('comision_id',$request->comision_id)->orderBy('created_at','DESC')->get();

        return view('Comisiones.listado_reuniones_comision',["reuniones"=>$reuniones]);
    }

    //PENDIENTE DE TRAER LAS PETICIONES
    public function reunion_comision(Request $request){
        $peticiones = Peticion::where('id','!=',0)->orderBy('estado_peticion_id','ASC')->orderBy('updated_at','ASC')->get(); // Primero ordenar por el estado, despues los estados ordenarlo por fechas

        return view('Comisiones.reunion_comision')
            ->with('peticiones',$peticiones);
    }
}
