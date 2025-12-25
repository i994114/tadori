<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FavoritesTableSeeder extends Seeder
{
    public function run()
    {
        if (config('app.env') === 'production') {
            $users = range(1, 8);

            foreach ($users as $userId) {
                // 自分以外のステップを取得
                $availableSteps = DB::table('steps')
                    ->where('owner_id', '<>', $userId)
                    ->pluck('id')  // step_idだけのコレクション
                    ->toArray();

                // 8件ランダムに選ぶ（重複なし）
                $stepIds = Arr::random($availableSteps, min(8, count($availableSteps)));

                foreach ($stepIds as $stepId) {
                    DB::table('favorites')->insert([
                        'user_id'    => $userId,
                        'step_id'    => $stepId,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        } else {
            $users = [4, 5, 6, 7];
            $numSteps = 50;
            $minFavorites = 14;

            $data = [];

            foreach ($users as $userId) {
                // STEPプールをシャッフルして重複なしで割り当て
                $stepPool = range(1, $numSteps);
                shuffle($stepPool);

                for ($i = 0; $i < $minFavorites; $i++) {
                    $stepId = $stepPool[$i];

                    $randomDate = Carbon::now()->subDays(rand(0,30))
                                            ->setTime(rand(0,23), rand(0,59), rand(0,59));

                    $data[] = [
                        'user_id'    => $userId,
                        'step_id'    => $stepId,
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ];
                }
            }

            DB::table('favorites')->insert($data);
        }

    }
}
