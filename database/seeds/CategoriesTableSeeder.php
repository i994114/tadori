<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $categories = [
            ['category_name' => 'プログラミング',   'created_at' => $now, 'updated_at' => $now],
            ['category_name' => '資格試験',         'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'ビジネススキル',   'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'デザイン',         'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'ライフスタイル',   'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'キャリア形成',     'created_at' => $now, 'updated_at' => $now],
            ['category_name' => '自己啓発',         'created_at' => $now, 'updated_at' => $now],
            ['category_name' => '語学',             'created_at' => $now, 'updated_at' => $now],
        ];

        
 

        DB::table('categories')->insert($categories);
    }
}
