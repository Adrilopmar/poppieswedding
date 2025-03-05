<?php namespace Adrilomart\Wedding\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAdrilomartWeddingInvitados2 extends Migration
{
    public function up()
    {
        Schema::table('adrilomart_wedding_invitados', function($table)
        {
            $table->renameColumn('hijos', 'hijosas');
        });
    }
    
    public function down()
    {
        Schema::table('adrilomart_wedding_invitados', function($table)
        {
            $table->renameColumn('hijosas', 'hijos');
        });
    }
}
