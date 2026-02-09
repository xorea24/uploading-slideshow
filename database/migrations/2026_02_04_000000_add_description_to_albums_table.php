<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Photo;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('albums') && ! Schema::hasColumn('albums', 'description')) {
            Schema::table('albums', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('albums') && Schema::hasColumn('albums', 'description')) {
            Schema::table('albums', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
