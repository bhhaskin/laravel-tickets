<?php

use Bhhaskin\Tickets\Models\Ticket;
use Bhhaskin\Tickets\Support\TicketsUserResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ticket::class)
                ->constrained('tickets')
                ->cascadeOnDelete();
            $table->foreignIdFor(TicketsUserResolver::resolveModel())
                ->constrained()
                ->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
    }
};
