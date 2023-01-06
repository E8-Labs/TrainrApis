<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment\InvoiceStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->default('');
            $table->timestamps();
        });
        \DB::table('invoice_statuses')->insert([
            ['id'=> InvoiceStatus::StatusPending, 'name' => 'Pending Payment'],
            ['id'=> InvoiceStatus::StatusPaid, 'name' => 'Payment Made'],
            ['id'=> InvoiceStatus::StatusCancelled, 'name' => 'Cancelled'],
            ['id'=> InvoiceStatus::StatusRefunded, 'name' => 'Refunded'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_statuses');
    }
};
