<?php

use App\Models\Currencies;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // Currency code (e.g., 'USD', 'PHP')
            $table->string('name');  // Currency name (e.g., 'US Dollar', 'Philippine Peso')
            $table->string('symbol');  // Currency symbol (e.g., '$', '₱')
            $table->string('img_url');
            $table->timestamps();
        });
      

        Currencies::insert([
            ['code' => 'PHP', 'name' => '🇵🇭 PHP', 'symbol' => '₱','img_url'=>'', 'created_at' => now(), 'updated_at'=>now()],
            ['code' => 'USD', 'name' => '🇺🇸 USD', 'symbol' => '$','img_url'=>'', 'created_at' => now(), 'updated_at'=>now()],
            ['code' => 'EUR', 'name' => '🇪🇺 EUR', 'symbol' => '€','img_url'=>'', 'created_at' => now(), 'updated_at'=>now()],
           
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
