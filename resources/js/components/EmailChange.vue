<template>
<div>
    <!-- ページタイトル -->
    <h2 class="c-title c-title--outer">メールアドレス変更</h2>

    <div class="c-mainArea">
        <div class="c-container c-container--colored">            
            <form @submit.prevent="openModal" class="c-formGroup">
                <base-input
                    title="現在のメールアドレス"
                    type="text"
                    v-model="oldEmail"
                    :isAutofocus="false"
                    :isDisabled="true"
                ></base-input>

                <base-input
                    title="新しいメールアドレス"
                    type="text"
                    rule="英数字・記号6文字以上"
                    v-model="newEmail"
                    :isAutofocus="true"
                    :isDisabled="false"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.USER_EMAIL"
                    :errors="errors['email']"
                ></base-input>

                <base-input
                    title="パスワード"
                    type="password"
                    v-model="password"
                    :isAutofocus="false"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.USER_EMAIL"
                    :errors="errors['password']"
                ></base-input>

                <button class="c-btn c-btn--primary">アドレスを変更する</button>
                
                <!-- Modal -->
                <div v-if="modal.show">
                    <base-modal
                        :title="modal.title"
                        :text="modal.text"
                        @confirm="editEmail"
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
import { MAX_LENGTH} from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],

    data() {
        return {
            MAX_LENGTH,

            oldEmail: 'taroyamadataroyamada20000101+e@gmail.com',
            newEmail: 'taroyamadataroyamada20000101+e@gmail.com',
            password: 'aaaa1111',
            errors: {},
        };
    },

    methods: {
        //変更したアドレスをバックエンドに送る
        async editEmail() {

            //modal閉じる
            this.closeModal();
            
            try {

                const response = await axios.put('/api/user/email', {
                    email: this.newEmail,
                    password: this.password,
                });

                //フラッシュメッセージ
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                //パスワード変更によりログアウト
                response = await axios.post('/api/logout');

                //ローカルストレージのトークン情報を削除
                this.$store.dispatch('user/logout');

                this.$store.dispatch('flashMsg/showMsg', 'メールアドレスを変更しました。再度ログインしてください。');
            } catch(e) {
                this.errors = e.response.data.errors;
            }
        }
    }
}
</script>