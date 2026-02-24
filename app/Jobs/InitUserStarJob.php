<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Star\IStarService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class InitUserStarJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user,
        private IStarService $starService,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $amount = (int) config('const.star.init');

        if ($amount <= 0) {
            return;
        }

        try {
            $success = $this->starService->addStarByUserId(
                $amount,
                $this->user->id,
                __('Bạn được tặng sao khi đăng ký tài khoản')
            );

            if ($success) {
                Log::info('Initialized user stars on registration.', [
                    'user_id' => $this->user->id,
                    'amount' => $amount,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to initialize user stars on registration.', [
                'user_id' => $this->user->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
