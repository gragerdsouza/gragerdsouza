<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

/**
 * Seeds a curated set of the owner's LinkedIn certifications. Skipped once any exist.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void Nothing is returned.
     */
    public function run(): void
    {
        if (Certification::query()->exists()) {
            return;
        }

        // Curated: the strongest, current certifications. The full list lives on LinkedIn.
        $certifications = [
            ['Amazon Web Services Cloud Practitioner', 'Amazon Web Services (AWS)', '2025-02-01', null],
            ['Scrum Master Certification: Scrum Methodologies', 'LearnQuest', '2025-01-01', null],
            ['Fundamentals of Agile Project Management', 'Project Management Institute', '2025-01-01', null],
        ];

        foreach ($certifications as [$name, $issuer, $issuedAt, $expiresAt]) {
            Certification::query()->create([
                'name' => $name,
                'issuer' => $issuer,
                'issued_at' => $issuedAt,
                'expires_at' => $expiresAt,
            ]);
        }
    }
}
