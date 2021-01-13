<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pais;
use Illuminate\Support\Facades\Config;

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
            "id", "IDUsersPos", "name", "prf_descripcion", "pais_id"
        ])
        ->where("usuario", $usuario)
        ->where("std_descripcion",$status)
        ->whereRaw("PWDCOMPARE(?,password)=1",[$clave])
        ->first();

        if ($user) {
            if ($user->pais_id !== $pais_id) {
                return ["user_name"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
            }
            $token = $user->createToken('token')->accessToken;
            return [
                "user_name"=>$user->name,
                "prf_descripcion"=>$user->prf_descripcion,
                "user_id"=>$user->id,
                "IDUsersPos"=>$user->IDUsersPos,
                "token"=>$token,
                "grant"=>true
            ];
        } else {
            $conexion = $this->get_connection_name($pais_id);
            if ($conexion == "") {
                return ["user_name"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
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
                return ["user_name"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
            } else {
                $email = "";
                $new_user_added = $this->insert_user($user_to_add->name, $email, $clave, $status, $user_to_add->profile, $pais_id, $usuario, $user_to_add->IDUsersPos);
                if ($new_user_added) {
                    $token = $new_user_added->createToken('token')->accessToken;
                    return [
                        "user_name"=>$new_user_added->name,
                        "prf_descripcion"=>$new_user_added->prf_descripcion,
                        "user_id"=>$new_user_added->id,
                        "IDUsersPos"=>$new_user_added->IDUsersPos,
                        "token"=>$token,
                        "grant"=>true
                    ];
                } else {
                    return ["user_name"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
                }

            }
        }

        return ["user_name"=>"", "prf_descripcion"=>"", "user_id"=>"", "IDUsersPos"=>"", "token"=>"", "grant"=>false];
    }

    protected function insert_user($name, $email, $password, $std_descripcion, $prf_descripcion, $pais_id, $usuario, $IDUsersPos) {
        $sql_query = "EXECUTE New_User '$name', '$email', '$password', '$std_descripcion', '$prf_descripcion', $pais_id, '$usuario', '$IDUsersPos';";
        DB::raw($sql_query);
        $new_user = User::select([
            "id", "IDUsersPos", "name", "prf_descripcion", "pais_id"
        ])
        ->where("usuario", $usuario)
        ->where("std_descripcion", $std_descripcion)
        ->whereRaw("PWDCOMPARE(?,password)=1",[$password])
        ->first();
        return $new_user;
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
