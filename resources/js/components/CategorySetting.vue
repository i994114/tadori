<template>
<div class="c-container c-container--colored">
    <h2 class="c-title c-title--inner">カテゴリ設定</h2>

    <!-- フラッシュメッセージ -->
    <flash-msg />

    <!-- Modal -->
    <div v-if="modal.show">
        <base-modal
            :title="modal.title"
            :text="modal.text"
            @confirm="handleConfirm"
            @cancel="closeModal"
        ></base-modal>
    </div>

    <!-- 新規追加欄 -->
    <form  @submit.prevent="openModal('new')" class="c-formGroup">

        <base-input
            type="text"
            v-model="newCategoryName"
            placeholder="「例：プログラミング」"
            :isAutofocus="true"
            :isCountable="true"
            :maxLength="MAX_LENGTH.CATEGORY_NAME"
            :errors="errors.new['newCategoryName']"
        ></base-input>

        <button  type="submit" class="c-btn c-btn--primary">追加する</button>
    </form>

    <!-- カテゴリ一覧 -->
    <div  class="p-adminSetting" v-for="category in allCategories" :key="category.id" :value="category.category_name">
        
        <div v-if="category.deleted_at">
            <base-input
                type="text"
                v-model="category.category_name"
                :isDisabled="true"
            ></base-input>

            <i class="fas fa-undo" @click="openModal('restore', category.id)" ></i>
        </div>
        <div v-else>
            <base-input
                type="text"
                v-model="editCategoryName[category.id]"
                :isCountable="true"
                :maxLength="MAX_LENGTH.CATEGORY_NAME"
                :errors="errors.edit[category.id]"
            ></base-input>

            <i class="far fa-edit" @click="openModal('edit', category.id)" ></i>
            <i class="far fa-trash-alt p-adminSetting__icon" @click="openModal('delete', category.id)" ></i>
            
        </div>

    </div>

</div>

</template>
<script>
import { mapState } from 'vuex'
import BaseErrorDisplay from './BaseErrorDisplay.vue';
import { MAX_LENGTH } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],
    
    data() {
        return {
            MAX_LENGTH,
            
            confirmAction: null, //モーダルで実行する関数を保持
            newCategoryName: '',
            editCategoryName: {},   //編集用

            errors: {
                new: {},
                edit: {},
            },
        };
    },

    mounted() {
        this.$store.dispatch('category/getCategories', 'all').then(() => {
            // カテゴリ一覧取得後に editCategoryName を初期化
            this.allCategories.forEach(category => {
                this.$set(this.editCategoryName, category.id, category.category_name);
            });
        });
    },

    computed: {
        ...mapState({
            allCategories: state => state.category.allCategories,
            
        }),
    },

    methods: {
        //Modal画面表示
        openModal(type, id = null) {
            let msg = '';

            switch (type) {
                case 'new':
                    msg = 'カテゴリを登録しますか？';
                    this.confirmAction = this.addCategory;
                    break;
                case 'edit':
                    msg = 'カテゴリを変更しますか？';
                    this.confirmAction = () => this.editCategory(id);
                    break;
                case 'delete':
                    msg = 'STEPを削除しますか？';
                    this.confirmAction = () => this.deleteCategory(id);
                    break;
                case 'restore':
                    msg = '削除したカテゴリを復元しますか？';
                    this.confirmAction = () => this.restoreCategory(id);
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
            
            if (typeof this.confirmAction === 'function') {
                this.confirmAction();
            }
        },

        //カテゴリを新規追加する
        async addCategory() {
            try {
                const response = await axios.post('/api/categories', {
                    newCategoryName: this.newCategoryName,
                });

                //フラッシュメッセージ表示
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);
                
                //一覧表示を更新するためにカテゴリを再度dispatchする
                await this.$store.dispatch('category/getCategories', 'setting');

                //追加にともない、editCategoryNameを再初期化
                this.allCategories.forEach(category => {
                    this.$set(this.editCategoryName, category.id, category.category_name);
                });

                //入力欄とエラーをクリア
                this.newCategoryName = '';
                this.errors.new = {};
            } catch(e) {
                this.errors.new = e.response.data.errors;
            }

            //modalを閉じる
            this.closeModal();
        },

        //カテゴリを変更する
        async editCategory(id) {

            try {
                const name = this.editCategoryName[id];

                const response = await axios.put(`/api/categories/${id}`, {
                    editCategoryName: name,
                });
                
                //フラッシュメッセージ
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                //一覧表示を更新するためにカテゴリを再度dispatchする
                this.$store.dispatch('category/getCategories', 'setting');

                //エラーを削除
                this.errors.edit[id] = {};

                //modalを閉じる
                this.closeModal();
            } catch(e) {
                this.errors.edit = {
                    ...this.errors.edit,
                    [id]: e.response.data.errors.editCategoryName
                };

                //modalを閉じる
                this.closeModal();

            }
        },

        //カテゴリを削除する
        async deleteCategory(id) {
            
            const response = await axios.delete(`/api/categories/${id}`);
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);
            
            //一覧再表示
            this.$store.dispatch('category/getCategories', 'setting');

            //modalを閉じる
            this.closeModal();
        },

        //削除したカテゴリーを復活
        async restoreCategory(id) {

            const response = await axios.patch(`/api/category/${id}/restore`);
            
            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //一覧再表示
            this.$store.dispatch('category/getCategories', 'setting');

            //modalを閉じる
            this.closeModal();
        }
    }
}
</script>