<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers_services', function (Blueprint $table) {
            if (Schema::hasColumn('customers_services', 'service_category')) {
                $table->dropColumn('service_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers_services', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_services', 'service_category')) {
                $table->string('service_category')->nullable();
            }
        });
    }
};