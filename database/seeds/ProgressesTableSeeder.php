<?php

use App\Step;
use App\Challenge;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProgressesTableSeeder extends Seeder
{
    public function run()
    {
        if (config('app.env') === 'production') {
            $challenges = DB::table('challenges')->get();
            
            foreach ($challenges as $challenge) {
                // challengeに紐づくstepを取得（1件）
                $step = DB::table('steps')->where('id', $challenge->step_id)->first();
                if (!$step) continue;

                // stepに紐づくsub_stepsを取得
                $subSteps = DB::table('sub_steps')->where('step_id', $step->id)->get();

                foreach ($subSteps as $subStep) {
                    // ランダムに進捗を作るかどうかを決める
                    if (rand(0, 1)) { // 50%の確率
                        DB::table('progresses')->insert([
                            'challenge_id' => $challenge->id,
                            'sub_step_id'  => $subStep->id,
                            'created_at'   => Carbon::now(),
                            'updated_at'   => Carbon::now(),
                        ]);
                    }
                }
            }
        } else {
            // 全チャレンジ取得
            $challenges = Challenge::all();

            foreach ($challenges as $challenge) {
                $step = Step::find($challenge->step_id);

                if (!$step) {
                    continue; // STEPが存在しない場合はスキップ
                }

                $subSteps = $step->subSteps;

                if ($subSteps->isEmpty()) {
                    continue; // 子STEPがない場合はスキップ
                }

                // ランダムで進捗数決定
                $clearCount = rand(0, $subSteps->count());

                $clearedSubSteps = $subSteps->shuffle()->take($clearCount);

                foreach ($clearedSubSteps as $subStep) {
                    DB::table('progresses')->insert([
                        'challenge_id' => $challenge->id,
                        'sub_step_id'  => $subStep->id,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }

    }
}
