<template>
    <div>判定中です。少々お待ちください...</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { STORAGE_NAMES } from '../const';
import { restoreSessionIfNeeded } from '../router'

export default {
    props: ['status'],

    computed: {
        ...mapGetters('user', [
            'authUser',    //ログインユーザ情報
        ]),
    },

    async mounted() {
        let type;

        //トークンをもとに、ログイン状態を復元する
        //理由：ログイン状態のまま、画面を閉じてしまっている場合があるため(”Vuexユーザ情報なしだがトークンあり状態”を想定)
        await restoreSessionIfNeeded();

        //restoreSessionIfNeededにより、Vuexに確実に値がセットされたことを確認するためにVuex情報をconstに保存
        const user = this.authUser;

        //ローカルストレージのトークン取得
        const token = localStorage.getItem(STORAGE_NAMES.ACCESS_TOKEN);

        //メール認証OKがコントローラからきているか
        if (this.status === 'OK' || this.status === 'Authenticated') {

            //トークンがあるか、もしくはログイン状態ならマイページへ
            if (token || user) {
                //フラッシュメッセージ
                this.$store.dispatch('flashMsg/showMsg', 'メール認証しました。STEPの登録またはチャレンジをおこないましょう。');
                //Vuexのユーザ情報更新
                await this.$store.dispatch('user/login');
                //マイページへ
                this.$router.push({ name: 'mypage'});

            //未ログインのとき
            } else {
                //フラッシュメッセージ
                this.$store.dispatch('flashMsg/showMsg', 'メール認証しました。ログインし、STEPの登録またはチャレンジをおこないましょう。');
                //ログイン画面へ
                this.$router.push({ name: 'login'});
            }
        } else {
            // 認証失敗 or トークンなし → top画面へ
            this.$store.dispatch('flashMsg/showMsg', 'メール認証に失敗しました。再度おこなってください。');
            this.$router.push({ name: 'welcome' });
        }
    }
};
</script>
