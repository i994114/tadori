<template>
    <div class="c-container c-container--colored">
        <!-- ページタイトル -->
        <h2 class="c-title c-title--inner">パスワードリセットフォーム</h2>

        <form @submit.prevent="openModal" class="c-formGroup">
            <!-- メールアドレス -->
            <base-input
                title="メールアドレス"
                type="text"
                v-model="email"
                :isAutofocus="false"
                :isDisabled="true"
            ></base-input>

            <!-- パスワード -->
            <base-input
                title="パスワード"
                type="password"
                v-model="password"
                :isAutofocus="false"
                :maxLength="MAX_LENGTH.USER_PASSWORD"
                :errors="errors['password']"
            ></base-input>

            <!-- パスワード(確認) -->
            <base-input
                title="パスワード(確認)"
                type="password"
                name="password_confirmation"
                v-model="password_confirmation"
                :isAutofocus="false"
            ></base-input>

            <div class="c-area c-area__actionArea">
                <button class="c-btn c-btn--primary">新しいパスワードを登録する</button>
            </div>

            <!-- modal -->
            <div v-if="modal.show">
                <base-modal
                    :title="modal.title"
                    :text="modal.text"
                    @confirm="resetPassword"
                    @cancel="closeModal"
                ></base-modal>
            </div>
        </form>
    </div>
</template>
<script>
import BaseErrorDisplay from './BaseErrorDisplay.vue';
import { MAX_LENGTH } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],
    
    props: {
        email: String,
        token: String,
    },

    data() {
        return {
            MAX_LENGTH,

            password: '',
            password_confirmation: '',
            errors: [],
        };
    },

    methods: {

        //Modal画面表示
        openModal() {
            let msg = 'パスワード変更しますか？';

            this.showModal({
                text: msg
            });
        },

        //入力されたパスワードをサーバに登録する
        async resetPassword() {

            //modal閉じる
            this.closeModal();

            try {
                await axios.post('/password/reset', {
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    token: this.token,
                });
                this.$store.dispatch('flashMsg/showMsg', '新しいパスワードを登録しました');
                this.$router.push('/');
            } catch(e) {
                this.errors = e.response.data.errors;
            }
        }
    }
}
</script>
