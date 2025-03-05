<?php namespace Adrilomart\Wedding\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAdrilomartWeddingHijos extends Migration
{
    public function up()
    {
        Schema::create('adrilomart_wedding_hijos', function($table)
        {
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('nombre');
            $table->string('apellidos');
            $table->text('alergias');
            $table->boolean('autocar')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('adrilomart_wedding_hijos');
    }
}
