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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('book_title');
            $table->string('book_description');
            $table->string('author_name');
            $table->string('book_edition');
            $table->string('book_publisher');
            $table->foreignId('category_id')->constrained("categories");
            $table->decimal('location_longitude', 10, 7);
            $table->decimal('location_latitude', 10 , 7);
            $table->string('location');
            $table->float('book_price');
            $table->float('book_selling_price');
            $table->json('book_images');
            $table->foreignId("user_id")->constrained("users");
            $table->tinyInteger('is_request')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
