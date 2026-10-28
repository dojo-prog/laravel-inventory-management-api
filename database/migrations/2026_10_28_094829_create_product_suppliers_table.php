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
        Schema::create('product_suppliers', function (Blueprint $table) {
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('supplier_sku')->nullable();
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->timestamps();

            // Constraints

            $table->unique(['product_id', 'supplier_id'], 'idx_product_suppliers_unique');
            $table->unique('supplier_sku', 'idx_product_suppliers_supplier_sku_unique');
        });

        // Check Constraints 

        DB::statement('
            ALTER TABLE product_suppliers
            ADD CONSTRAINT product_suppliers_unit_cost_chk 
            CHECK (unit_cost >= 0) 
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('
            ALTER TABLE product_suppliers 
            DROP CONSTRAINT product_suppliers_unit_cost_chk'
        );

        Schema::dropIfExists('product_suppliers');
    }
};
