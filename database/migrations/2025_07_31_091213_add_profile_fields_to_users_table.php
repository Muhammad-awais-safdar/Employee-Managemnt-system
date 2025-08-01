<?php

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
        Schema::table('users', function (Blueprint $table) {
            // Basic profile fields
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->string('profile_image')->nullable()->after('date_of_birth');
            $table->text('bio')->nullable()->after('profile_image');
            
            // Professional fields
            $table->string('employee_id', 50)->unique()->nullable()->after('bio');
            $table->date('date_of_joining')->nullable()->after('employee_id');
            $table->decimal('salary', 10, 2)->nullable()->after('date_of_joining');
            $table->string('qualification')->nullable()->after('salary');
            $table->integer('experience_years')->nullable()->after('qualification');
            $table->text('skills')->nullable()->after('experience_years');
            
            // Personal fields
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('skills');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('gender');
            
            // Emergency contact
            $table->string('emergency_contact_name')->nullable()->after('marital_status');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            
            // Social links
            $table->string('linkedin_url')->nullable()->after('emergency_contact_phone');
            $table->string('twitter_url')->nullable()->after('linkedin_url');
            
            // Settings
            $table->json('settings')->nullable()->after('twitter_url');
            
            // Indexes for performance
            $table->index('employee_id');
            $table->index('date_of_joining');
            $table->index('gender');
            $table->index('marital_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'date_of_birth',
                'profile_image',
                'bio',
                'employee_id',
                'date_of_joining',
                'salary',
                'qualification',
                'experience_years',
                'skills',
                'gender',
                'marital_status',
                'emergency_contact_name',
                'emergency_contact_phone',
                'linkedin_url',
                'twitter_url',
                'settings'
            ]);
        });
    }
};