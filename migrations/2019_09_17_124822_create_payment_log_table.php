<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xepay_payment_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pg', 50);
            $table->string('oid')->index();
            $table->string('tid')->index();
            $table->string('type', 100);
            $table->string('method')->nullable();
            $table->string('currency', 10);
            $table->float('amount');
            $table->boolean('success')->default(0);
            $table->text('response');
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->dateTime('created_at')->nullable();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xepay_payment_log');
    }
}
