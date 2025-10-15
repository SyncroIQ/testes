<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\OrderStatus;
use App\Constants\PaymentMethod;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('ordered_quantity');
            $table->decimal('total_price', 10, 2);
            $table->tinyInteger('status')->default(OrderStatus::STATUS_PENDING);
            $table->tinyInteger('payment_method')->default(PaymentMethod::CASH_ON_DELIVERY);
            $table->string('shipping_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
