<template>
<div class="c-container c-container--colored">
    <h2 class="c-title c-title--inner">ログイン</h2>
    
    <flash-msg />
    <form  @submit.prevent="login" class="c-formGroup">
        <base-error-display :errors="errors" ></base-error-display>

        <!-- メールアドレス -->
        <base-input
            title="メールアドレス"
            type="text"
            v-model="email"
            :isAutofocus="true"
        ></base-input>

        <!-- パスワード -->
        <base-input
            title="パスワード"
            type="password"
            v-model="password"
            :isAutofocus="false"
        ></base-input>

        <!-- 次回ログイン省略 -->
        <div class="c-formCheck">
            <input class="c-formCheck__box" type="checkbox" name="remember" v-model="remember">
            <label class="c-formCheck__labelText" >次回ログインを省略する</label>
        </div>

        <!-- ログインボタン -->
        <div class="c-area c-area__actionArea">
            <button type="submit" class="c-btn c-btn--primary">ログインする</button>
        </div>

    </form>

    <!-- パスワードを忘れた場合のリンク -->
    <div class="c-linkSentence c-linkSentence--shrink">
        パスワードを忘れた方は
        <a class="c-linkSentence__word">
            <router-link :to="{ name: 'password-reset-email'}" >こちら</router-link></a>
    </div>

</div>
</template>
<script>
import { STORAGE_NAMES } from '../const';

export default {
    data() {
        return {
            email: '',
            password: '',
            remember: false,
            errors: [],
        };
    },

    methods: {

        //ログイン認証
        async login() {

            try {
                const response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                    remember: this.remember,
                });

                //トークン情報をローカルストレージに保存
                localStorage.setItem(STORAGE_NAMES.ACCESS_TOKEN, response.data.access_token);
                localStorage.setItem(STORAGE_NAMES.REFRESH_EXPIRES, response.data.refresh_token_expires_in);

                // ログイン情報をVuexに保存。あと、このあとメール認証をチェックするので、認証情報も合わせて取得
                const user  = await this.$store.dispatch('user/login');

                if (user.email_verified_at) {
                    //メール認証済みなのでマイページへ遷移
                    this.$store.dispatch('flashMsg/showMsg', 'ログインしました');
                    this.$router.push({ name: 'mypage' });
                    
                } else {
                    //メール未認証のため、認証を促す画面へ遷移
                    this.$router.push({ name: 'request-email-verification'});
                }
                

            } catch(error) {
                //エラーメッセージはauthControllerで指定
                this.errors = error.response.data.errors['general'];
            }
        },
    }
}
</script>