<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * MainSeeder
 *
 * Seeds minimal required data for the application to function.
 * Replace the placeholder settings and pages with client-specific content.
 *
 * Run:  php spark db:seed MainSeeder
 */
class MainSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedPages();

        echo "Database seeded successfully.\n";
        echo "  Next: run php spark db:seed AdminUserSeeder to create the first admin account.\n";
    }

    // ----------------------------------------------------------------
    // Settings
    // ----------------------------------------------------------------

    private function seedSettings(): void
    {
        $settings = [
            // ── Site identity ────────────────────────────────────────────
            'site_name'        => 'LNM Accountants',
            'tagline'          => 'Maximising Your Financial Potential',
            'email'            => 'info@lnmaccountants.co.za',       // placeholder
            'phone_mobile'     => '+27 00 000 0000',                  // placeholder
            'whatsapp_number'  => '27000000000',                      // placeholder — digits only
            'whatsapp_display' => '+27 00 000 0000',                  // placeholder
            'address_physical' => 'Secunda, Mpumalanga, South Africa',

            // ── Theme ────────────────────────────────────────────────────
            'active_theme'     => 'lnm',

            // ── Navigation ───────────────────────────────────────────────
            'nav_items'     => json_encode([
                ['label' => 'About',    'href' => '#about'],
                ['label' => 'Services', 'href' => '#services'],
                ['label' => 'Contact',  'href' => '#contact'],
            ]),
            'nav_cta_label' => 'Book a Consultation',
            'nav_cta_href'  => '#contact',
            'nav_align'     => 'right',

            // ── Accreditations ───────────────────────────────────────────
            'accreditations' => json_encode([
                'Registered Tax Practitioner',
                'Registered Business Accountant (SAICA/SAIPA)',
                'SARS Compliant',
            ]),
        ];

        foreach ($settings as $key => $value) {
            $this->db->table('settings')->upsert([
                'key'   => $key,
                'value' => $value,
            ]);
        }

        echo "  Settings seeded.\n";
    }

    // ----------------------------------------------------------------
    // Pages
    // ----------------------------------------------------------------

    private function seedPages(): void
    {
        foreach ($this->builtinPages() as $slug => $data) {
            $this->db->table('pages')->upsert([
                'slug'       => $slug,
                'data'       => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "  Page '{$slug}' upserted.\n";
        }
    }

    private function builtinPages(): array
    {
        return [

            // ── Home ──────────────────────────────────────────────────────
            'home' => [
                'seoTitle'       => 'LNM Accountants | Professional Tax & Accounting Services Secunda',
                'seoDescription' => 'Registered accountants and tax practitioners serving start-ups to large enterprises in Secunda, Mpumalanga. SAICA, SAIPA and SARS registered.',
                'eyebrow'        => 'SAICA · SAIPA · SARS Registered · Secunda, Mpumalanga',
                'title'          => 'Maximising Your Financial Potential',
                'body'           => 'Registered accountants and tax practitioners serving start-ups to large-scale enterprises across Mpumalanga and beyond.',
                'image'          => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=80',
                'content'        => [
                    'services' => [
                        [
                            'num'   => '01',
                            'title' => 'Accounting & Bookkeeping',
                            'href'  => '#contact',
                            'items' => [
                                'Day-to-day ledger management',
                                'Annual financial statements',
                                'Management accounts',
                                'Payroll processing',
                            ],
                        ],
                        [
                            'num'   => '02',
                            'title' => 'Tax Compliance',
                            'href'  => '#contact',
                            'items' => [
                                'Tax returns & restructuring',
                                'Provisional tax management',
                                'VAT submissions',
                                'Strategic compliance advice',
                            ],
                        ],
                        [
                            'num'   => '03',
                            'title' => 'Business Advisory',
                            'href'  => '#contact',
                            'items' => [
                                'Entity setup & registration',
                                'Financial health analysis',
                                'Start-up structuring',
                                'Growth strategy support',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Contact ───────────────────────────────────────────────────
            'contact' => [
                'seoTitle'       => 'Contact — LNM Accountants',
                'seoDescription' => 'Get in touch with LNM Accountants in Secunda, Mpumalanga.',
                'content'        => (object) [],
            ],

        ];
    }
}
