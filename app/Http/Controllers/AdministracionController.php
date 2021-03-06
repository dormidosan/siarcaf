<?php

namespace App\Http\Controllers;

use App\Asambleista;
use App\Cargo;
use App\Clases\Mensaje;
use App\Comision;
use App\Facultad;
use App\Modulo;
use App\Periodo;
use App\Permiso;
use App\Persona;
use App\Plantilla;
use App\Rol;
use App\Sector;
use App\User;
use App\Parametro;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

use Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use App\Http\Requests\UsuarioRequest;
use App\Http\Requests\PeriodoRequest;
use Illuminate\Support\Facades\Auth;
use Excel;

class AdministracionController extends Controller
{
    public function registrar_usuario()
    {
        $facultades = Facultad::all();
        $sectores = Sector::all();
        $tipos_usuario = Rol::all();
        return view("Administracion.RegistrarUsuarios", ["facultades" => $facultades, "sectores" => $sectores, "tipos_usuario" => $tipos_usuario]);
    }

    public function guardar_usuario(UsuarioRequest $request)
    {
        //Se crea un objeto de tipo persona y se asocia lo que se recibe del form a su respectiva variable,
        //una vez ingresado la nueva persona, ya se tiene acceso a todos sus datos.
        $persona = new Persona();
        $persona->primer_nombre = $request->get("primer_nombre");
        $persona->segundo_nombre = $request->get("segundo_nombre");
        $persona->primer_apellido = $request->get("primer_apellido");
        $persona->segundo_apellido = $request->get("segundo_apellido");
        $persona->dui = $request->get("dui");
        $persona->nit = $request->get("nit");

        //sentencia para agregar la foto
        //$persona->foto = $request->get("foto");

        $persona->afp = $request->get("afp");
        $persona->cuenta = $request->get("cuenta");
        $persona->save();

        $usuario = new User();
        $usuario->rol_id = $request->get("tipo_usuario");
        $usuario->persona_id = $persona->id;
        $usuario->name = $persona->primer_nombre . "." . $persona->primer_apellido;
        $usuario->password = bcrypt("ATB");
        $usuario->email = $request->get("correo");
        $usuario->activo = 1;
        $usuario->save();

        $periodo_activo = Periodo::where("activo", "=", 1)->first();
        //dd($periodo_activo);
        $asambleista = new Asambleista();
        $asambleista->user_id = $usuario->id;
        $asambleista->periodo_id = $periodo_activo->id;
        $asambleista->facultad_id = $request->get("facultad");
        $asambleista->sector_id = $request->get("sector");
        $asambleista->propietario = $request->get("propietario");
        //setea al user como un asambleista activo
        $asambleista->activo = 1;

        $hoy = Carbon::now();
        $inicio_periodo = Carbon::createFromFormat("Y-m-d", $periodo_activo->inicio);

        if ($hoy > $inicio_periodo) {
            $asambleista->inicio = $hoy;
        } else {
            $asambleista->inicio = $inicio_periodo;
        }
        $asambleista->save();

        $request->session()->flash("success", "Usuario agregado con exito");
        return redirect()->route("mostrar_formulario_registrar_usuario");
    }

    /*
     * Funcion que esta asociada a un metodo GET, que muestra todos los periodos AGU
     * hasta la fecha
     */
    public function gestionar_plantillas()
    {
        $plantillas = Plantilla::all();
        return view("Administracion.gestionar_plantillas", ["plantillas" => $plantillas]);
    }

    public function descargar_plantilla($id)
    {
        $plantilla = Plantilla::find($id);
        $ruta_plantilla = "../storage/plantillas/" . $plantilla->path;
        return response()->download($ruta_plantilla);
    }

    public function mostrar_periodos_agu()
    {
        $periodos = Periodo::orderBy("id", "desc")->get();
        return view("Administracion.PeriodosAGU", ["periodos" => $periodos]);
    }

    public function guardar_periodo(PeriodoRequest $request)
    {
        $periodo_activo = Periodo::where("activo", 1)->first();

        if (!empty($periodo_activo)) {
            $request->session()->flash("error", "Ya existe un periodo activo");
            return redirect()->back();
        } else {
            $periodo = new Periodo();
            $periodo->nombre_periodo = $request->get("nombre_periodo");
            $periodo->inicio = Carbon::createFromFormat('d-m-Y', $request->get("inicio"));
            $periodo->fin = Carbon::createFromFormat('d-m-Y', $request->get("inicio"))->addYear(2);
            $periodo->activo = 1;
            $periodo->save();

            if ($request->hasFile("excel") && $request->file('excel')->isValid()) {
                $extension = $request->excel->extension();
                if ($extension == "xlsx" || $extension == "csv") {
                    $path = $request->excel->path();
                    $data = Excel::load($path, function ($reader) {
                    })->get();
                    if (!empty($data) && $data->count()) {
                        foreach ($data as $key => $value) {
                            //$asambleistas[] = ['name' => $value->name, 'phone' => $value->phone, 'email' => $value->email];
                            $persona = new Persona();
                            $persona->primer_nombre = $value->primer_nombre;
                            $persona->segundo_nombre = $value->segundo_nombre;
                            $persona->primer_apellido = $value->primer_apellido;
                            $persona->segundo_apellido = $value->segundo_nombre;
                            $persona->dui = $value->dui;
                            $persona->nit = $value->nit;

                            //sentencia para agregar la foto
                            //$persona->foto = $request->get("foto");

                            $persona->afp = $value->afp;
                            $persona->cuenta = $value->cuenta;
                            $persona->save();

                            $usuario = new User();
                            switch ($value->tipo_usuario) {
                                case "Administrador":
                                    $usuario->rol_id = 1;
                                    break;
                                case "Secretario":
                                    $usuario->rol_id = 2;
                                    break;
                                case "Asambleista":
                                    $usuario->rol_id = 3;
                                    break;
                            }

                            $usuario->persona_id = $persona->id;
                            $usuario->name = $persona->primer_nombre . "." . $persona->primer_apellido;
                            $usuario->password = bcrypt("ATB");
                            $usuario->email = $value->correo;
                            $usuario->activo = 1;
                            $usuario->save();

                            $periodo_activo = Periodo::where("activo", "=", 1)->first();
                            //dd($periodo_activo);
                            $asambleista = new Asambleista();
                            $asambleista->user_id = $usuario->id;
                            $asambleista->periodo_id = $periodo_activo->id;
                            $asambleista->facultad_id = (Facultad::where("nombre", strtoupper($value->facultad))->first())->id;
                            $asambleista->sector_id = (Sector::where("nombre", $value->sector)->first())->id;
                            switch ($value->propetario) {
                                case "Si":
                                    $asambleista->propietario = 1;
                                    break;
                                case "No":
                                    $asambleista->propietario = 0;
                                    break;
                            }
                            //setea al user como un asambleista activo
                            $asambleista->activo = 1;

                            $hoy = Carbon::now();
                            $inicio_periodo = Carbon::createFromFormat("Y-m-d", $periodo_activo->inicio);

                            if ($hoy > $inicio_periodo) {
                                $asambleista->inicio = $hoy;
                            } else {
                                $asambleista->inicio = $inicio_periodo;
                            }
                            $asambleista->save();
                        }
                    }
                }
            }
            $request->session()->flash("success", "Periodo creado con exito");
            return redirect()->route("periodos_agu");
        }
    }

    public function finalizar_periodo(Request $request)
    {
        if ($request->ajax()) {
            $periodo = Periodo::find($request->get("periodo_id"));
            $periodo->activo = 0;
            $respuesta = new \stdClass();
            $respuesta->mensaje = (new Mensaje("Exito", "Periodo: " . $periodo->nombre_periodo . " finalizado", "success"))->toArray();
            $periodo->save();

            //se genera la respuesta json
            return new JsonResponse($respuesta);
        }
    }

    public function parametros(Request $request)
    {
        $parametros = Parametro::all();
        return view('Administracion.Parametros')
            ->with('parametros', $parametros);
    }

    public function almacenar_parametro(Request $request)
    {
        //dd($request->all());
        $parametro = Parametro::where('id', '=', $request->id_parametro)->firstOrFail();
        $parametro->valor = $request->nuevo_valor;
        $parametro->save();

        $parametros = Parametro::all();
        $request->session()->flash("success", "Parametro actualizado con exito");

        return redirect()->route("parametros");

    }

    public function almacenar_plantilla(Request $request)
    {
        $vieja_plantilla_id = $request->plantilla_id;
        $nueva_plantilla = $this->guardarPlantilla($request->plantilla, $vieja_plantilla_id, 'plantillas');
        $plantillas = Plantilla::all();
        return view("Administracion.gestionar_plantillas", ["plantillas" => $plantillas]);

    }

    public function administracion_usuarios()
    {
        $comisiones = Comision::where("activa", 1)->get();
    }

    public function cambiar_perfiles()
    {
        $perfiles = Rol::all();
        $periodo_activo = Periodo::where("activo", 1)->firstOrFail();

        /*
         * Obtener la entidad del actual usuario logueado
         * con el fin de filtrar el listado de asambleistas y no mostrarlo en dicha lista, con el proposito de evitar
         * que el usuario logueado cambie su perfil
         *
         */
        $current_user = Asambleista::where("periodo_id", $periodo_activo->id)->where("activo", 1)->where("id", Auth::user()->id)->firstOrFail();

        //se genera el listado de asambleistas, sin incluir el actual logueado en el sistema
        $asambleistas = Asambleista::where("periodo_id", $periodo_activo->id)->where("activo", 1)->where("id", "!=", $current_user->id)->get();

        return view("Administracion.cambiar_perfiles", ["perfiles" => $perfiles, "asambleistas" => $asambleistas]);

    }

    public function actualizar_perfil_usuario(Request $request)
    {
        if ($request->ajax()) {

            $asambleista = Asambleista::find($request->get("idAsambleista"));

            $perfil = Rol::find($request->get("idPerfil"));
            $asambleista->user->rol_id = $perfil->id;
            $asambleista->user->save();

            //obtiendo la informacion necesaria para renderizarla y mostrarla al usuario
            $perfiles = Rol::all();
            $periodo_activo = Periodo::where("activo", 1)->firstOrFail();
            $current_user = Asambleista::where("periodo_id", $periodo_activo->id)->where("activo", 1)->where("id", Auth::user()->id)->firstOrFail();
            $asambleistas = Asambleista::where("periodo_id", $periodo_activo->id)->where("activo", 1)->where("id", "!=", $current_user->id)->get();

            $respuesta = new \stdClass();
            $body_tabla = "";
            foreach ($asambleistas as $asambleista) {
                $body_tabla .= "<tr>
                                    <td>" . $asambleista->user->persona->primer_nombre . ' ' . $asambleista->user->persona->segundo_nombre . ' ' . $asambleista->user->persona->primer_apellido . ' ' . $asambleista->user->persona->segundo_apellido . "</td>
                                    <td>" . ucfirst($asambleista->user->rol->nombre_rol) . "</td>
                                    <td>
                                        <select id='perfil' class='form-control' onchange='actualizar_perfil_usuario(" . $asambleista->id . ",this.value)'>
                                            <option> -- Seleccione una opcion --</option>";

                foreach ($perfiles as $perfil) {
                    $body_tabla .= "<option value='" . $perfil->id . "'>" . ucfirst($perfil->nombre_rol) . "</option>";
                }//fin foreach perfiles


                $body_tabla .= "</select>
                                    </td>
                               </tr>";
            }//fin foreach asambleistas

            $respuesta->body_tabla = $body_tabla;
            $respuesta->mensaje = (new Mensaje("Exito", "Asignación de nuevo perfil realizada con exito", "success"))->toArray();
            return new JsonResponse($respuesta);
        }
    }

    public function cambiar_cargos_comision()
    {
        $comisiones = Comision::where("activa", 1)->where("nombre", "!=", "junta directiva")->get();
        return view("Administracion.cambiar_cargos_comision", ["comisiones" => $comisiones]);
    }

    public function mostrar_asambleistas_comision_post(Request $request)
    {
        if ($request->ajax()) {

            $comision = Comision::find($request->get("idComision"));
            $tabla = $this->generarTabla($comision->id);
            /*
            $comision = Comision::find($request->get("idComision"));

            //obtener los integrantes de la comision y que esten activos en el periodo activo
            $integrantes = Cargo::join("asambleistas", "cargos.asambleista_id", "=", "asambleistas.id")
                ->join("periodos", "asambleistas.periodo_id", "=", "periodos.id")
                ->where("cargos.comision_id", $request->get("idComision"))
                ->where("asambleistas.activo", 1)
                ->where("periodos.activo", 1)
                ->where("cargos.activo", 1)
                ->get();

            $tabla =
                "<table class='table table-striped table-bordered table-condensed table-hover dataTable text-center'>
                    <thead>
                        <tr>
                            <th>Asambleista</th>
                            <th>Cargo</th>
                            <th>Coordinador</th>
                        </th>
                    </thead>
                    <tbody>";

            foreach ($integrantes as $integrante){
                $tabla .= "<tr>
                                <td>".$integrante->asambleista->user->persona->primer_nombre . " " . $integrante->asambleista->user->persona->segundo_nombre . " " . $integrante->asambleista->user->persona->primer_apellido . " " . $integrante->asambleista->user->persona->segundo_apellido."</td>
                                <td>".$integrante->cargo."</td>";
                if ($integrante->cargo == "Coordinador"){
                    $tabla .= "<td><div class='pretty p-icon p-curve'><input type='checkbox' checked disabled /><div class='state p-success'><i class='icon mdi mdi-check'></i><label>Coordinador de Comision</label></div></div></td>";
                }
                else{
                    $tabla .= "<td><div class='pretty p-icon p-curve'><input type='checkbox' onchange='actualizar_coordinador(".$integrante->asambleista->id.")'/><div class='state p-success'><i class='icon mdi mdi-check'></i><label></label></div></div></td>";
                }

            }

            $tabla .= "</tbody></table>";

            */
            $respuesta = new \stdClass();
            $respuesta->comision = $comision->id;
            $respuesta->tabla = $tabla;

            return new JsonResponse($respuesta);
        }
    }

    public function actualizar_coordinador(Request $request)
    {
        if ($request->ajax()) {
            $comision = $comision = Comision::find($request->get("idComision"));
            $asambleista = Asambleista::find($request->get("idAsambleista"));
            //se obtienen todos los asambleistas de la comision, con el fin de identificar el anterior coordinador
            $cargos_comision = Cargo::where("comision_id", $comision->id)->where("activo", 1)->get();

            foreach ($cargos_comision as $cargo) {
                //se verifca quien es el coordinador actual y se le quita ese cargo, para asignarselo al nuevo coordinador
                //y que no sea el asambleista nuevo
                $cargo_asambleista = $cargo->cargo;
                switch ($cargo_asambleista) {
                    case "Coordinador":
                        if ($cargo->asambleista_id != $asambleista->id) {
                            $cargo->cargo = "Asambleista";
                            $cargo->save();
                        }
                        break;
                    case "Asambleista":
                        if ($cargo->asambleista_id == $asambleista->id) {
                            $cargo->cargo = "Coordinador";
                            $cargo->save();
                        }
                        break;
                    case "Secretario":
                        if ($cargo->asambleista_id == $asambleista->id) {
                            $cargo->cargo = "Coordinador";
                            $cargo->save();
                        }
                        break;
                }
            }

            $respuesta = new \stdClass();
            $respuesta->tabla = $this->generarTabla($comision->id);
            $respuesta->mensaje = (new Mensaje("Exito", "Asignación de nuevo coordinador realizada con exito", "success"))->toArray();
            return new JsonResponse($respuesta);
        }
    }

    public function actualizar_secretario(Request $request)
    {
        if ($request->ajax()) {
            $comision = $comision = Comision::find($request->get("idComision"));
            $asambleista = Asambleista::find($request->get("idAsambleista"));
            //se obtienen todos los asambleistas de la comision, con el fin de identificar el anterior coordinador
            $cargos_comision = Cargo::where("comision_id", $comision->id)->where("activo", 1)->get();

            foreach ($cargos_comision as $cargo) {
                //se verifca quien es el coordinador actual y se le quita ese cargo, para asignarselo al nuevo coordinador
                //y que no sea el asambleista nuevo
                $cargo_asambleista = $cargo->cargo;
                switch ($cargo_asambleista) {
                    //si hay un anterior secretario, se le quita ese cargo
                    case "Secretario":
                        if ($cargo->asambleista_id != $asambleista->id) {
                            $cargo->cargo = "Asambleista";
                            $cargo->save();
                        }
                        break;
                    case "Asambleista":
                        if ($cargo->asambleista_id == $asambleista->id) {
                            $cargo->cargo = "Secretario";
                            $cargo->save();
                        }
                        break;
                    case "Coordinador":
                        if ($cargo->asambleista_id == $asambleista->id) {
                            $cargo->cargo = "Secretario";
                            $cargo->save();
                        }
                        break;
                }
            }

            $respuesta = new \stdClass();
            $respuesta->tabla = $this->generarTabla($comision->id);
            $respuesta->mensaje = (new Mensaje("Exito", "Asignación de nuevo secretario realizada con exito", "success"))->toArray();
            return new JsonResponse($respuesta);
        }
    }

    public function cambiar_cargos_junta_directiva()
    {
        $miembros_jd = Cargo::join("asambleistas", "cargos.asambleista_id", "=", "asambleistas.id")
            ->join("periodos", "asambleistas.periodo_id", "=", "periodos.id")
            ->where("cargos.comision_id", 1)
            ->where("asambleistas.activo", 1)
            ->where("periodos.activo", 1)
            ->where("cargos.activo", 1)
            ->get();

        return view("Administracion.cambiar_cargos_junta_directiva", ["miembros_jd" => $miembros_jd]);
    }

    public function actualizar_cargo_miembro_jd(Request $request)
    {
        $contador_vocales = 0;
        if ($request->ajax()) {
            $miembros_jd = Cargo::where("comision_id", 1)->where("activo", 1)->get();
            foreach ($miembros_jd as $miembro) {
                if ($miembro->cargo == $request->get("nuevo_cargo")) {
                    $miembro->cargo = "Sin cargo";
                    $miembro->save();
                }

                if ($miembro->asambleista->id == $request->get("idMiembroJD")) {
                    $miembro->cargo = $request->get("nuevo_cargo");
                    $miembro->save();
                }
            }

            $respuesta = new \stdClass();
            $respuesta->tabla = $this->generarTabla(1);
            $respuesta->mensaje = (new Mensaje("Exito", "Asignación de nuevo cargo " . $request->get("nuevo_cargo") . " realizada con exito", "success"))->toArray();
            return new JsonResponse($respuesta);
        }
    }

    public function gestionar_perfiles()
    {
        $perfiles = Rol::all();
        return view("Administracion.gestionar_perfiles", ["perfiles" => $perfiles]);
    }

    public function agregar_perfiles(Request $request)
    {
        if ($request->ajax()) {
            $rol = new Rol();
            $rol->nombre_rol = ucfirst($request->get("perfil"));
            $rol->save();

            $respuesta = new \stdClass();
            $respuesta->mensaje = (new Mensaje("Exito", "Perfil agregado con exito", "success"))->toArray();
            return new JsonResponse($respuesta);

        }

        //return redirect()->route("gestionar_perfiles");
    }

    public function administrar_acceso_modulos(Request $request)
    {
        $modulos_padres = Modulo::where("tiene_hijos", 1)->get();
        $modulos_hijos = Modulo::all();
        $id_rol = Rol::find($request->get("id_rol"));
        $modulosArrayTemporal = $id_rol->modulos->toArray();
        $modulosArray = array();
        foreach ($modulosArrayTemporal as $mat) {
            array_push($modulosArray, $mat["pivot"]["modulo_id"]);
        }
        return view("Administracion.administrar_acceso_modulos", ["modulos_padres" => $modulos_padres, "modulos_hijos" => $modulos_hijos, "id_rol" => $id_rol, "modulosArray" => $modulosArray]);
    }

    public function asignar_acceso_modulos(Request $request)
    {
        $id_rol = $request->get("id_rol");
        $modulos = $request->get("modulos");
        $rol = Rol::find($id_rol);

        //obtener los modulos que se encuentran en la tabla modulo_rol
        $modulos_actuales = (Rol::find($id_rol))->modulos()->get();
        //se remueven todos los modulos que tiene asociado el rol
        foreach ($modulos_actuales as $modulo) {
            $modulo->roles()->detach($rol->id);
        }

        //para salvar en la relacion ManyToMany de rol y modulo
        $arrayTemporal = array();
        $ModuloPadreTienePadre = false;
        array_push($arrayTemporal, 1);
        foreach ($modulos as $modulo) {
            $mod = Modulo::find($modulo);

            $mp = Modulo::find($mod->modulo_padre);
            if ($mp->modulo_padre != "") {
                $mp2 = Modulo::find($mp->modulo_padre);
                $ModuloPadreTienePadre = true;
            }

            switch ($ModuloPadreTienePadre) {
                case false:
                    if (!in_array($mp->id, $arrayTemporal)) {
                        array_push($arrayTemporal, $mp->id);
                    }
                    break;
                case true:
                    if (!in_array($mp->id, $arrayTemporal) && !in_array($mp2->id, $arrayTemporal)) {
                        array_push($arrayTemporal, $mp->id);
                        array_push($arrayTemporal, $mp2->id);
                    }
                    break;
            }
            $ModuloPadreTienePadre = false;
            array_push($arrayTemporal, $mod->id);
        }

        $rol->modulos()->attach($arrayTemporal);
        return redirect()->route("gestionar_perfiles");

    }

    public function agregar_plantillas(Request $request)
    {
        if ($request->ajax()) {
            if ($request->hasFile("plantillas")) {
                foreach ($request->plantillas as $plantilla) {
                    $documento = new Plantilla();
                    $documento->nombre = $plantilla->getClientOriginalName();
                }
                $respuesta = new \stdClass();
                $respuesta->mensaje = (new Mensaje("Exito", "Plantillas agregadas con exito", "success"))->toArray();
                return new JsonResponse($respuesta);
            }
        }
    }

    public function registro_permisos_temporales(){
        $periodo_activo = Periodo::where('activo','=', 1)->first();
        $asambleistas = Asambleista::where('activo','=', 1)
            ->where('periodo_id','=',$periodo_activo->id)
            ->get();
        $permisos = Permiso::all();

        return view("Administracion.registro_permisos_temporales",['asambleistas'=>$asambleistas,'permisos'=>$permisos]);
    }

    public function mostrar_delegados(Request $request){
        if ($request->ajax()){
            $respuesta = new \stdClass();
            $asambleista = Asambleista::find($request->id);

            if ($asambleista->propietario == 1){
                $suplentes = Asambleista::where("sector_id",$asambleista->sector_id)->where("activo",1)->where("propietario",0)->where("facultad_id",$asambleista->facultad_id)->get();

                $dropdown = '<option value="">-- Seleccione un delegado --</option>';
                foreach ($suplentes as $suplente){
                    $dropdown .= '<option value="'.$suplente->id .'">'.$suplente->user->persona->primer_nombre . ' ' .$suplente->user->persona->segundo_nombre . ' ' . $suplente->user->persona->primer_apellido . ' ' . $suplente->user->persona->segundo_apellido.'</option>';
                }
                $respuesta->dropdown = $dropdown;
            }else{
                $respuesta->esPropietario = 1;
            }
            return new JsonResponse($respuesta);
        }
    }

    public function guardar_permiso(Request $request){
        if ($request->ajax()){
            $permiso = new Permiso();
            $permiso->asambleista_id = $request->get("asambleista");

            if ($request->delegado != "")
                $permiso->delegado_id = $request->get("delegado");

            $permiso->fecha_permiso = Carbon::now();
            $permiso->motivo = $request->motivo;
            $permiso->inicio = Carbon::createFromFormat("d-m-Y", $request->startDate);
            $permiso->fin = Carbon::createFromFormat("d-m-Y", $request->endDate);
            $permiso->save();

            $respuesta = new \stdClass();
            $respuesta->mensaje = (new Mensaje("Exito","Permiso Temporal registrado con exito","success"))->toArray();
            return new JsonResponse($respuesta);
        }
    }

    private function generarTabla($idComision)
    {
        $comision = Comision::find($idComision);

        //obtener los integrantes de la comision y que esten activos en el periodo activo
        $integrantes = Cargo::join("asambleistas", "cargos.asambleista_id", "=", "asambleistas.id")
            ->join("periodos", "asambleistas.periodo_id", "=", "periodos.id")
            ->where("cargos.comision_id", $idComision)
            ->where("asambleistas.activo", 1)
            ->where("periodos.activo", 1)
            ->where("cargos.activo", 1)
            ->get();

        //si el id que se recibe no es el que pertenece a JD
        if ($idComision != 1) {
            $tabla =
                "<table id='tabla_miembros' class='table table-striped table-bordered table-condensed table-hover dataTable text-center'>
                    <thead>
                        <tr>
                            <th>Asambleista</th>
                            <th>Cargo</th>
                            <th>Coordinador</th>
                            <th>Secretario</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($integrantes as $integrante) {
                $tabla .= "<tr>
                                <td>" . $integrante->asambleista->user->persona->primer_nombre . " " . $integrante->asambleista->user->persona->segundo_nombre . " " . $integrante->asambleista->user->persona->primer_apellido . " " . $integrante->asambleista->user->persona->segundo_apellido . "</td>
                                <td>" . $integrante->cargo . "</td>";

                if ($integrante->cargo == "Coordinador") {
                    $tabla .= "<td>
                                <div class='pretty p-icon p-curve'>
                                    <input type='checkbox' checked disabled />
                                    <div class='state p-success'><i class='icon mdi mdi-check'></i><label>Coordinador de Comision</label></div>
                                </div>
                          </td>";
                } else {
                    $tabla .= "<td>
                                <div class='pretty p-icon p-curve'>
                                    <input type='checkbox' onchange='actualizar_coordinador(" . $integrante->asambleista->id . ")'/>
                                    <div class='state p-success'><i class='icon mdi mdi-check'></i><label></label></div></div>
                           </td>";
                }

                if ($integrante->cargo == "Secretario") {
                    $tabla .= "<td>
                                <div class='pretty p-icon p-curve'>
                                    <input type='checkbox' checked disabled />
                                    <div class='state p-success'><i class='icon mdi mdi-check'></i><label>Secretario de Comision</label></div>
                                </div>
                          </td>";
                } else {
                    $tabla .= "<td>
                                <div class='pretty p-icon p-curve'>
                                    <input type='checkbox' onchange='actualizar_secretario(" . $integrante->asambleista->id . ")'/>
                                    <div class='state p-success'><i class='icon mdi mdi-check'></i><label></label></div></div>
                           </td>";
                }

            }

            $tabla .= "</tr></tbody></table>";
        } else { //si es JD
            $tabla =
                "<table id='tabla_miembros_jd' class='table table-striped table-bordered table-condensed table-hover dataTable text-center'>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cargo Actual</th>
                            <th>Nuevo Cargo</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($integrantes as $integrante) {
                $tabla .= "<tr>
                                <td>" . $integrante->asambleista->user->persona->primer_nombre . " " . $integrante->asambleista->user->persona->segundo_nombre . " " . $integrante->asambleista->user->persona->primer_apellido . " " . $integrante->asambleista->user->persona->segundo_apellido . "</td>
                                <td>" . $integrante->cargo . "</td>
                                <td>
                                    <select id='cargos_jd' name='cargos_jd' class='form-control' onchange='cambiar_cargo(" . $integrante->asambleista->id . ",this.value)'>
                                        <option>-- Seleccione un cargo --</option>
                                        <option value='Presidente'>Presidente</option>
                                        <option value='Vicepresidente'>Vicepresidente</option>
                                        <option value='Secretario'>Secretario</option>
                                        <option value='Vocal'>Vocal</option>
                                    </select>
                                </td>
                            </tr>";
            }

            $tabla .= "</tbody></table>";
        }

        return $tabla;
    }

    public function guardarPlantilla($doc, $plantilla_id, $destino)
    {
        $archivo = $doc;
        $vieja_plantilla = Plantilla::where('id', '=', $plantilla_id)->first();
        $vieja_plantilla->nombre = $archivo->getClientOriginalName();

        //$plantilla = new Plantilla();
        //$plantilla->nombre = $archivo->getClientOriginalName();
        $ruta = MD5(microtime()) . "." . $archivo->getClientOriginalExtension();
        while (Plantilla::where('path', '=', $ruta)->first()) {
            $ruta = MD5(microtime()) . "." . $archivo->getClientOriginalExtension();
        }
        //dd($ruta);
        $r1 = Storage::disk($destino)->put($ruta, \File::get($archivo));
        $vieja_plantilla->path = $ruta;
        $vieja_plantilla->save();

        return $vieja_plantilla;
    }

}
