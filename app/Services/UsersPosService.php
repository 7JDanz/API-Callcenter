<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pais;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class UsersPosService
{
    public function validarLogin($usuario, $clave) {
        $pais_prefix = Config::get("PAIS_RUTA_PETICION");
        $pais = DB::table("paises")->select([
            "id"
        ])
        ->where("prefijo_pais",$pais_prefix)
        ->first();
        $pais_id = $pais->id;
        $status = "Activo";
        $user = User::select([
            "id", "IDUsersPos", "name", "prf_descripcion", "pais_id", "password"
        ])
        ->where("usuario", $usuario)
        ->where("std_descripcion",$status)
        ->first();
        if ($user) {
            if ($user->pais_id !== $pais_id || Crypt::decryptString($user->password) !== $clave) {
                return ["user_name"=>"", "cadenas"=>"", "tipo_atencion"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
            }
            $token = $user->createToken('token')->accessToken;
            return [
                "user_name"=>$user->name,
                "cadenas"=>$user->cadenas,
                "tipo_atencion"=>$user->tipo_atencion,
                "prf_descripcion"=>$user->prf_descripcion,
                "user_id"=>$user->id,
                "IDUsersPos"=>$user->IDUsersPos,
                "token"=>$token,
                "grant"=>true
            ];
        } else {
            $conexion = $this->get_connection_name($pais_id);
            if ($conexion == "") {
                return ["user_name"=>"", "cadenas"=>"", "tipo_atencion"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
            }
            $user_to_add = DB::connection($conexion)->table("Users_Pos")
            ->select([
                "Users_Pos.usr_descripcion as name",
                "Perfil_Pos.prf_descripcion as profile",
                "Users_Pos.IDUsersPos"
            ])
            ->join("Perfil_Pos ","Users_Pos.IDPerfilPos","=","Perfil_Pos.IDPerfilPos")
            ->join("Status ","Status.IDStatus","=","Users_Pos.IDStatus")
            ->where("Users_Pos.usr_usuario",$usuario)
            ->where("Status.std_descripcion",$status)
            ->whereRaw("PWDCOMPARE(?,Users_Pos.usr_clave)=1",[$clave])
            ->first();
            if (!$user_to_add) {
                return ["user_name"=>"", "cadenas"=>"", "tipo_atencion"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
            } else {
                $email = "";
                $sql_query = "SELECT * FROM [config].[fn_buscaCadenasyTipo]('".$user_to_add->IDUsersPos."')";
                $user_data_atencion = DB::connection($conexion)->select($sql_query);
                $cadenas = $user_data_atencion[0]->cadenas;
                $tipo_atencion = $user_data_atencion[0]->tipo_atencion;
                $new_user_added = $this->insert_user($user_to_add->name, $email, $clave, $status, $user_to_add->profile, $pais_id, $usuario, $user_to_add->IDUsersPos, $cadenas, $tipo_atencion);
                if ($new_user_added) {
                    $token = $new_user_added->createToken('token')->accessToken;
                    return [
                        "user_name"=>$new_user_added->name,
                        "cadenas"=>$new_user_added->cadenas,
                        "tipo_atencion"=>$new_user_added->tipo_atencion,
                        "prf_descripcion"=>$new_user_added->prf_descripcion,
                        "user_id"=>$new_user_added->id,
                        "IDUsersPos"=>$new_user_added->IDUsersPos,
                        "token"=>$token,
                        "grant"=>true
                    ];
                } else {
                    return ["user_name"=>"", "cadenas"=>"", "tipo_atencion"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
                }

            }
        }

        return ["user_name"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
    }

    protected function insert_user($name, $email, $password, $std_descripcion, $prf_descripcion, $pais_id, $usuario, $IDUsersPos, $cadenas, $tipo_atencion) {
        $new_user_to_add = new User();
        $new_user_to_add->password = Crypt::encryptString($password);
        $new_user_to_add->name = $name;
        $new_user_to_add->cadenas = $cadenas;
        $new_user_to_add->tipo_atencion = $tipo_atencion;
        $new_user_to_add->email = $email;
        $new_user_to_add->std_descripcion = $std_descripcion;
        $new_user_to_add->prf_descripcion = $prf_descripcion;
        $new_user_to_add->pais_id = $pais_id;
        $new_user_to_add->usuario = $usuario;
        $new_user_to_add->IDUsersPos = $IDUsersPos;
        $new_user_to_add->save();
        return $new_user_to_add;
    }

    public function get_connection_name($pais_id) {
        $conexion = DB::table("conexiones")->select("conexiones.nombre")
        ->join('paises', 'paises.prefijo_pais', '=', 'conexiones.prefijo_pais')
        ->where("conexiones.nombre", "like", "sqlsrv_mxp_%")
        ->where("paises.id",$pais_id)->first();
        if ($conexion) {
            return $conexion->nombre;
        } else {
            return "";
        }
    }

    public function update_users_batch() {
        $users = User::get();
        foreach($users as $user) {
            $conexion = $this->get_connection_name($user->pais_id);
            if ($conexion == "") {
                DB::beginTransaction();
                $user->update([
                    "std_descripcion"=>"Inactivo"
                ]);
                DB::commit();
            } else {
                $user_from_update = DB::connection($conexion)->table("Users_Pos")
                ->select([
                    "Users_Pos.usr_descripcion as name",
                    "Perfil_Pos.prf_descripcion as profile",
                    "Status.std_descripcion as status"
                ])
                ->join("Perfil_Pos ","Users_Pos.IDPerfilPos","=","Perfil_Pos.IDPerfilPos")
                ->join("Status ","Status.IDStatus","=","Users_Pos.IDStatus")
                ->where("Users_Pos.IDUsersPos",$user->IDUsersPos)
                ->first();

                DB::beginTransaction();
                if ($user_from_update) {
                    $user->update([
                        "prf_descripcion"=>$user_from_update->profile,
                        "std_descripcion"=>$user_from_update->status
                    ]);
                } else {
                    $user->update([
                        "std_descripcion"=>"Inactivo"
                    ]);
                }
                DB::commit();
            }
        }
    }
}
