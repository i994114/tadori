<template>
<div class="c-container c-container--colored">

    <h2 class="c-title c-title--inner">ユーザ登録</h2>
    
    <form @submit.prevent="openModal" class="c-formGroup">

        <!-- ユーザ名 -->
        <base-input
            title="ユーザ名"
            type="text"
            v-model="name"
            :isAutofocus="true"
            :isCountable="true"
            placeholder="例：山田太郎"
            :maxLength="MAX_LENGTH.USER_NAME"
            :errors="errors['name']"
        ></base-input>

        <!-- メールアドレス -->
        <base-input
            title="メールアドレス"
            type="text"
            v-model="email"
            rule="英数字・記号6文字以上"
            :isAutofocus="false"
            :isCountable="true"
            placeholder="例：sample@sample.com"
            :maxLength="MAX_LENGTH.USER_EMAIL"
            :errors="errors['email']"
        ></base-input>
        
        <!-- パスワード -->
        <base-input
            title="パスワード"
            type="password"
            v-model="password"
            rule="8文字以上"
            :isAutofocus="false"
            :isCountable="true"
            placeholder="8文字以上の英数字を入力"
            :maxLength="MAX_LENGTH.USER_PASSWORD"
            :errors="errors['password']"
        ></base-input>

        <!-- パスワード(確認) -->
        <base-input
            title="パスワード(確認)"
            type="password"
            v-model="password_confirmation"
            :isAutofocus="false"
            :isCountable="true"
            placeholder="もう一度パスワードを入力"
            :maxLength="MAX_LENGTH.USER_PASSWORD"
        ></base-input>

        <!-- プライバシーポリシー --->
        <div class="c-formCheck">
            <input class="c-formCheck__box" type="checkbox" name="agreedPrivacy" v-model="agreedPrivacy" >
            <label class="c-formCheck__labelText">
                <router-link :to="{ name: 'privacy-policy' }" target="_blank" rel="noopener noreferrer" >プライバシーポリシー</router-link>
                に同意します。
            </label> 
        </div>
        <base-error-display :errors="errors['agreedPrivacy']"></base-error-display>
        
        <!-- 利用規約 --->
        <div class="c-formCheck">
            <input class="c-formCheck__box" type="checkbox" name="agreedTerms" v-model="agreedTerms" >
            <label class="c-formCheck__labelText">
                <router-link :to="{ name: 'terms-of-service' }" target="_blank" rel="noopener noreferrer">利用規約</router-link>
                に同意します。
            </label>
        </div>
        <base-error-display :errors="errors['agreedTerms']"></base-error-display>
         
        <div class="c-area c-area__actionArea">
            <button class="c-btn c-btn--primary">ユーザ登録する</button>
        </div>

        <!-- Modal -->
        <div v-if="modal.show">
            <base-modal
                :title="modal.title"
                :text="modal.text"
                @confirm="registerUser"
                @cancel="closeModal"
            ></base-modal>
        </div>
    </form>

</div>
</template>
<script>
import { MAX_LENGTH, STORAGE_NAMES } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],
    
    data() {
        return {
            MAX_LENGTH,
            
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            
            agreedPrivacy: false,
            agreedTerms: false,

            errors: [],
        };
    },

    methods: {
        
        //Modal画面表示
        openModal() {
            let msg = 'ユーザ登録しますか？';

            this.showModal({
                text: msg
            });
        },

        //入力されたユーザ情報をサーバに登録する
        async registerUser() {
            let response;

            //サーバに入力データを登録
            try {
                response = await axios.post('/api/users', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    agreedPrivacy: this.agreedPrivacy,
                    agreedTerms: this.agreedTerms,
                });
                await this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                //modalを閉じる
                this.closeModal();
            } catch(e) {
                this.errors = e.response.data.errors;
                this.$store.dispatch('flashMsg/showMsg', 'ユーザ登録に失敗しました。エラー表示を確認してください。');

                //modalを閉じる
                this.closeModal();

                return;
            }

            //bladeのときと同様、まずはログイン状態にする
            try {
                response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                });

                //アクセストークンをローカルストレージに保存
                localStorage.setItem(STORAGE_NAMES.ACCESS_TOKEN, response.data.access_token);
                localStorage.setItem(STORAGE_NAMES.REFRESH_EXPIRES, response.data.refresh_token_expires_in);

            } catch(e) {    //基本とおらないパス
                this.$store.dispatch('flashMsg/showMsg', 'ユーザ登録後のログイン遷移に失敗しました。ログイン操作してください。');
                return;
            }

            //ログインユーザ情報のストア更新後、メール認証画面にいく
            try {
                await this.$store.dispatch('user/login');
                this.$router.push({name: 'request-email-verification'});
            } catch(e) {    //基本とおらないパス
                this.$store.dispatch('flashMsg/showMsg', '処理に失敗しました。ログイン操作してください。');
                return;
            }
        },

    },
}
</script>