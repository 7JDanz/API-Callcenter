<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaPayloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura_payloads', function (Blueprint $table) {
            $table->id();
            $table->string('detalle');
            $table->string('modificadores');
            $table->string('cabecera');
            $table->string('valores');
            $table->string('status');
            $table->string('IDFactura');
            $table->string('IDRestaurante');
            $table->string('IDCadena');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factura_payloads');
    }
}
