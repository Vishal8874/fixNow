<?php

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {

            $table->id();

            // Booking Reference
            $table->string('booking_number')->unique();

            // Relationships
            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('provider_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('provider_service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('service_area_id')
                ->constrained('provider_service_areas')
                ->cascadeOnDelete();

            // Scheduled Date & Time
            $table->dateTime('scheduled_at');

            // Completion Date & Time
            $table->timestamp('completed_at')->nullable();

            // Customer Snapshot
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->string('customer_city');
            $table->string('customer_state');
            $table->string('customer_pincode', 10);

            // Service Details
            $table->text('issue_description');

            // Price Snapshot
            $table->decimal('estimated_price', 10, 2);

            // Final negotiated/completed price
            $table->decimal('final_price', 10, 2)->nullable();

            // Booking Status
            $table->string('status', 20)
                ->default(BookingStatus::PENDING->value);

            // Cancellation Reason
            $table->string('cancel_reason', 255)->nullable();

            //Rejection Reason
            $table->string('reject_reason', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_id');
            $table->index('provider_profile_id');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};