<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') === 'production') {
            $randDate = Carbon::now()->subMonths(3)->addSecond(rand(0, 60 * 60 * 24 * 30));

            //メール認証はcreateから3日後ってことで
            $emailVerifiedAt = (clone $randDate)->addDays(3);

            $user_profiles = [
                "プログラミング初心者としてSTEPを始めました。最初は何から手を付ければいいかわからず戸惑っていましたが、他のユーザーのSTEPを参考にして、HTML→CSS→JavaScriptの順で学ぶことにしました。特に小さなプロジェクトを作りながら学ぶ方法が、自分には合っていたと感じます。今では簡単なウェブアプリを作れるまでになり、次はReactやVueにも挑戦したいと思っています。自分のSTEPを共有することで、これから学ぶ人の参考になれば嬉しいです。",
                "英語学習のSTEPを投稿しています。私は中学英語の基礎を復習した後、TOEIC対策用の単語帳を使い、その後リスニング教材で耳を慣らす、という順で学習しました。STEPを作ることで、自分の学習の振り返りにもなり、他の人からフィードバックをもらえるのが楽しいです。今後はアウトプット中心の英会話練習も加えて、STEPをアップデートしていきたいです。",
                "これまで独学でプログラミングを学んできましたが、STEPに参加して自分の学習順序を整理することにしました。Pythonの基本を押さえた後、データ分析や機械学習に挑戦するという順で学習を進めました。STEPに投稿することで、自分の理解度も確認でき、また他のユーザーのSTEPを参考に新しい学習方法を取り入れられるのが魅力です。今後はWebフレームワークを使ったアプリ開発にも挑戦予定です。",
                "STEPを使い始めてから、自分の学習の優先順位が明確になりました。プログラミング言語やツールの習得順序を悩んでいた私にとって、他のユーザーの具体的なSTEPは大変参考になりました。特にフロントエンド開発のSTEPは、自分が挫折しやすい箇所を事前に知ることができ、効率的に学習を進めることができています。私も自分の経験をシェアして、少しでも誰かの助けになれればと思います。",
                "学生時代から英語とプログラミングの両方に興味がありました。STEPでは、まず英語基礎→プログラミング基礎→英語アウトプット→プログラミング応用という順番で学んだSTEPを投稿しています。他のユーザーのフィードバックで、自分のSTEPの改善点にも気付けました。今は学んだ知識を使って、小さなウェブサービスを作ることが目標です。STEPのおかげで、学習が迷子にならずに済むのがありがたいです。",
                "私は完全初心者としてプログラミング学習を始め、まずは基礎文法の習得から始めました。その後、簡単なWebサイトを作る演習→API利用→フレームワーク学習というSTEPで進めています。STEPに投稿することで、自分がどの順番で学習したかを整理でき、他のユーザーが同じ順番で学べるようになっています。将来的にはデータ可視化や自動化ツールの開発にも挑戦したいと考えています。",
                "英語学習とプログラミング学習を並行して行う私にとって、STEPは最適なツールです。まず文法や基礎知識を身につけ、その後実践練習や簡単なプロジェクトに挑戦するという順序でSTEPを組んでいます。自分の学習記録を投稿すると、他のユーザーから有用なアドバイスや修正案がもらえるので、効率よくスキルを伸ばせています。今後はさらに応用的な学習STEPを作成する予定です。",
                "プログラミングを学び始めて2年になりますが、STEPを使って自分の学習プロセスを整理しました。まず基礎文法→小さなアプリ制作→フレームワーク学習→実務的プロジェクトという順で進めています。他のユーザーのSTEPを参考にすることで、より効率的に学べる点が助かります。自分の経験を投稿することで、同じく学び始めた方々の手助けになれば嬉しいです。",
                "英語の勉強を再開するにあたり、STEPを使って学習の順序を考えました。最初にリスニングと基礎文法→簡単な会話練習→ライティング練習→TOEIC形式問題という順で学ぶことで、無理なく段階的にスキルを上げられました。STEPに投稿することで、自分の成長も実感でき、また他の学習者の参考にもなりやすいです。今後はアウトプットの幅をさらに広げる予定です。",
                "独学でプログラミングを学んでいましたが、STEPに参加してから学習の順番を明確にできました。HTML→CSS→JavaScript→フレームワーク→小さなアプリ制作というSTEPです。特に自分が躓きやすいポイントを他のユーザーの経験から学べるのが大きいです。自分のSTEPを投稿することで、他の初心者が効率よく学べる手助けになればと思います。",
                "私は英語とプログラミング両方の学習STEPを作成しています。英語は基礎文法→リスニング→スピーキング練習、プログラミングは基礎文法→簡単なプロジェクト→フレームワーク学習の順で進めています。STEPに投稿することで、自分の学習の振り返りになり、さらに他のユーザーのSTEPも参考にできるので、学習の効率が格段に上がりました。",
                "STEPを使って自分の学習記録を整理しています。私はまずプログラミングの基礎→簡単なWebアプリ制作→データ処理→フレームワーク学習という順序で進めました。自分の経験を投稿すると、同じように学ぶ人の助けになり、また自分も振り返ることができるので、学習のモチベーションが維持しやすいです。今後はさらにステップアップして、実務に近いプロジェクトにも挑戦していきたいです。"
            ];

            DB::table('users')->insert([
                'name' => '管理 花子',
                'email' => 'mastaroyamada2000@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[0],
                'email_verified_at' => $emailVerifiedAt,
                'user_img' => '/job_kantokukan_woman.png',
                'created_at' => $randDate,
                'updated_at' => $randDate,
                'role' => 1,
            ]);

            DB::table('users')->insert([
                'name' => '佐藤 太郎B',
                'email' => 'taroyamadataroyamada20000101+b@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[1],
                'user_img' => '/onepiece15_lucci.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '鈴木 花子C',
                'email' => 'taroyamadataroyamada20000101+c@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[2],
                'user_img' => '/pose_galpeace_schoolgirl.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '高橋 次郎D',
                'email' => 'taroyamadataroyamada20000101+d@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[3],
                'user_img' => '',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '田中 美咲E',
                'email' => 'taroyamadataroyamada20000101+e@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[4],
                'user_img' => '/otaku_girl_fashion.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '山田 一郎F',
                'email' => 'taroyamadataroyamada20000101+f@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[5],
                'user_img' => '/hengao_mabuta_uragaesu.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '佐々木 優G',
                'email' => 'taroyamadataroyamada20000101+g@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[6],
                'user_img' => '/girl_13.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '中村 大輔H',
                'email' => 'taroyamadataroyamada20000101+h@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[7],
                'user_img' => '/magonote_ojiisan.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '小林 真理子I',
                'email' => 'taroyamadataroyamada20000101+i@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[8],
                'user_img' => '/pose_reiwa_woman.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);
        } else {
            $randDate = Carbon::now()->subMonths(3)->addSecond(rand(0, 60 * 60 * 24 * 30));

            //メール認証はcreateから3日後ってことで
            $emailVerifiedAt = (clone $randDate)->addDays(3);

            $user_profiles = [
                "プログラミング初心者としてSTEPを始めました。最初は何から手を付ければいいかわからず戸惑っていましたが、他のユーザーのSTEPを参考にして、HTML→CSS→JavaScriptの順で学ぶことにしました。特に小さなプロジェクトを作りながら学ぶ方法が、自分には合っていたと感じます。今では簡単なウェブアプリを作れるまでになり、次はReactやVueにも挑戦したいと思っています。自分のSTEPを共有することで、これから学ぶ人の参考になれば嬉しいです。",
                "英語学習のSTEPを投稿しています。私は中学英語の基礎を復習した後、TOEIC対策用の単語帳を使い、その後リスニング教材で耳を慣らす、という順で学習しました。STEPを作ることで、自分の学習の振り返りにもなり、他の人からフィードバックをもらえるのが楽しいです。今後はアウトプット中心の英会話練習も加えて、STEPをアップデートしていきたいです。",
                "これまで独学でプログラミングを学んできましたが、STEPに参加して自分の学習順序を整理することにしました。Pythonの基本を押さえた後、データ分析や機械学習に挑戦するという順で学習を進めました。STEPに投稿することで、自分の理解度も確認でき、また他のユーザーのSTEPを参考に新しい学習方法を取り入れられるのが魅力です。今後はWebフレームワークを使ったアプリ開発にも挑戦予定です。",
                "STEPを使い始めてから、自分の学習の優先順位が明確になりました。プログラミング言語やツールの習得順序を悩んでいた私にとって、他のユーザーの具体的なSTEPは大変参考になりました。特にフロントエンド開発のSTEPは、自分が挫折しやすい箇所を事前に知ることができ、効率的に学習を進めることができています。私も自分の経験をシェアして、少しでも誰かの助けになれればと思います。",
                "学生時代から英語とプログラミングの両方に興味がありました。STEPでは、まず英語基礎→プログラミング基礎→英語アウトプット→プログラミング応用という順番で学んだSTEPを投稿しています。他のユーザーのフィードバックで、自分のSTEPの改善点にも気付けました。今は学んだ知識を使って、小さなウェブサービスを作ることが目標です。STEPのおかげで、学習が迷子にならずに済むのがありがたいです。",
                "私は完全初心者としてプログラミング学習を始め、まずは基礎文法の習得から始めました。その後、簡単なWebサイトを作る演習→API利用→フレームワーク学習というSTEPで進めています。STEPに投稿することで、自分がどの順番で学習したかを整理でき、他のユーザーが同じ順番で学べるようになっています。将来的にはデータ可視化や自動化ツールの開発にも挑戦したいと考えています。",
                "英語学習とプログラミング学習を並行して行う私にとって、STEPは最適なツールです。まず文法や基礎知識を身につけ、その後実践練習や簡単なプロジェクトに挑戦するという順序でSTEPを組んでいます。自分の学習記録を投稿すると、他のユーザーから有用なアドバイスや修正案がもらえるので、効率よくスキルを伸ばせています。今後はさらに応用的な学習STEPを作成する予定です。",
                "プログラミングを学び始めて2年になりますが、STEPを使って自分の学習プロセスを整理しました。まず基礎文法→小さなアプリ制作→フレームワーク学習→実務的プロジェクトという順で進めています。他のユーザーのSTEPを参考にすることで、より効率的に学べる点が助かります。自分の経験を投稿することで、同じく学び始めた方々の手助けになれば嬉しいです。",
                "英語の勉強を再開するにあたり、STEPを使って学習の順序を考えました。最初にリスニングと基礎文法→簡単な会話練習→ライティング練習→TOEIC形式問題という順で学ぶことで、無理なく段階的にスキルを上げられました。STEPに投稿することで、自分の成長も実感でき、また他の学習者の参考にもなりやすいです。今後はアウトプットの幅をさらに広げる予定です。",
                "独学でプログラミングを学んでいましたが、STEPに参加してから学習の順番を明確にできました。HTML→CSS→JavaScript→フレームワーク→小さなアプリ制作というSTEPです。特に自分が躓きやすいポイントを他のユーザーの経験から学べるのが大きいです。自分のSTEPを投稿することで、他の初心者が効率よく学べる手助けになればと思います。",
                "私は英語とプログラミング両方の学習STEPを作成しています。英語は基礎文法→リスニング→スピーキング練習、プログラミングは基礎文法→簡単なプロジェクト→フレームワーク学習の順で進めています。STEPに投稿することで、自分の学習の振り返りになり、さらに他のユーザーのSTEPも参考にできるので、学習の効率が格段に上がりました。",
                "STEPを使って自分の学習記録を整理しています。私はまずプログラミングの基礎→簡単なWebアプリ制作→データ処理→フレームワーク学習という順序で進めました。自分の経験を投稿すると、同じように学ぶ人の助けになり、また自分も振り返ることができるので、学習のモチベーションが維持しやすいです。今後はさらにステップアップして、実務に近いプロジェクトにも挑戦していきたいです。"
            ];

            DB::table('users')->insert([
                'name' => '管理ああああああああああああああああああああ花子',
                'email' => 'taroyamadataroyamada20000101@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[0],
                'email_verified_at' => $emailVerifiedAt,
                'user_img' => '/job_kantokukan_woman.png',
                'created_at' => $randDate,
                'updated_at' => $randDate,
                'role' => 1,
            ]);

            DB::table('users')->insert([
                'name' => '佐藤あああああああああああああああああああ太郎B',
                'email' => 'taroyamadataroyamada20000101+b@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[1],
                'user_img' => '/onepiece15_lucci.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '鈴木あああああああああああああああああああ花子C',
                'email' => 'taroyamadataroyamada20000101+c@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[2],
                'user_img' => '/pose_galpeace_schoolgirl.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '高橋あああああああああああああああああああ次郎D',
                'email' => 'taroyamadataroyamada20000101+d@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[3],
                'user_img' => '',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '田中あああああああああああああああああああ美咲E',
                'email' => 'taroyamadataroyamada20000101+e@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[4],
                'user_img' => '/otaku_girl_fashion.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '山田あああああああああああああああああああ一郎F',
                'email' => 'taroyamadataroyamada20000101+f@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[5],
                'user_img' => '/hengao_mabuta_uragaesu.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '佐々木あああああああああああああああああああ優G',
                'email' => 'taroyamadataroyamada20000101+g@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[6],
                'user_img' => '/girl_13.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '中村あああああああああああああああああああ大輔H',
                'email' => 'taroyamadataroyamada20000101+h@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[7],
                'user_img' => '/magonote_ojiisan.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

            DB::table('users')->insert([
                'name' => '小林ああああああああああああああああああ真理子I',
                'email' => 'taroyamadataroyamada20000101+i@gmail.com',
                'password' => bcrypt('aaaa1111'),
                'user_profile' => $user_profiles[8],
                'user_img' => '/pose_reiwa_woman.png',
                'email_verified_at' => $emailVerifiedAt,
                'created_at' => $randDate,
                'updated_at' => $randDate,
            ]);

        }
    }
}
