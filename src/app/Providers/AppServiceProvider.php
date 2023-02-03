<?php

namespace App\Providers;

use App\Models\Auth\PersonalAccessToken;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        JsonResource::withoutWrapping();
        Sanctum::ignoreMigrations();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Queue::failing(static function (JobFailed $event) {
            \Log::error('ジョブが失敗しました。', [
                'exception' => (string)$event->exception,
                'connection' => $event->connectionName,
                'job' => [
                    'id' => $event->job->getJobId(),
                    'name' => $event->job->getName(),
                    'queue' => $event->job->getQueue(),
                    'connection' => $event->job->getConnectionName(),
                    'raw_body' => $event->job->getRawBody(),
                ],
            ]);
        });
    }
}
