<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateReservationTableTable extends Migration
{
    public function up(): void
    {
        Schema::create("reservation_table", function (Blueprint $table) {
            $table->foreignId("reservation_id")->constrained();
            $table->foreignId("table_id")->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("reservation_tables");
    }
}