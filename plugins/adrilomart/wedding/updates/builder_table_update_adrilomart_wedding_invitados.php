<?php namespace Adrilomart\Wedding\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAdrilomartWeddingInvitados extends Migration
{
    public function up()
    {
        Schema::table('adrilomart_wedding_invitados', function($table)
        {
            $table->text('alergias');
        });
    }
    
    public function down()
    {
        Schema::table('adrilomart_wedding_invitados', function($table)
        {
            $table->dropColumn('alergias');
        });
    }
}
