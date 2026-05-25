<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Программирование',
                'slug' => 'programming',
                'description' => 'Курсы по программированию на различных языках и технологиях',
                'icon' => 'fas fa-code',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Веб-дизайн',
                'slug' => 'web-design',
                'description' => 'Курсы по дизайну интерфейсов и пользовательскому опыту',
                'icon' => 'fas fa-palette',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Маркетинг',
                'slug' => 'marketing',
                'description' => 'Курсы по цифровому маркетингу и продвижению',
                'icon' => 'fas fa-bullhorn',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Бизнес',
                'slug' => 'business',
                'description' => 'Курсы по предпринимательству и управлению бизнесом',
                'icon' => 'fas fa-briefcase',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Языки',
                'slug' => 'languages',
                'description' => 'Курсы по изучению иностранных языков',
                'icon' => 'fas fa-language',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('categories')->insert($categories);
    }
}