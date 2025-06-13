<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('files', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
        $table->string('path'); // Chemin du fichier (ex. "storage/files/pharmacy_1/contract.pdf")
        $table->string('name'); // Nom du fichier (ex. "contract.pdf")
        $table->string('type'); // Type MIME (ex. "application/pdf")
        $table->unsignedBigInteger('size'); // Taille en octets
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
