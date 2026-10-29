<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Seed the application's suppliers.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'TechSource Distribution',
                'contact_person' => 'Melanie Cruz',
                'email' => 'm.cruz@techsource.example.com',
                'phone' => '+1-415-555-0101',
                'address' => '2200 Harbor Blvd, Suite 400, San Francisco, CA 94107',
            ],
            [
                'name' => 'Global Compute Supply',
                'contact_person' => 'Daniel Reyes',
                'email' => 'd.reyes@globalcompute.example.com',
                'phone' => '+1-212-555-0102',
                'address' => '88 Fifth Avenue, New York, NY 10011',
            ],
            [
                'name' => 'ScreenCraft Ltd.',
                'contact_person' => 'Priya Nair',
                'email' => 'priya.nair@screencraft.example.com',
                'phone' => '+44-20-7555-0103',
                'address' => '14 Queen Victoria Street, London EC4N 4TJ',
            ],
            [
                'name' => 'AudioHub Wholesale',
                'contact_person' => 'Kenji Tanaka',
                'email' => 'k.tanaka@audiohub.example.com',
                'phone' => '+81-3-5555-0104',
                'address' => '3-1 Shibuya 2-chome, Shibuya-ku, Tokyo 150-0002',
            ],
            [
                'name' => 'NetLink Systems',
                'contact_person' => 'Sophia Lim',
                'email' => 's.lim@netlink.example.com',
                'phone' => '+65-6555-0105',
                'address' => '1 Fusionopolis Way, Connexis North, Singapore 138632',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
