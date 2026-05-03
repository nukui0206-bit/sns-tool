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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 30);
            $table->string('account_name');
            $table->string('external_account_id')->nullable();
            // access_token / refresh_token は Eloquent の 'encrypted' cast で暗号化される。
            // 暗号文は元データより長いため text 型で保存する。
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status', 20)->default('disconnected');
            $table->text('memo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'platform']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
