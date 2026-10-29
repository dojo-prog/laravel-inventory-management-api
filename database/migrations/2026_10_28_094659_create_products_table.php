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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('sku');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit');
            $table->decimal('unit_cost', 8, 2);
            $table->integer('stock_quantity');
            $table->integer('reorder_level');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();

            // Indexes

            $table->index('sku', 'idx_products_sku');
            $table->index('category_id', 'idx_products_category');
            $table->index('name', 'idx_products_name');

            // Constraints

            $table->unique('sku', 'products_sku_unique');
            $table->unique(['name', 'category_id'], 'products_name_category_unique');
        });

        // Check Constraints

        DB::statement('
            ALTER TABLE products 
            ADD CONSTRAINT products_unit_cost_chk
            CHECK (unit_cost >= 0)
        ');

        DB::statement('
            ALTER TABLE products 
            ADD CONSTRAINT products_stock_quantity_chk
            CHECK (stock_quantity >= 0)
        ');

        DB::statement('
            ALTER TABLE products 
            ADD CONSTRAINT products_reorder_level_chk
            CHECK (reorder_level >= 0)
        ');

        DB::statement('
            ALTER TABLE products 
            ADD CONSTRAINT products_status_chk
            CHECK (status IN  (
                \'active\',
                \'inactive\'       
            ))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_unit_cost_chk');
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_stock_quantity_chk');
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_reorder_level_chk');
        DB::statement('ALTER TABLE products DROP CONSTRAINT products_status_chk');

        Schema::dropIfExists('products');
    }
};
