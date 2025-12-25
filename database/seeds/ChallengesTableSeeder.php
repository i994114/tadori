<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class ChallengesTableSeeder extends Seeder
{

    public function run()
    {
        if (config('app.env') === 'production') {
            $steps = DB::table('steps')->select('id', 'owner_id')->get();
            $users = range(1, 8);
            $insertData = [];
            $usedCombinations = [];

            // 各ユーザが最低6個チャレンジを持つように準備
            $userChallenges = array_fill_keys($users, 0);

            foreach ($steps as $step) {
                // オーナーを除外したユーザーリストを作成
                $availableUsers = array_diff($users, [$step->owner_id]);

                // このステップをチャレンジするユーザーをランダムに数人選ぶ
                $selectedUsers = Arr::random($availableUsers, rand(1, 3));

                foreach ((array)$selectedUsers as $userId) {
                    $key = $step->id . '-' . $userId;
                    if (!isset($usedCombinations[$key])) {
                        $usedCombinations[$key] = true;
                        $insertData[] = [
                            'step_id'    => $step->id,
                            'user_id'    => $userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $userChallenges[$userId]++;
                    }
                }
            }

            // どのユーザーも最低6件は登録されているかチェックし、不足分を補う
            foreach ($userChallenges as $userId => $count) {
                if ($count < 6) {
                    $needed = 6 - $count;

                    // オーナーではないステップをランダム選択
                    $candidateSteps = $steps->filter(fn($s) => $s->owner_id !== $userId)->pluck('id')->all();
                    $availableSteps = array_filter($candidateSteps, function ($sid) use ($usedCombinations, $userId) {
                        return !isset($usedCombinations["$sid-$userId"]);
                    });

                    $extraSteps = Arr::random($availableSteps, min($needed, count($availableSteps)));

                    foreach ((array)$extraSteps as $sid) {
                        $insertData[] = [
                            'step_id'    => $sid,
                            'user_id'    => $userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $usedCombinations["$sid-$userId"] = true;
                    }
                }
            }

            DB::table('challenges')->insert($insertData);
        } else {
            $users = [4, 5, 6, 7];
            $numSteps = 50;
            $minChallenges = 14;

            $data = [];

            foreach ($users as $userId) {
                $assignedSteps = [];

                // STEPをシャッフルして重複なしで割り当て
                $stepPool = range(1, $numSteps);
                shuffle($stepPool);

                for ($i = 0; $i < $minChallenges; $i++) {
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

            DB::table('challenges')->insert($data);
        }
    }
}

/*
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChallengesTableSeeder extends Seeder
{
    public function run()
    {
        $users = [4, 5, 6, 7];
        $numSteps = 50;
        $minChallenges = 14;

        $data = [];

        foreach ($users as $userId) {
            $assignedSteps = [];

            // STEPをシャッフルして重複なしで割り当て
            $stepPool = range(1, $numSteps);
            shuffle($stepPool);

            for ($i = 0; $i < $minChallenges; $i++) {
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

        DB::table('challenges')->insert($data);
    }
}
*/
