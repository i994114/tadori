<template>
<div>
    <!-- パンくずリスト -->
    <base-breadcrumb :type="STEP_TYPE.SUB_STEP_INDEX" />

    <h2 class="c-title c-title--outer">子STEP一覧</h2>

    <div class="c-mainArea">
        <div class="p-subStepList c-container">

            <!-- STEP名 -->
            <div class="p-subStepList__stepHeader" v-if="currentStep">
                <div class="p-subStepList__stepHeaderLabel">STEP名：</div>
                <span class="p-subStepList__stepHeaderName">{{ currentStep.step_name }}</span>
            </div>

            <!-- ツイッター領域 -->
            <div class="p-subStepList__caption">
                <!-- ツイッターボタン -->
                <a
                    v-if="!isDeleted"
                    :href="twitterShareUrl"
                    target="_blank"
                    rel="noopener"
                    >
                    <i class="fa-brands fa-x-twitter u-p-m"></i>
                </a>
            </div>

            <!-- 新規追加欄 -->
            <div v-if="isOwner" @submit.prevent="createSubStep" class="p-subStepList__inputArea">
                <!-- 入力部分 -->
                <div class="p-subStepList__inputArea--left">
                    <base-input
                        type="text"
                        v-model="newSubStepName"
                        :isAutofocus="true"
                        :isCountable="true"
                        placeholder="例：「STEP1：単語帳で基礎を固めよう」"
                        :maxLength="MAX_LENGTH.STEP_NAME"
                        :errors="errors['sub_step_name']"
                    ></base-input>
                </div> 

                <!-- 追加ボタン -->
                <div class="p-subStepList__inputArea--right c-hover">
                    <i class="fa-solid fa-plus" @click="createSubStep"></i>
                </div>

            </div>

            <!-- 子STEP一覧 -->
            <div class="p-subStepList__list">
                <!-- 子STEP0件 -->
                <div v-if="subSteps.length === 0">子STAPはありません</div>

                <!-- ⚠ 論理削除が含まれる場合の注意文 -->
                <p
                    v-if="hasDeletedSubStep"
                    class="p-subStepList__note p-subStepList__note--warn"
                >
                    ※削除された子STEPは進捗率に反映されません
                </p>

                <!-- 子STEPあり -->
                <p class="p-subStepList__note" v-if="shouldShowDragNote" >ドラッグ＆ドロップで入れ替えできます</p>
                <draggable v-model="subSteps" @end="onDragEnd" :disabled="!isOwner">
                    <div
                        v-for="(subStep, index) in subSteps"
                        :key="subStep.id"
                        class="p-subStepList__listRow"
                    >
                        <!-- 子STEP名 -->
                        <router-link class="p-subStepList__stepInfo" :to="{ name: 'sub-step-show', params: { id: subStep.id } }">
                            <span class="p-subStepList__badge">子STEP {{ index + 1 }}</span>
                            <div class="p-subStepList__stepTitle">
                                {{ showDeletedStatus(subStep.deleted_at) }}{{ subStep.sub_step_name }}
                            </div>
                        </router-link>

                        <!-- 操作 -->
                        <div class="p-subStepList__actionArea">
                            <i
                                v-if="isOwner"
                                class="far fa-edit icon-btn"
                                @click="editSubStep(subStep.id)"
                                title="編集"
                            ></i>

                            <button
                                v-if="canShowClearCancelBtn(subStep)"
                                class="p-subStepList__clearBtn p-subStepList__clearBtn--done c-btn c-btn--toggleSmall"
                                type="button"
                                @click="deleteProgress(subStep.userProgress[0].id)"
                            >
                                <i class="fa-solid fa-xmark"></i>cleared!
                            </button>

                            <button
                                v-else-if="canShowClearBtn(subStep)"
                                class="p-subStepList__clearBtn p-subStepList__clearBtn--doing c-btn c-btn--toggleSmall"
                                type="button"
                                @click="createProgress(subStep.id)"
                            >
                                <i class="fa-solid fa-check"></i>clear!
                            </button>
                        </div>

                    </div>
                </draggable>
            </div>

            <!-- 並び順保存 -->
            <div class="p-subStepList__sortBtnArea">
                <button v-if="isOwner" class="c-btn c-btn--short" @click="saveOrder">
                    並び順を保存
                </button>
            </div>

        </div>
    </div>
</div>
</template>
<script>
import { mapGetters, mapState } from 'vuex';
import { showDeletedStatus } from '../common';
import { MAX_LENGTH, STEP_TYPE, STORAGE_NAMES } from '../const';

import router from '../router';
import BaseBreadcrumb from './BaseBreadcrumb.vue';

export default {

    data() {
        return {
            MAX_LENGTH,
            STEP_TYPE,

            newSubStepName: '',
            subSteps: [],
            stepId: '',
            myChallenge: [],
            errors: [],

        };
    }, 

    mounted() {
        // currentStep がすでに存在する場合は即時処理
        if (this.currentStep) {
            this.initSubStep();
        } 
    },

    computed: {
        ...mapState({    
            currentStep: state => state.currentStep.currentStep,
        }),

        ...mapGetters('user', [
            'authUser',  //ログインユーザ情報
        ]),

        //当該STEPのオーナーか
        isOwner() {
            if (!this.authUser || !this.currentStep) return false;
            return this.currentStep? this.currentStep.owner_id === this.authUser.id : false;
        },

        //論理削除された子STEPが1つでもあるか
        hasDeletedSubStep() {
            return this.subSteps.some(subStep => subStep.deleted_at !== null);
        },

        //ドラッグ案内を表示するか
        shouldShowDragNote() {
            return this.isOwner && this.subSteps.length > 0;
        },

        //当該STEPは論理削除されていないか(されていないならツイッター可能)
        isDeleted() {
            return !!this.currentStep.deleted_at
        },

        // ツイッターの投稿内容の生成
        twitterShareText() {

            return encodeURIComponent(
                `📚「${this.currentStep.step_name}」にチャレンジ中！！\n\n他の人の学びのSTEPから、自分に合った成長の道を見つけよう！\n\n#STEP #成長の記録\n\n`
            );
        },

        //ツイッターでシャアするURLの生成
        twitterShareUrl() {
            const pageUrl = encodeURIComponent(this.getCurrentPageURL());
            return `https://twitter.com/intent/tweet?text=${this.twitterShareText}&url=${pageUrl}`;
        },

    },

    methods: {

        //削除済みSTEPか否かの表示用
        showDeletedStatus,

        //表示データ読み出し
        async initSubStep() {
            // 当該STEPのもつ子STEP、およびそれに対するログインユーザの進捗を取得
            await this.userSubStepProgress();

            // 当該STEPに対するログインユーザのチャレンジ情報取得
            if (this.authUser) {
                await this.getMyChlallenge();
            }
            
        },

        //当該STEPのもつ子STEP、およびそれに対するログインユーザの進捗を取得
        async userSubStepProgress() {
            const response = await axios.get(`/api/step/${this.currentStep.id}/user/progresses`);
            this.subSteps = response.data;
        },

        //当該STEPに対するログインユーザのチャレンジ情報取得
        async getMyChlallenge() {
            const response = await axios.get(`/api/user/step/${this.currentStep.id}/challenge`); 
            this.myChallenge = response.data;
        },

        //クリアキャンセルボタン表示可否
        canShowClearCancelBtn(subStep) {
            return  !this.isOwner && subStep.userProgress && subStep.userProgress.length > 0;
        },

        //クリアボタン表示可否
        canShowClearBtn( ) {
            if (!this.authUser) return;
            //オーナーでない、かつチャレンジ中、かつチャレンジデータのユーザIDがログインユーザと一致するか
            return !this.isOwner && this.myChallenge && this.myChallenge.user_id === this.authUser.id;
        },

        //並べ替えした子STEPの並び順を更新する
        onDragEnd() {
            this.subSteps.forEach((subStep, index) => {
                subStep.order_no = index + 1;
            });
        },

        //子STEP作成
        async createSubStep() {
            try {
                const response = await axios.post('/api/sub_steps', {
                    sub_step_name: this.newSubStepName,
                    step_id: this.currentStep.id,
                });

                //入力欄とエラー欄クリア
                this.newSubStepName = "";
                this.errors = '';

                this.$store.dispatch('flashMsg/showMsg', '子STEPを登録しました。');

                //子STEP一覧再取得
                await this.userSubStepProgress();
            } catch(e) {
                this.errors = e.response.data.errors;
                this.$store.dispatch('flashMsg/showMsg', '子STEP登録に失敗しました。エラー表示を確認してください。');
            }
        },

        //子STEP編集
        editSubStep(id) {
            router.push({ name: 'sub-step-edit', params: { id: id } });
        },

        //子STEPの並びをDBに保存
        async saveOrder() {
            const response = await axios.patch('/api/sub-step/update-order', {
                subSteps: this.subSteps.map(s => ({ id: s.id, no: s.order_no })) 
            });
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);
        },
        
        //子STEPクリア(達成済み)処理
        async createProgress(id) {
            
            const response = await axios.post('/api/progresses', {
                stepId: this.currentStep.id,
                subStepId: id,
            });

            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //子STGEP一覧を再取得
            await this.userSubStepProgress();
        },

        //子STEPクリアキャンセル処理
        async deleteProgress(id) {
            const response = await axios.delete(`/api/progresses/${id}`);

            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //子STGEP一覧を再取得
            await this.userSubStepProgress();
        },

        //現在のファイルパスを取得
        getCurrentPageURL() {
            return window.location.href;
        }
    }


};
</script>
