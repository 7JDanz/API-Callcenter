<?php


namespace App\Services;

use App\Models\Maxpoint\UsersPos;
use Illuminate\Support\Facades\DB;

class UsersPosService
{

    /*
     * SELECT pp.prf_descripcion,up.usr_descripcion,up.IDUsersPos,up.urs_varchar1
        FROM dbo.Users_Pos up
        INNER JOIN dbo.Perfil_Pos pp ON pp.IDPerfilPos = up.IDPerfilPos
        WHERE up.usr_usuario='xtwo'
        AND PWDCOMPARE('222222222',up.usr_clave)=1
        AND up.IDStatus='86039503-85CF-E511-80C6-000D3A3261F3'
     */

    public function validarLogin($usuario, $clave){
        // TODO: Hacer Join con la tabla de estado
        // TODO: Crear el usuario y los perfiles en las tablas de la aplicacion si no existen
        // TODO: (?) Colocar el usuario en sesión (??)  ????????
        // https://stackoverflow.com/questions/62941992/getting-user-data-with-laravel-sanctum
        // TODO: Retornar el usuario encontrado y el sus roles

       // DB::connection("sqlsrv_mxp_ecu")->enableQueryLog();
        $u = DB::connection("sqlsrv_mxp_ecu")->table("Users_Pos as up")
            ->select(["pp.prf_descripcion","up.usr_descripcion","up.IDUsersPos","up.urs_varchar1"])
            ->join("Perfil_Pos as pp","up.IDPerfilPos","=","pp.IDPerfilPos")
            ->where("up.usr_usuario",$usuario)
            ->whereRaw("PWDCOMPARE(?,up.usr_clave)=1",["$clave"])
            ->where("up.usr_usuario",$usuario)
            ->first();


       // dd(DB::connection("sqlsrv_mxp_ecu")->getQueryLog());
        return $u;
    }
}