<template>
<div class="c-mainArea">
    <div class="c-container c-container--colored">
        <!-- パンくずリスト -->
        <base-breadcrumb
            :type="type"
            :subStepId="id"
        ></base-breadcrumb>

        <h2 class="c-title c-title--inner" >{{ title }}</h2>

        <form  @submit.prevent="openModal" class="c-formGroup">
            <!-- STEP名 -->
            <base-input
                :title="inputTitle"
                type="text"
                v-model="stepNameDisplay"
                placeholder="「例: プログラミング基礎からHTMLまで」"
                :isAutofocus="true"
                :isRequired="true"
                :isCountable="true"
                :maxLength="MAX_LENGTH.STEP_NAME"
                :errors="nameError"
            ></base-input>

            <div v-if="isStep">
                <!-- カテゴリ -->
                <base-dropdown-menu
                    title="カテゴリ"
                    :dropdownMenus="isCategories"
                    v-model="categoryDisplay"
                    :type="DROPDOWN_TYPE.CATEGORIES"
                    extraClass="c-optionbox--primary"
                    :isRequired="true"
                    :errors="errors['category_id']"
                ></base-dropdown-menu>
            </div>

            <!-- 目安達成時間 -->
            <base-input
                title="目安達成時間"
                type="number"
                v-model="estimatedTimeDisplay"
                rule=" 最大値: STEP=28,800 子STEP=1,440"
                :isAutofocus="false"
                placeholder="単位:分"
                :maxLength="MAX_LENGTH.ESTIMATED_TIME"
                :errors="EstimatedTimeError"
            ></base-input>
            <span class="c-area c-area__estimatedTimeArea">入力値の時間換算結果: {{minutesToHours(estimatedTimeDisplay)}}</span>


            <!-- 子STEPの目安達成時間合計 -->
             <div v-if="isStepEdit">
                <the-step-time-sum v-model="estimatedTimeDisplay" />
             </div>

            <!-- STEP詳細 -->
            <base-textarea
                :title="detailTitle"
                v-model="stepDetailDisplay"
                placeholder="このSTEPで学ぶ内容やポイントを簡単にまとめましょう"
                :maxLength="MAX_LENGTH.STEP_DETAIL"
                :errors="detailError"
            ></base-textarea>


            <!-- STEP画像 -->
            <base-image-uploader
                :title="imgTitle"
                v-model="stepImgDisplay"
                :name="'step_img'"
                :errors="imgError"
            ></base-image-uploader>

            <!-- 登録/編集ボタン -->
            <div v-if="canEditBtn" class="c-area c-area__actionArea">
                <button class="c-btn c-btn--primary">{{title}}する</button>
            </div>

            <!-- 削除/復活エリア -->
            <div class="c-area c-area__deleteArea">
                <button v-if="canDeleteBtn" type="button" class="c-btn c-btn--danger" @click="openModal('delete')" >{{ DeleteBtnName }}</button>
                <button v-else-if="canRestoreBtn" type="button" class="c-btn c-btn--danger" @click="openModal('restore')" >{{ restoreBtnName }}</button>
            </div>
            
            <!-- Modal -->
            <div v-if="modal.show">
                <base-modal
                    :title="modal.title"
                    :text="modal.text"
                    @confirm="submit"
                    @cancel="closeModal"
                ></base-modal>
            </div>
        </form>

    </div> 

    <!-- サイドバー -->
    <sidebar-right-component />
</div>
</template>
<script>
import { mapState } from 'vuex';
import { MAX_LENGTH, STEP_TYPE, STORAGE_NAMES } from '../const';
import { DROPDOWN_TYPE } from '../const';
import modalMixin from '../modalMixin';
import { decideNextRoute, minutesToHours } from '../common';

export default {
    mixins: [modalMixin],

    props: {
        title: String,
        type: Number,
        url: String,
        id: Number,
    },

    data() {
        return {
            MAX_LENGTH,
            DROPDOWN_TYPE,
            
            stepName: '',
            stepDetail: '',
            categoryId: null,
            stepUri: '', //imgタグのsrc属性の画像URI
            estimatedTime: '',
            mode: '',

            step: {},
            errors: {},
        };
    },

    computed: {
        ...mapState({
            categories: state => state.category.categories,
            allCategories: state => state.category.allCategories,
            currentStep: state => state.currentStep.currentStep,
        }),

        // title を type に応じて可変
        inputTitle() {
            switch (this.type) {
                case STEP_TYPE.STEP_CREATE:
                case STEP_TYPE.STEP_EDIT:
                    return 'STEP名';
                case STEP_TYPE.SUB_STEP_EDIT:
                    return '子STEP名';
                default:
                    return '';
            }
        },
        detailTitle() {
            switch (this.type) {
                case STEP_TYPE.STEP_CREATE:
                case STEP_TYPE.STEP_EDIT:
                    return 'STEP説明';
                case STEP_TYPE.SUB_STEP_EDIT:
                    return '子STEP説明';
                default:
                    return '';
            }
        },
        imgTitle() {
            switch (this.type) {
                case STEP_TYPE.STEP_CREATE:
                case STEP_TYPE.STEP_EDIT:
                    return 'STEP画像';
                case STEP_TYPE.SUB_STEP_EDIT:
                    return '子STEP画像';
                default:
                    return '';
            }
        },

        //論理削除したカテゴリか、そうでないカテゴリ一覧かの識別
        isCategories() {
            if (this.type === STEP_TYPE.STEP_CREATE) {
                //新規STEP作成は論理削除したカテゴリは除く
                return this.categories;
            } else if (this.type === STEP_TYPE.STEP_EDIT) {
                //STEP編集のときは論理削除したカテゴリ込み
                //(論理削除済みのカテゴリを設定したSTEPもあるため)
                return this.allCategories;
            } else {
                return [];
            }
        },

        //登録しようとしているのはSTEPか
        isStep() {
            return this.type === STEP_TYPE.STEP_CREATE || this.type === STEP_TYPE.STEP_EDIT;
        },

        //STEP編集か
        isStepEdit() {
            return this.type === STEP_TYPE.STEP_EDIT;
        },

        //削除ボタン表示名
        DeleteBtnName() {
            switch (this.type) {
                case STEP_TYPE.STEP_EDIT:
                    return 'STEP削除する';
                case STEP_TYPE.SUB_STEP_EDIT:
                    return '子STEP削除する';
                default:
                    break;
            }
        },

        // 編集ボタンを表示するかどうか
        canEditBtn() {
            if (
                this.type !== STEP_TYPE.STEP_CREATE &&
                this.type !== STEP_TYPE.SUB_STEP_CREATE &&
                this.type !== STEP_TYPE.STEP_EDIT &&
                this.type !== STEP_TYPE.SUB_STEP_EDIT
            ) {
                return false;
            }

            if (this.step.deleted_at) {
                return false;
            }

            return true;
        },

        //削除ボタン表示可否
        canDeleteBtn() {
            
            if (this.type !== STEP_TYPE.STEP_EDIT && this.type !== STEP_TYPE.SUB_STEP_EDIT) {
                return false;
            } 

            if (this.step.deleted_at) {
                return false;
            }

            return true;
        },

        //復活ボタン表示可否
        canRestoreBtn() {
            
            if (this.type !== STEP_TYPE.STEP_EDIT && this.type !== STEP_TYPE.SUB_STEP_EDIT) {
                return false;
            } 

            return true;
        },

        //復活ボタン表示名
        restoreBtnName() {
            switch (this.type) {
                case STEP_TYPE.STEP_EDIT:
                    return 'STEPを復活する';
                case STEP_TYPE.SUB_STEP_EDIT:
                    return '子STEPを復活する';
                default:
                    return '';
                    
            }
        },

        //----------------------------
        //inputタグのv-modelに設定する変数
        //----------------------------
        stepNameDisplay: {
            get() {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        return this.stepName;
                    case STEP_TYPE.STEP_EDIT:
                        return this.step.step_name;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        return this.step.sub_step_name;
                    default:
                        break;
                }
            },

            set(newValue) {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        this.stepName = newValue;
                        break;
                    case STEP_TYPE.STEP_EDIT:
                        this.step.step_name = newValue;
                        break;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        this.step.sub_step_name = newValue;
                        break;
                    default:
                        break;
                }
            }
        },

        stepDetailDisplay: {
            get() {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        return this.stepDetail;
                    case STEP_TYPE.STEP_EDIT:
                        return this.step.step_detail;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        return this.step.sub_step_detail;
                    default:
                        break;
                }
            },
            set(value) {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        this.stepDetail = value;
                        break
                    case STEP_TYPE.STEP_EDIT:
                        this.step.step_detail = value;
                        break
                    case STEP_TYPE.SUB_STEP_EDIT:
                        this.step.sub_step_detail = value;
                        break
                    default:
                        break;
                }
            }
        },

        stepImgDisplay: {
            get() {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        return this.stepUri;
                    case STEP_TYPE.STEP_EDIT:
                        return this.step.step_img;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        return this.step.sub_step_img;
                    default:
                        break;
                }
            },
            set(value) {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        this.stepUri = value;
                        break;
                    case STEP_TYPE.STEP_EDIT:
                        this.step.step_img = value;
                        break;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        this.step.sub_step_img = value;
                        break;
                    default:
                        break;
                }
            }
        },

        categoryDisplay: {
            get() {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        return this.categoryId;
                    case STEP_TYPE.STEP_EDIT:
                        return this.step.category_id;
                    default:
                        break;
                }
            },
            set(value) {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        this.categoryId = value;
                        break;
                    case STEP_TYPE.STEP_EDIT:
                        this.step.category_id = value;
                        break;
                    default:
                        break;
                }
            }
        },

        estimatedTimeDisplay: {
            get() {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        return this.estimatedTime;
                    case STEP_TYPE.STEP_EDIT:
                        return this.step.total_estimated_time;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        return this.step.estimated_time;
                    default:
                        break;
                }
            },
            set(value) {
                switch (this.type) {
                    case STEP_TYPE.STEP_CREATE:
                        this.estimatedTime = value;
                        break;
                    case STEP_TYPE.STEP_EDIT:
                        this.step.total_estimated_time = value;
                        break;
                    case STEP_TYPE.SUB_STEP_EDIT:
                        this.step.estimated_time = value;
                        break;
                    default:
                        break;
                }
            }
        },

        //エラー情報:STEP名
        nameError() {
            return (this.type === STEP_TYPE.STEP_CREATE || this.type === STEP_TYPE.STEP_EDIT)
                ? this.errors?.step_name || []
                : this.errors?.sub_step_name || [];
        },

        //エラー情報:目標達成時間
        EstimatedTimeError() {
            return (this.type === STEP_TYPE.STEP_CREATE || this.type === STEP_TYPE.STEP_EDIT)
                ? this.errors?.total_estimated_time || []
                : this.errors?.estimated_time || [];
        },

        //エラー情報:STEP詳細名
        detailError() {
            return (this.type === STEP_TYPE.STEP_CREATE || this.type === STEP_TYPE.STEP_EDIT)
                ? this.errors?.step_detail || []
                : this.errors?.sub_step_detail || [];
        },

        //エラー情報:STEP画像名
        imgError() {
            return (this.type === STEP_TYPE.STEP_CREATE || this.type === STEP_TYPE.STEP_EDIT)
                ? this.errors?.step_img || []
                : this.errors?.sub_step_img || [];
        },

    },

    mounted() {
        if (this.type === STEP_TYPE.STEP_EDIT || this.type === STEP_TYPE.SUB_STEP_EDIT) {
            this.getStep();
        }
    },

    methods: {
        
        //直前のルートから、ユーザ操作後の戻り先を決める
        decideNextRoute,


        //分を時間に換算
        minutesToHours,

        //Modal画面表示
        openModal(mode = null) {
            let msg = '';
            this.mode = mode;

            switch (this.type) {
                case STEP_TYPE.STEP_CREATE:
                    msg = 'STEPを登録しますか？';
                    break;
                case STEP_TYPE.STEP_EDIT:
                    if (mode === 'delete') {
                        msg = 'STEPを削除しますか？';
                    } else if (mode === 'restore') {
                        msg = 'STEPを復活させますか？';
                    } else {
                        msg = 'STEP編集内容を登録しますか？';
                    }
                    break;
                case STEP_TYPE.SUB_STEP_EDIT:
                    if (mode === 'delete') {
                        msg = '子STEPを削除しますか？';
                    } else if (mode === 'restore') {
                        msg = '子STEPを復活させますか？';
                    } else {
                        msg = '子STEP編集内容を登録しますか？';
                    }
                    break;
                default:
                    break;
            }

            this.showModal({
                text: msg,
            });
        },

        //親から渡されたidのSTEP情報を取得
        async getStep() {

            //STEP:Vuex登録してる親STEP ID、子STEP:propsのid
            const id = this.type === STEP_TYPE.STEP_EDIT? this.currentStep.id : this.id;

            const response = await axios.get(`/api/${this.url}/${id}`);
            this.step = response.data;
        },

        //STEP/子STEPの新規作成/編集内容をDBに渡す
        async submit() {
            //modal閉じる
            this.closeModal();
            
            switch (this.type) {
                case STEP_TYPE.STEP_CREATE:
                    this.createStep();
                    break;
                case STEP_TYPE.STEP_EDIT:
                    if (this.mode === 'delete') {
                        this.deleteStep();
                    } else if (this.mode === 'restore') {
                        this.restoreStep();
                    } else {
                        this.editStep();
                    }
                    break;
                case STEP_TYPE.SUB_STEP_EDIT:
                    if (this.mode === 'delete') {
                        this.deleteStep();
                    } else if (this.mode === 'restore') {
                        this.restoreSubStep();
                    } else {
                        this.editSubStep();
                    }
                    break;
                default:
                    break;
            }
        },

        //STEP新規作成
        async createStep() {
            const formData = new FormData();

            formData.append('step_name', this.stepName);
            formData.append('step_detail', this.stepDetail);
            formData.append('category_id', this.categoryId);

            if (this.estimatedTime) {
                formData.append('total_estimated_time', this.estimatedTime);
            }
            
            if (this.stepUri) {
                formData.append('step_img', this.stepUri);
            }
            
            try {
                const response = await axios.post(`/api/${this.url}`, formData);
                
                //STEP情報をVuexに登録
                this.$store.dispatch('currentStep/fetchCurrentStep', response.data);

                //フラッシュメッセージ
                this.$store.dispatch('flashMsg/showMsg',  'STEP登録しました。続けて子STEPを登録してください。');
                
                //当該STEPのもつ子STEP一覧へ遷移
                this.$router.push({ name: 'sub-step-list', params: {id: response.data.id} });
            } catch(e) {
                this.errors = e.response?.data?.errors || {};
                //modalを閉じる
                this.closeModal();

            }
        },

        //STEP編集
        async editStep() {
            const formData = new FormData();

            formData.append('step_name', this.step.step_name);
            formData.append('step_detail', this.step.step_detail || '');
            formData.append('category_id', this.step.category_id);

            if (this.step.total_estimated_time) {
                formData.append('total_estimated_time', this.step.total_estimated_time);
            }

            //登録画像変更有無
            if (this.step.step_img) {
                //画像変更あり
                if (typeof this.step.step_img === 'object') {
                    formData.append('step_img', this.step.step_img);
                } else {
                    //処置なし(画像アップロードおよび削除操作なしなので)
                }
            } else {
                //登録画像削除時
                formData.append('deleted_step_img', true);
            }

            //擬似要素追加
            formData.append('_method', 'PUT');

            try {
                const response = await axios.post(`/api/${this.url}/${this.currentStep.id}`, formData);
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);
                
                //画面遷移
                //(decideNextRouteは.jsであり、this.が使えないので、$routerを渡す)
                decideNextRoute(this.$router);

            } catch(e) {
                this.errors = e.response?.data?.errors || {};
                //modalを閉じる
                this.closeModal();
            }

        },

        //STEP編集
        async editSubStep() {
            const formData = new FormData();

            formData.append('sub_step_name', this.step.sub_step_name);
            formData.append('sub_step_detail', this.step.sub_step_detail || '');

            if (this.step.estimated_time) {
                formData.append('estimated_time', this.step.estimated_time);
            }

            //登録画像変更有無
            if (this.step.sub_step_img) {
                //画像変更あり
                if (typeof this.step.sub_step_img === 'object') {
                    formData.append('sub_step_img', this.step.sub_step_img);
                } else {
                    //処置なし(画像アップロードおよび削除操作なしなので)
                }
            } else {
                //登録画像削除時
                formData.append('delete_sub_step_img', true);
            }

            //擬似要素追加
            formData.append('_method', 'PUT');

            try {
                const response = await axios.post(`/api/${this.url}/${this.id}`, formData);
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);
                
                //子STEP一覧に戻る
                this.$router.push({ name: 'sub-step-list', params: {id: this.currentStep.id} });
            } catch(e) {
                this.errors = e.response?.data?.errors || {};
                //modalを閉じる
                this.closeModal();
            }

        },

        //当該STEP/子STEP削除
        async deleteStep() {
            //modal閉じる
            this.closeModal();
            
            if (this.type === STEP_TYPE.STEP_EDIT) {
                const response = await axios.delete(`/api/steps/${this.currentStep.id}`);
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                //削除識別子を削除
                this.mode = '';

                //カレントのSTEP Vuexとローカルストレージ削除
                this.$store.dispatch('currentStep/clearCurrentStepData');
                localStorage.removeItem(STORAGE_NAMES.STEP_ID);

                //マイページへ遷移
                this.$router.push({ name: 'mypage' });
            } else if (this.type === STEP_TYPE.SUB_STEP_EDIT) {
                const response = await axios.delete(`/api/sub_steps/${this.id}`);
                this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                //削除識別子を削除
                this.mode = '';

                //マイページへ遷移
                this.$router.push({ name: 'sub-step-list', params: {id: this.currentStep.id} });
            }

        },

        //削除したSTEPの復活処理
        async restoreStep() {
            //modal閉じる
            this.closeModal();

            const response = await axios.patch(`/api/step/${this.currentStep.id}/restore`);
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //削除識別子を削除
            this.mode = '';

            //マイページへ遷移
            this.$router.push({ name: 'mypage' });

        },

        //削除した子STEPの復活処理
        async restoreSubStep() {
            //modal閉じる
            this.closeModal();

            const response = await axios.patch(`/api/sub-step/${this.id}/restore`);
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //削除識別子を削除
            this.mode = '';

            //子STEP一覧へ遷移
            this.$router.push({ name: 'sub-step-list', params: {id: this.id} });

        }

    },
}
</script>