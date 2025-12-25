<template>
    <div class="c-container c-container--colored">
        <h2 class="c-title c-title--inner">パスワードリセット</h2>
        <form @submit.prevent="sendResetEmail" class="c-formGroup">
            <!-- 説明部分 -->
            <div class="c-information">
                ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーを送付いたします.
            </div>

            <!-- メールアドレス -->
            <base-input
                title="メールアドレス"
                type="text"
                v-model="email"
                :isAutofocus="false"
                :isCountable="false"
                :maxLength="MAX_LENGTH.USER_EMAIL"
                :errors="errors['email']"
            ></base-input>

            <div class="c-area c-area__actionArea">
                <button  type="submit" class="c-btn c-btn--primary">送信する</button>
            </div>

        </form>
    </div>
</template>
<script>
import { MAX_LENGTH } from '../const';

export default {
    data() {
        return {
            MAX_LENGTH,
            
            email : '',
            errors: {},
        };
    },

    methods: {
        //入力されたアドレスにパスワードを送信する
        async sendResetEmail() {
            
            try {
                const response = await axios.post('/password/email', {email: this.email});

                this.$store.dispatch('flashMsg/showMsg', 'リセットURLをメールで通知しました。');

                //エラー消去
                this.errors = {};
            } catch(e) {
                this.errors = e.response.data.errors;
                this.$store.dispatch('flashMsg/showMsg', '処理に失敗しました。もう一度お試しください。')
            }
        }
    }
}
</script>