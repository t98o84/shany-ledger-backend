<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('operator', function (string $column) {
            $this->foreignUuid($column)->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });

        Blueprint::macro('operators', function (bool $creator = true, bool $updater = true, bool $deleter = true) {
           if ($creator) {
               $this->operator('created_by');
           }
            if ($updater) {
                $this->operator('updated_by');
            }
            if ($deleter) {
                $this->operator('deleted_by');
            }
        });
    }
}
