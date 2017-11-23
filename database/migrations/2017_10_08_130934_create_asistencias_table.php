<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsistenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('agenda_id');
            $table->unsignedInteger('asambleista_id');
            $table->unsignedInteger('estado_asistencia_id');

            $table->Time('entrada')->nullable();
            $table->Time('salida')->nullable();
            $table->boolean('propietario')->nullable();

            $table->index(["asambleista_id"], 'fk_asistencias_asambleistas1_idx');

            $table->index(["agenda_id"], 'fk_asistencias_agendas1_idx');

            $table->index(["estado_asistencia_id"], 'fk_asistencias_estado_asistencia1_idx');


            $table->foreign('agenda_id', 'fk_asistencias_agendas1_idx')
                ->references('id')->on('agendas')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('asambleista_id', 'fk_asistencias_asambleistas1_idx')
                ->references('id')->on('asambleistas')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('estado_asistencia_id', 'fk_asistencias_estado_asistencia1_idx')
                ->references('id')->on('estado_asistencias')
                ->onDelete('no action')
                ->onUpdate('no action');

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
        Schema::drop('asistencias');
    }
}
