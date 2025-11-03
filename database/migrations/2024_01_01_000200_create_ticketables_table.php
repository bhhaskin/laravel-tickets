<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticketables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->morphs('ticketable');
            $table->timestamps();

            $table->unique(['ticket_id', 'ticketable_type', 'ticketable_id'], 'ticketables_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticketables');
    }
};
