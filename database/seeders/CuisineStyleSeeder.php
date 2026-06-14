<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CuisineStyle;
use Illuminate\Database\Seeder;

class CuisineStyleSeeder extends Seeder
{
    public function run(): void
    {
        $styles = [
            [
                'key' => 'japanese',
                'name' => 'Japanese',
                'plating_keywords' => [
                    'wabi-sabi minimalism', 'negative space',
                    'asymmetric arrangement', 'natural imperfection',
                ],
                'props' => [
                    'dark slate plate', 'bamboo mat', 'chopsticks',
                    'small ceramic soy dish', 'bamboo leaf',
                ],
                'color_mood' => [
                    'muted earth tones', 'deep black',
                    'natural wood', 'moss green accent',
                ],
                'lighting' => 'soft diffused, moody, slight shadow play',
            ],
            [
                'key' => 'western_cafe',
                'name' => 'Western Cafe',
                'plating_keywords' => [
                    'clean minimalist', 'symmetrical',
                    'white ceramic focus', 'negative space used well',
                ],
                'props' => [
                    'white ceramic cup', 'marble table',
                    'linen napkin', 'wooden board', 'small vase',
                ],
                'color_mood' => [
                    'bright white', 'warm cream', 'natural wood',
                    'muted pastels',
                ],
                'lighting' => 'bright natural daylight, airy and fresh',
            ],
            [
                'key' => 'indonesian',
                'name' => 'Indonesian',
                'plating_keywords' => [
                    'generous portions', 'colorful accompaniments',
                    'traditional vessel', 'rustic authentic',
                ],
                'props' => [
                    'banana leaf', 'traditional ceramic', 'wooden ladle',
                    'batik cloth', 'kerupuk', 'sambal bowl',
                ],
                'color_mood' => [
                    'warm earthy yellow', 'deep red', 'bright green',
                    'natural brown',
                ],
                'lighting' => 'warm natural, rich and inviting',
            ],
            [
                'key' => 'fine_dining',
                'name' => 'Fine Dining',
                'plating_keywords' => [
                    'precise tweezers plating', 'sauce swoosh',
                    'microgreen accent', 'negative space dominates',
                    'height and structure',
                ],
                'props' => [
                    'white wide-rim plate', 'gold cutlery',
                    'edible flowers', 'gold leaf', 'sauce dots',
                ],
                'color_mood' => [
                    'pristine white', 'deep jewel tones',
                    'gold accent', 'dramatic contrast',
                ],
                'lighting' => 'dramatic spotlight, dark moody background',
            ],
            [
                'key' => 'korean',
                'name' => 'Korean',
                'plating_keywords' => [
                    'colorful banchan arrangement',
                    'stone bowl center', 'vibrant side dishes',
                ],
                'props' => [
                    'dolsot stone bowl', 'metal chopsticks',
                    'small banchan plates', 'gochujang red',
                ],
                'color_mood' => [
                    'vibrant red', 'sesame brown', 'bright green',
                    'golden yellow',
                ],
                'lighting' => 'warm bright, energetic and appetizing',
            ],
            [
                'key' => 'italian',
                'name' => 'Italian',
                'plating_keywords' => [
                    'rustic abundance', 'olive oil drizzle',
                    'fresh herb scatter', 'family style generous',
                ],
                'props' => [
                    'terracotta dish', 'olive oil bottle',
                    'fresh basil', 'parmesan block', 'red checkered cloth',
                ],
                'color_mood' => [
                    'tomato red', 'basil green', 'cream parmesan',
                    'olive gold',
                ],
                'lighting' => 'warm trattoria light, intimate and welcoming',
            ],
        ];

        foreach ($styles as $style) {
            CuisineStyle::updateOrCreate(['key' => $style['key']], $style);
        }
    }
}