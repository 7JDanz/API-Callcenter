<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entities\Kiosko\KioskoModel
 *
 * @mixin \Eloquent
 */
class AppModel extends Model
{
    public function getDateFormat()
    {
        return env("FORMATO_FECHAS","Y-d-m H:i:s.u");
    }

    public function fromDateTime($value)
    {
        return substr(parent::fromDateTime($value), 0, -3);
    }

    public function connection(){
        //sqlsrv_mxp_ecu    sqlsrv_mxp_col   sqlsrv_mxp_chi
        $this->connection="sqlsrv_mxp_".session("pais");

    }
}
