<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('user_id');

                // Indexes for workspace queries
                $table->index('workspace_id');
                $table->index(['workspace_id', 'status']); // For filtering workspace tickets by status
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'workspace_id')) {
                $table->dropIndex(['workspace_id', 'status']);
                $table->dropIndex(['workspace_id']);
                $table->dropColumn('workspace_id');
            }
        });
    }
};
