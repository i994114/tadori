<template>
<div>
    <h2 class="c-title c-title--outer">利用者プロフィール編集</h2>

    <!-- Modal -->
    <div v-if="modal.show">
        <base-modal
            :title="modal.title"
            :text="modal.text"
            @confirm="handleConfirm"
            @cancel="closeModal"
        ></base-modal>
    </div>

    <div class="c-mainArea">
        <div class="c-container c-container--colored">
            
            <form @submit.prevent="openModal('edit')" class="c-formGroup" enctype="multipart/form-data" >
                <!-- ユーザ名 -->
                <base-input
                    title="ユーザ名"
                    type="text"
                    v-model="user.name"
                    :isAutofocus="true"
                    :isRequired="true"
                    :isCountable="true"
                    placeholder="例：山田太郎"
                    :maxLength="MAX_LENGTH.USER_NAME"
                    :errors="errors['name']"
                ></base-input>

                <!-- プロフィール文章 -->
                <base-textarea
                    title="プロフィール文章"
                    v-model="user.user_profile"
                    placeholder="STEPで共有したい自分の経験や学びのスタイルについて書いてみましょう"
                    :maxLength="MAX_LENGTH.USER_PROFILE"
                    :errors="errors['user_profile']"
                ></base-textarea>

                <!-- プロフィール画像 -->
                <base-image-uploader
                    title="プロフィール画像"
                    v-model="user.user_img"
                    :errors="errors['user_img']"
                ></base-image-uploader>

                <button type="submit" class="c-btn c-btn--primary">プロフィールを変更する</button>
            </form>
    
            <!-- 退会 -->
            <div class="c-linkSentence c-linkSentence--shrink">
                退会は<a @click.prevent="openModal('delete')" >こちら</a>
            </div>
        </div>
        <!-- サイドバー -->
        <sidebar-right-component />
    </div>
</div>
</template>
<script>
import { LOGIN_TYPE, MAX_LENGTH, STORAGE_NAMES } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],
    data() {
        return {
            MAX_LENGTH,

            userId: '',
            type: '',

            user: {},       //DB登録のユーザ情報
            editedUser: {},   //編集されたユーザ情報格納用
            errors: {},
        };
    },
    
    mounted() {
        this.userId = this.$route.params.id;
        this.getUser(this.userId);
    },

    watch: {

        //inputタブの変更を検知する
        "user.name"(newValue) {
            this.editedUser.name = newValue;
        },

        //textareaの変更を検知する
        "user.user_profile"(newValue) {
            this.editedUser.user_profile = newValue;
        },

        //imgの変更を検知する
        "user.user_img"(newValue) {
            this.editedUser.user_img = newValue;
        }
    },

    methods: {
        
        //Modal画面表示
        openModal(type) {
            let msg = '';
            this.type = type;

            switch (type) {
                case 'edit':
                    msg = 'ユーザ情報を変更しますか？';
                    break;
                case 'delete':
                    msg = '本当に退会しますか？';
                    break; 
                default:
                    break;
            }

            this.showModal({
                text: msg
            });
        },

        //モーダルの「はい」で呼ばれる共通ハンドラ
        handleConfirm() {
            //modal閉じる
            this.closeModal();
            
            switch (this.type) {
                case 'edit':
                    this.editUser();
                    break;
                case 'delete':
                    this.deleteUser();
                    break; 
                default:
                    break;
            }
        },

        //ユーザ情報を読み出しS
        async getUser(id) {
            const response = await axios.get(`/api/users/${id}`);
            
            const data = response.data;

            //DBの値がnullだと、取得時点で'null'の文字列が入るので空欄に変換
            if (data.user_profile === null) {
                data.user_profile = '';
            }
            this.user = data;
        },

        //変更内容をサーバに送信
        async editUser() {
            //modalを閉じる
            this.closeModal();

            const formData = new FormData();

            formData.append('name', this.editedUser.name);
            formData.append('user_profile', this.editedUser.user_profile);

            if (this.editedUser.user_img) { //登録画像変更があった場合
                if (typeof this.editedUser.user_img === 'object') {
                    formData.append('user_img', this.editedUser.user_img);
                } else {
                    //処置なし(画像アップロードおよび削除操作なしなので)
                }
            } else {    //登録画像削除時
                formData.append('deleted_user_img', true);
            }

            //擬似要素を追加
            formData.append('_method', 'PUT');

            try {
                const response = await axios.post(`/api/users/${this.userId}`, formData);

                //フラッシュメッセージ
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                //ユーザVuex情報更新
                this.$store.dispatch('user/login');

                //エラークリア
                this.errors = {};

                //マイページに遷移
                this.$router.push({ name: 'mypage' });
            } catch(e) {
                this.errors = e.response.data.errors;
                //modalを閉じる
                this.closeModal();
            }

        },

        //ユーザ情報削除
        async deleteUser() {
            let response;

            //modalを閉じる
            this.closeModal();

            //お気に入り削除
            await axios.delete(`/api/user-favorites/${this.userId}`);

            //ユーザ情報削除
            response = await axios.delete(`/api/users/${this.userId}`);
            
            //ローカルストレージのトークン情報を削除
            localStorage.removeItem(STORAGE_NAMES.ACCESS_TOKEN);
            localStorage.removeItem(STORAGE_NAMES.REFRESH_EXPIRES);

            //Vuex情報も削除
            this.$store.dispatch('user/logout');

            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //トップ画面に遷移
            this.$router.replace({name: 'welcome'});


        }

    }


}
</script>