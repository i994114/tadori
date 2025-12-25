<template>
<div>
    <!-- ページタイトル -->
    <h2 class="c-title c-title--outer">パスワード変更</h2>
     <div class="c-mainArea">
        <div class="c-container c-container--colored">
            <form @submit.prevent="openModal" class="c-formGroup">
                <!-- 現在のパスワード -->
                <base-input
                    title="現在のパスワード"
                    type="password"
                    v-model="oldPassword"
                    :isAutofocus="true"
                    :errors="errors['old_password']"
                ></base-input>

                <!-- 新しいパスワード -->
                <base-input
                    title="新しいパスワード"
                    type="password"
                    v-model="newPassword"
                    rule="8文字以上"
                    :isAutofocus="false"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.USER_PASSWORD"
                    :errors="errors['new_password']"
                ></base-input>

                <!-- パスワード(確認) -->
                <base-input
                    title="新しいパスワード(確認)"
                    type="password"
                    v-model="confirmPassword"
                    :isAutofocus="false"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.USER_PASSWORD"
                    :errors="errors['password']"
                ></base-input>

                <button class="c-btn c-btn--primary">パスワードを変更する</button>

                <!-- Modal -->
                <div v-if="modal.show">
                    <base-modal
                        :title="modal.title"
                        :text="modal.text"
                        @confirm="editPassword"
                        @cancel="closeModal"
                    ></base-modal>
                </div>
            </form>
        </div>
        <!-- サイドバー -->
        <sidebar-right-component />
    </div>
</div>
</template>
<script>
import { mapState, mapGetters } from 'vuex';
import { LOGIN_TYPE, MAX_LENGTH } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],
    
    data() {
        return {
            MAX_LENGTH,
            
            userId: '',
            oldPassword: '',
            newPassword: '',
            confirmPassword: '',
            errors: {},
        };
    },

    mounted() {
        this.userId = this.$route.params.id;
    },

    methods: {

        //Modal画面表示
        openModal() {
            let msg = 'パスワードを変更しますか？';

            this.showModal({
                text: msg
            });
        },

        //変更内容をサーバに送信
        async editPassword() {
            let response;
            try {

                response = await axios.put(`/api/user/${this.userId}/password`, {
                    old_password: this.oldPassword,
                    new_password: this.newPassword,
                    new_password_confirmation: this.confirmPassword,
                });

                //modalを閉じる
                this.closeModal();

                this.$store.dispatch('flashMsg/showMsg', response.data.msg);
                
                //パスワード変更によりログアウト
                response = await axios.post('/api/logout');

                //ローカルストレージのトークン情報を削除
                this.$store.dispatch('user/logout');

                this.$store.dispatch('flashMsg/showMsg', 'パスワードを変更しました。再度ログインしてください。');

                //ログイン画面に遷移
                this.$router.replace({name: 'login'});
            } catch(e) {
                this.errors = e.response.data.errors;
                //modalを閉じる
                this.closeModal();

            }
        }
    }
}
</script>