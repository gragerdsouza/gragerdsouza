<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds the admin user and starter portfolio content.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void Nothing is returned.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PortfolioSeeder::class,
        ]);
    }
}
