<?php namespace Adrilomart\Wedding\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAdrilomartWeddingInvitados extends Migration
{
    public function up()
    {
        Schema::create('adrilomart_wedding_invitados', function($table)
        {
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('telf');
            $table->boolean('autocar')->default(0);
            $table->boolean('hijos')->default(0);
            $table->boolean('pre_boda')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('adrilomart_wedding_invitados');
    }
}
