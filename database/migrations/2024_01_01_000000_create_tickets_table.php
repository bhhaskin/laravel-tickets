<?php

use Bhhaskin\Tickets\Support\TicketsUserResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignIdFor(TicketsUserResolver::resolveModel())
                ->constrained()
                ->cascadeOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->string('status', 20)->default('new');
            $table->string('priority', 20)->default('normal');
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_replied_at')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index('user_id'); // Already indexed by foreign key, but explicit for clarity
            $table->index('created_at'); // For sorting by creation date
            $table->index(['status', 'priority']); // For filtering by status and priority
            $table->index(['status', 'created_at']); // For filtering open tickets by date
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
