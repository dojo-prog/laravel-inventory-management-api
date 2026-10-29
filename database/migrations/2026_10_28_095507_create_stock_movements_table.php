<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('type', ['stock-in', 'stock-out', 'adjustment']);
            $table->integer('quantity'); 
            $table->integer('previous_quantity'); 
            $table->integer('new_quantity');
            $table->text('reason');
            $table->string('reference')->nullable(); 
            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE stock_movements
            ADD CONSTRAINT stock_movements_type_chk
            CHECK (type IN (
                \'stock-in\',
                \'stock-out\',
                \'adjustment\'
            ))
        ');

        DB::statement('
            ALTER TABLE stock_movements
            ADD CONSTRAINT stock_movements_new_quantity_chk
            CHECK (new_quantity >= 0)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT stock_movements_type_chk');
        DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT stock_movements_new_quantity_chk');

        Schema::dropIfExists('stock_movements');
    }
};
