<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Payment\SubscriptionPlanOccurrence;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plan_occurrences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        \DB::table('subscription_plan_occurrences')->insert([
            ['id'=> SubscriptionPlanOccurrence::Weekly, 'name' => 'Weekly'],
            ['id'=> SubscriptionPlanOccurrence::Monthly, 'name' => 'Monthly'],
            ['id'=> SubscriptionPlanOccurrence::Quarterly, 'name' => 'Quarterly'],
            ['id'=> SubscriptionPlanOccurrence::HalfYearly, 'name' => 'Half Yearly'],
            ['id'=> SubscriptionPlanOccurrence::Yearly, 'name' => 'Yearly'],
            ['id'=> SubscriptionPlanOccurrence::None, 'name' => 'None'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plan_occurrences');
    }
};
