<template>
<div>

    <!-- フラッシュメッセージ -->
    <flash-msg />

    <h2 class="c-title c-title--outer">マイページ</h2>
    <div class="c-mainArea">
        <div class="c-container">
            <!-- =========== -->
            <!-- 投稿したSTEP -->
            <!-- =========== -->

            <!-- 見出し -->
            <h3 class="c-title c-title--medium">
                <i class="fa-solid fa-sticky-note"></i>
                 投稿したSTEP(最新5件)
            </h3>

            <!-- STEP一覧 -->
            <div v-if="recentPostedSteps.length" class="c-stepList">
                <div class="c-stepList__step" v-for="step in recentPostedSteps" :key="'step-' + step.id">
                    <div class="c-stepList__item c-hover" @click="goStepShow(step)">
                        <!-- STEP名、カテゴリ -->
                        <div class="c-stepList__itemHeader">
                            <h3 class="c-stepList__title">{{ showDeletedStatus(step.deleted_at) }}{{ step.step_name }}</h3>
                            <span class="c-stepList__category c-stepList__category--stretch">{{ step.category ? step.category.category_name : '' }}</span>
                        </div>

                        <!-- 目安達成時間 -->
                        <p class="c-stepList__time">目安達成時間： {{ minutesToHours(step.total_estimated_time) }}</p>

                    </div>

                    <!-- STEP編集ボタン -->
                    <div class="c-stepList__icon">
                        <i class="far fa-edit c-hover" @click="editStep(step)"></i>
                    </div>


                </div>
            </div>
            <div v-else>まだありません</div>

            <!-- 全件表示ボタン -->
            <div v-if="recentPostedSteps.length" class="u-tr-c">
                <button class="c-btn c-btn--short " @click="moveIndexs('posted')" >全件表示</button>
            </div>	

            <!-- =============== -->
            <!-- チャレンジしたSTEP -->
            <!-- =============== -->

            <!-- 見出し -->
            <h3 class="c-title c-title--medium u-mt-4l">
                <i class="fa-solid fa-mountain"></i>
                 チャレンジしたSTEP(最新5件)
            </h3>

            <!-- STEP一覧 -->
            <div v-if="recentChallengedSteps.length" class="c-stepList">
                <div class="c-stepList__step" v-for="step in recentChallengedSteps" :key="'step-' + step.id">
                    <div class="c-stepList__item c-hover" @click="goStepShow(step)">

                        <!-- STEP名、カテゴリ -->
                        <div class="c-stepList__itemHeader">
                            <h3 class="c-stepList__title">{{ showDeletedStatus(step.deleted_at) }}{{ step.step_name }}</h3>
                            <span class="c-stepList__category c-stepList__category--stretch">{{ step.category ? step.category.category_name : '' }}</span>
                        </div>

                        <!-- 目安達成時間 -->
                        <p class="c-stepList__time">目安達成時間： {{ minutesToHours(step.total_estimated_time) }}</p>

                        <!-- 進捗バー -->
                        <div class="c-stepList__progressBar c-progressBar">
                            <div class="c-progressBar__progressRate" :style="{ width: step.progress_count.rate + '%' }"></div>
                        </div>
                        <span class="c-progressBar__progressCount">{{ step.progress_count.cleared }} / {{ step.progress_count.total }}件</span>
                    </div>

                    <!-- チャレンジキャンセルボタン -->
                    <div class="c-stepList__icon">
                        <i class="fas fa-times c-hover" @click="handleClick(step.pivot.id)"></i>
                    </div>

                </div>
                <!-- modal -->
                <base-modal
                    v-if="modal.show"
                    :text="modal.text"
                    @confirm="cancelChallenge"
                    @cancel="closeModal"
                />
            </div>
            <div v-else>まだありません</div>

            <!-- 全件表示ボタン -->
            <div v-if="recentChallengedSteps.length" class="u-tr-c">
                <button class="c-btn c-btn--short " @click="moveIndexs('challenged')" >全件表示</button>
            </div>	

            <!-- =============== -->
            <!-- お気に入りしたSTEP -->
            <!-- =============== -->

            <!-- 見出し -->
            <h3 class="c-title c-title--medium u-mt-4l">
                <i class="fa-solid fa-bookmark"></i>
                 気になるSTEP(最新5件)
            </h3>

            <!-- STEP一覧 -->
            <div v-if="recentFavorites.length"  class="c-stepList">
                <div class="c-stepList__step" v-for="step in recentFavorites" :key="'favorite-' + step.id">
                    <div class="c-stepList__item c-hover" @click="goStepShow(step)">

                        <!-- STEP名、カテゴリ -->
                        <div class="c-stepList__itemHeader">
                            <h3 class="c-stepList__title">{{ step.step_name }}</h3>
                            <span class="c-stepList__category c-stepList__category--stretch">{{ step.category ? step.category.category_name : '' }}</span>
                        </div>

                        <!-- 目安達成時間 -->
                        <p class="c-stepList__time">目安達成時間： {{ minutesToHours(step.total_estimated_time) }}</p>

                    </div>

                    <!-- お気に入り解除ボタン -->
                    <div class="c-stepList__icon">
                        <i class="fa-solid fa-star c-hover"  @click="cancelFavorite(step.pivot.id)" ></i>
                    </div>
                </div>
            </div>
            <div v-else>まだありません</div>
            
            <!-- 全件表示ボタン -->
            <div v-if="recentFavorites.length" class="u-tr-c">
                <button class="c-btn c-btn--short " @click="moveIndexs('favorited')" >全件表示</button>
            </div>	

        </div>
        <!-- サイドバー -->
        <sidebar-right-component
            type="mypage"
        ></sidebar-right-component>
    </div>
</div>
</template>
<script>
import { goStepShow, minutesToHours, removeTime, showDeletedStatus } from '../common';
import { MAX_LENGTH } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],

    data() {
        return {
            MAX_LENGTH,
            
            postedSteps: [],
            challengedSteps: [],
            favorites: [],
            modalTargetStepId: null,  //modalではいを押したときにチャレンジをやめるSTEP情報
        };
    },

    mounted() {
        this.getMyPostedSteps();
        this.getMyChallengedSteps();
        this.getMyFavorites();
    },

    computed: {

        //最新5件の投稿したSTEP
        recentPostedSteps() {
            return this.postedSteps.slice(0, 5);
        },

        //最新5件のチャレンジしているSTEP
        recentChallengedSteps() {
            return this.challengedSteps.slice(0, 5);
        },

        //最新5件の気になるSTEP
        recentFavorites() {
            return this.favorites.slice(0, 5);
        },

    },

    methods: {
        //タイムスタンプから時間を消す
        removeTime,

        //分から時間換算
        minutesToHours,

        //STEP削除済みか否かを判定し、STEP表示の際に当該STEPが削除済みかわかるようにする
        showDeletedStatus,

        //詳細ページに遷移
        goStepShow,

        //投稿したSTEPを取得
        async getMyPostedSteps() {
            const response = await axios.get('/api/user/posted-steps');
            this.postedSteps = response.data;
        },

        //チャレンジしたSTEPを取得
        async getMyChallengedSteps() {
            const response = await axios.get('/api/user/challenges');
            this.challengedSteps = response.data;
        },

        //じぶんの気になる一覧
        async getMyFavorites() {
            const response = await axios.get('/api/user/favorites');
            this.favorites = response.data;
        },

        //全件表示ページ遷移
        moveIndexs(type) {
            switch (type) {
                case 'posted':
                    this.$router.push({ name: 'mypage-posted' });
                    break;
                case 'challenged':
                    this.$router.push({ name: 'mypage-challenged' });
                    break;
                case 'favorited':
                    this.$router.push({ name: 'mypage-favorited' });
                    break;

                default:
                    break;
            }
        },

        //STEP編集
        editStep(step) {
            
            //STEP情報をVuexに登録
            this.$store.dispatch('currentStep/fetchCurrentStep', step );
            
            //編集画面へ遷移
            this.$router.push({ name: 'step-edit', params: { id: step.id } });
        },

        //チャレンジキャンセルボタンクリック時のmodalよびだし
        handleClick(id) {
            //当該STEP情報を保存しておく
            this.modalTargetStepId = id;

            //modalひらく
            this.showModal({ text: 'STEPチャレンジをやめますか？' });
        },

        //チャレンジキャンセル処理
        async cancelChallenge() {
            const response = await axios.delete(`/api/challenges/${this.modalTargetStepId}`);
            
            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //modal閉じる
            this.closeModal();
            this.modalTargetStepId = null;

            //画面更新のため、再度Step取得
            await this.getMyChallengedSteps();
        },

        //お気に入り解除
        async cancelFavorite(id) {
            const response = await axios.delete(`/api/favorites/${id}`);
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);
            
            //一覧再表示
            this.getMyFavorites();
        },
    }
}
</script>
