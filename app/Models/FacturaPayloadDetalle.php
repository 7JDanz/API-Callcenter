<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class FacturaPayloadDetalle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detalleApp',
        'codigoApp',
        'codPlu',
        'precioBruto',
        'cantidad',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function __construct()
    {
        $this->detalleApp = 'detalleApp';
        $this->codigoApp = 'codigoApp';
        $this->codPlu = 0;
        $this->precioBruto = 0;
        $this->cantidad = 0;
    }

    public function getDateFormat()
    {
        return env("FORMATO_FECHAS","Y-d-m H:i:s.v");
    }
}
