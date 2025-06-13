<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index pour la table users
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'email']);
            $table->index('last_name');
        });

        // Index pour la table pharmacies
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->index(['status', 'city']);
            $table->index('name');
        });

        // Index pour la table orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['created_at', 'status']);
            $table->index('total');
        });

        // Index pour la table documents
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['type', 'created_at']);
            $table->index('title');
        });
    }

    public function down(): void
    {
        // Supprimer les index de la table users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'email']);
            $table->dropIndex(['last_name']);
        });

        // Supprimer les index de la table pharmacies
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropIndex(['status', 'city']);
            $table->dropIndex(['name']);
        });

        // Supprimer les index de la table orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at', 'status']);
            $table->dropIndex(['total']);
        });

        // Supprimer les index de la table documents
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['type', 'created_at']);
            $table->dropIndex(['title']);
        });
    }
}; 