<?php namespace Adrilomart\Wedding\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAdrilomartWeddingInvitados3 extends Migration
{
    public function up()
    {
        Schema::table('adrilomart_wedding_invitados', function($table)
        {
            $table->text('email');
        });
    }
    
    public function down()
    {
        Schema::table('adrilomart_wedding_invitados', function($table)
        {
            $table->dropColumn('email');
        });
    }
}
