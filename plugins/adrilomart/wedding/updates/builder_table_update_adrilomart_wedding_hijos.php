<?php namespace Adrilomart\Wedding\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAdrilomartWeddingHijos extends Migration
{
    public function up()
    {
        Schema::table('adrilomart_wedding_hijos', function($table)
        {
            $table->integer('invitado_id');
        });
    }
    
    public function down()
    {
        Schema::table('adrilomart_wedding_hijos', function($table)
        {
            $table->dropColumn('invitado_id');
        });
    }
}
