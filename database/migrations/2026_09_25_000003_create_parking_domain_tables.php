<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'petugas', 'owner'])->default('petugas')->after('password');
        });

        Schema::create('parking_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('vehicle_type', 30);
            $table->decimal('first_hour_rate', 12, 2);
            $table->decimal('next_hour_rate', 12, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('parking_areas', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->unsignedInteger('capacity');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table): void {
            $table->id();
            $table->string('plate_number', 20)->unique();
            $table->string('owner_name');
            $table->enum('vehicle_type', ['motor', 'mobil', 'lainnya']);
            $table->string('color', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('parking_area_id')->constrained('parking_areas')->restrictOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('plate_number', 20);
            $table->enum('vehicle_type', ['motor', 'mobil', 'lainnya']);
            $table->dateTime('entry_at');
            $table->dateTime('exit_at')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['parked', 'paid'])->default('parked');
            $table->timestamps();
            $table->index(['status', 'entry_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('parking_areas');
        Schema::dropIfExists('parking_rates');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};