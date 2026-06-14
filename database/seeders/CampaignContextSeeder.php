<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CampaignContext;
use Illuminate\Database\Seeder;

class CampaignContextSeeder extends Seeder
{
    public function run(): void
    {
        $contexts = [
            [
                'key' => 'payday_promo',
                'name' => 'Payday Promo',
                'aliases' => ['gajian', 'payday', 'akhir bulan', 'tanggal 25', 'tanggal muda', 'awal bulan'],
                'mood_keywords' => ['celebratory', 'indulgent', 'treat yourself', 'premium feel', 'reward after hard work'],
                'urgency_phrases' => ['Spesial Hari Gajian', 'Rayakan dengan yang Terbaik', 'Kamu Pantas Dapat yang Terbaik', 'Hadiah untuk Dirimu Sendiri'],
                'color_emotion' => ['gold', 'deep rich tones', 'luxurious'],
                'composition_hint' => 'hero product large and central, premium and indulgent composition',
            ],
            [
                'key' => 'weekend_special',
                'name' => 'Weekend Special',
                'aliases' => ['weekend', 'sabtu minggu', 'akhir pekan', 'saturday', 'sunday', 'libur'],
                'mood_keywords' => ['relaxed', 'social', 'fun', 'casual joy', 'gathering with friends'],
                'urgency_phrases' => ['Weekend Spesial', 'Nikmati Akhir Pekanmu', 'Sabtu Seru Bareng Sahabat', 'Weekend Vibes'],
                'color_emotion' => ['bright warm', 'cheerful yellow', 'energetic'],
                'composition_hint' => 'lifestyle feel, multiple items or group setting',
            ],
            [
                'key' => 'new_menu',
                'name' => 'Menu Baru',
                'aliases' => ['menu baru', 'new arrival', 'launching', 'new item', 'just dropped', 'baru hadir'],
                'mood_keywords' => ['exciting', 'fresh', 'curiosity', 'must try', 'first look'],
                'urgency_phrases' => ['Baru Hadir!', 'Harus Kamu Coba', 'Menu Terbaru Kami', 'Yang Ditunggu-tunggu'],
                'color_emotion' => ['fresh bright', 'energetic pop', 'clean modern'],
                'composition_hint' => 'single hero item spotlight, clean background, premium reveal feel',
            ],
            [
                'key' => 'ramadan',
                'name' => 'Ramadan & Buka Puasa',
                'aliases' => ['buka puasa', 'sahur', 'iftar', 'ramadhan', 'takjil', 'buka', 'ngabuburit'],
                'mood_keywords' => ['warm', 'togetherness', 'spiritual warmth', 'family', 'generous', 'blessed'],
                'urgency_phrases' => ['Spesial Buka Puasa', 'Menu Ramadan Kami', 'Berbagi di Bulan Berkah', 'Takjil Spesial'],
                'color_emotion' => ['warm gold', 'deep teal', 'crescent mood', 'warm amber', 'rich burgundy'],
                'composition_hint' => 'warm candlelit feel, date fruits or water as prop, family gathering atmosphere',
            ],
            [
                'key' => 'grand_opening',
                'name' => 'Grand Opening',
                'aliases' => ['grand opening', 'soft opening', 'pembukaan', 'open soon', 'we are open', 'baru buka'],
                'mood_keywords' => ['exciting', 'celebratory', 'welcome', 'anticipation', 'milestone'],
                'urgency_phrases' => ['Grand Opening!', 'Kami Resmi Buka', 'Datang dan Rasakan', 'Be The First'],
                'color_emotion' => ['celebration gold', 'bold red', 'confetti bright', 'premium launch'],
                'composition_hint' => 'bold and attention-grabbing, venue or signature item as hero',
            ],
            [
                'key' => 'harbolnas',
                'name' => 'Harbolnas & Flash Sale',
                'aliases' => ['11.11', '12.12', 'harbolnas', 'flash sale', 'double date', 'mega sale', 'big sale'],
                'mood_keywords' => ['urgent', 'exciting deal', 'limited time', 'dont miss out', 'best price'],
                'urgency_phrases' => ['Flash Sale Hari Ini!', 'Harga Spesial Terbatas', 'Jangan Sampai Kehabisan', 'Best Deal of the Year'],
                'color_emotion' => ['hot red urgent', 'electric yellow', 'bold contrast', 'high energy'],
                'composition_hint' => 'price badge prominent, multiple items, high energy layout',
            ],
            [
                'key' => 'anniversary',
                'name' => 'Anniversary & Ulang Tahun',
                'aliases' => ['ulang tahun', 'anniversary', 'tahun ke-', 'birthday', 'celebration', 'milestones'],
                'mood_keywords' => ['nostalgic warmth', 'proud milestone', 'celebratory', 'grateful', 'special moment'],
                'urgency_phrases' => ['Merayakan Kebersamaan', 'Terima Kasih Telah Bersama Kami', 'Special Anniversary Edition'],
                'color_emotion' => ['warm gold', 'deep burgundy', 'rose gold', 'festive sparkle'],
                'composition_hint' => 'cake or special item center, elegant celebratory props',
            ],
            [
                'key' => 'daily_promo',
                'name' => 'Promo Harian',
                'aliases' => ['promo harian', 'daily deal', 'happy hour', 'promo hari ini', 'diskon hari ini', 'today only'],
                'mood_keywords' => ['accessible', 'great value', 'everyday treat', 'friendly deal', 'approachable'],
                'urgency_phrases' => ['Promo Hari Ini', 'Happy Hour Spesial', 'Hanya Hari Ini', 'Dapatkan Sekarang'],
                'color_emotion' => ['friendly warm orange', 'approachable yellow', 'clear bright'],
                'composition_hint' => 'clear price communication, simple clean food hero',
            ],
        ];

        foreach ($contexts as $ctx) {
            CampaignContext::updateOrCreate(['key' => $ctx['key']], $ctx);
        }
    }
}