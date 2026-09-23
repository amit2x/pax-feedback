<?php

namespace Database\Seeders;

use App\Models\FeedbackQrCode;
use App\Services\QrTokenService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenericQrCodeSeeder extends Seeder
{
    public function run(QrTokenService $tokens): void
    {
        if (FeedbackQrCode::where('type', 'generic')->exists()) {
            return;
        }

        $generated = $tokens->generate();

        FeedbackQrCode::create([
            'uuid' => (string) Str::uuid(),
            'token_hash' => $generated['hash'],
            'token_prefix' => $generated['prefix'],
            'type' => 'generic',
            'name' => 'Generic Airport Feedback',
            'description' => 'Displayed throughout the airport.',
            'status' => 'active',
        ]);

        // Print the plain token to stdout for initial provisioning
        $this->command->info('Generic QR token: '.$generated['plain']);
        $this->command->info('URL: '.url('/feedback/f/'.$generated['plain']));
    }
}
