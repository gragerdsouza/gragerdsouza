<?php

declare(strict_types=1);

/**
 * Pest tests: Arch / ArchTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

/*
| Architecture rules. These document — and enforce — the layering described in
| docs/ARCHITECTURE.md. Break one and CI goes red.
*/

arch('php preset')->preset()->php();

arch('security preset')->preset()->security();

arch('laravel preset')->preset()->laravel()->ignoring('App\Providers\Filament');

arch('every file declares strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debugging helpers left behind')
    ->expect(['dd', 'dump', 'ddd', 'ray', 'var_dump', 'die'])
    ->not->toBeUsed();

arch('env() is only read in config files')
    ->expect('env')
    ->not->toBeUsedIn('App');

arch('controllers are thin, final and dependency-free of each other')
    ->expect('App\Http\Controllers')
    ->toBeFinal()
    ->toHaveSuffix('Controller')
    ->not->toUse('App\Http\Controllers')
    ->not->toUse(DB::class);

arch('form requests extend FormRequest')
    ->expect('App\Http\Requests')
    ->toExtend(FormRequest::class)
    ->toHaveSuffix('Request')
    ->toBeFinal();

arch('actions are final, readonly, single-purpose')
    ->expect('App\Actions')
    ->toBeFinal()
    ->toBeReadonly()
    ->toHaveMethod('handle')
    ->not->toUse('App\Http');

arch('models are plain Eloquent models')
    ->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring(User::class)
    ->not->toUse(['App\Http', 'App\Filament', 'App\Actions']);

arch('domain does not depend on the admin layer')
    ->expect(['App\Models', 'App\Actions', 'App\Http', 'App\Notifications', 'App\Enums'])
    ->not->toUse('App\Filament');

arch('enums are backed enums')
    ->expect('App\Enums')
    ->toBeStringBackedEnums();

arch('notifications extend Notification')
    ->expect('App\Notifications')
    ->toExtend(Notification::class);

arch('admin layer classes are final')
    ->expect('App\Filament')
    ->classes()
    ->toBeFinal();
