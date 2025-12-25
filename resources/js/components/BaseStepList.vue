<template>
<div>
    <h2 class="c-title c-title--outer">{{ title }}</h2>
    <div class="c-mainArea">

        <!-- サイドバー左 -->
        <sidebar-left
            v-model="selectedSort"
            @search="searchSteps"
        ></sidebar-left>

        <div class="c-container">
            <!-- 文字列検索 -->
            <base-search-input
                v-model="keyword"
                @search="searchSteps"
            ></base-search-input>

            <!--表示件数 -->
            <div class="c-searchTitle">
                <div class="c-searchTitle__left">
                    <span class="c-searchTitle__totalNum">{{ totalSteps }}</span>件のSTEPが見つかりました
                </div>
                <div v-if="totalSteps !== 0" class="c-searchTitle__right">
                    <!-- 表示件数 -->
                    {{ currentStart }}/{{ currentEnd }} {{ totalSteps }}件中
                </div>
            </div>

            <!-- STEP一覧 -->
            <div class="c-stepList">
                
                <!-- データ取得中 -->
                <div v-if="!loaded">...読み込み中</div>
                
                <!-- STEP0件 -->
                <div v-else-if="paginatedSteps.length === 0">まだありません</div>

                <!-- STEPあり -->
                <div 
                    v-for="step in paginatedSteps" 
                    :key="step.id"
                >
                    <div class="c-stepList__item c-hover" @click="goStepShow(step)">

                        <!-- STEP名、カテゴリ -->
                        <div class="c-stepList__itemHeader">
                            <div class="c-stepList__itemHeader--left">
                                <h3 class="c-stepList__title">{{ showDeletedStatus(step.deleted_at) }}{{ step.step_name }}</h3>
                            </div>

                            <div class="c-stepList__itemHeader--right">
                                <span class="c-stepList__category">{{ step.category_name }}</span>
                            </div>
                        </div>

                        <!-- 目安達成時間 -->
                        <p class="c-stepList__time">目安達成時間： {{ minutesToHours(step.total_estimated_time) }}</p>
                        
                        <!-- チャレンジ数 -->
                        <p v-if="step.challenges_count > 0" class="c-stepList__challenges">
                            <i class="fas fa-users"></i> {{ step.challenges_count }}人がチャレンジ中
                        </p>

                        <!-- 投稿日、投稿ユーザ情報 -->
                        <div class="c-stepList__foot">
                            <p class="c-stepList__date">投稿日: {{ removeTime(step.created_at) }}</p>
                            <div class="c-stepList__ownerInfo">
                                <img :src="`/storage/uploads/${step.owner.user_img}`" alt="" class="c-avatar c-avatar--primary" >
                                <p class="c-stepList__ownerName">{{ step.owner.name }}</p>
                            </div>
                        </div>
                        
                        <!-- 進捗バー -->
                        <div  v-if="canProgressBar(step)" class="c-stepList__progressBar c-progressBar">
                            <div class="c-progressBar__progressRate" :style="{ width: step.progress_count ? step.progress_count.rate + '%' : '0%' }"></div>
                        </div>
                        <span v-if="canProgressBar(step)" class="c-progressBar__progressCount">
                            {{ step.progress_count ? step.progress_count.cleared + ' / ' + step.progress_count.total + '件' : '' }}
                        </span>

                    </div>

                    <!-- 表示アイコン -->
                    <div class="c-stepList__icon">
                        <i v-if="canBtnShow" :class="step.action ? step.action.icon : ''" @click.stop="handleClick(step)"></i>
                    </div>
                </div>
            </div>

            <!-- ページネーション -->
            <paginate
                ref="paginate"
                :key="forcePage"
                :page-count="pageCount"
                :page-range="3"
                :margin-pages="2"
                :click-handler="handlePageChange"
                :prev-text="'<'"
                :next-text="'>'"
                :container-class="'c-pagination'"
                :page-class="'c-pagination__pageItem'"
                :prev-class="'c-pagination__pageItem'"
                :next-class="'c-pagination__pageItem'"
                :active-class="'c-pagination--active'"
                :hide-prev-next="true"
                :force-page="Number(forcePage)"
            />

            <!-- modal -->
            <base-modal
                v-if="modal.show"
                :text="modal.text"
                @confirm="cancelChallenge"
                @cancel="closeModal"
            />

        </div>
    </div>
</div>
</template>


<script>
import { goStepShow, minutesToHours, removeTime, showDeletedStatus } from '../common';
import { MAX_LENGTH } from '../const';
import modalMixin from '../modalMixin';
import pagination from '../pagination';
import stepListMixin from '../stepListMixin';

export default {
    mixins: [pagination, stepListMixin, modalMixin],
    props: {
        title: String,
        listType: String,
        url: String,    //一覧表示するSTEP情報の取得URL
        formatter: Function,
    },

    data() {
        return {
            MAX_LENGTH,

            selectedSort: {},
            keyword: '',
            loaded: false,  //表示するSTEPデータの読み込み完了有無(true:完了)
            modalTargetStep: null,    //modalではいを押したときにチャレンジをやめるSTEP情報
        };
    },

    computed: {
        
        //一覧にボタンを表示するか否か
        canBtnShow() {
            return Boolean(this.listType);
        },

    },

    mounted() {

        //ページ番号(STEP詳細からブラウザバックでもとのページ表示するための処理)
        this.currentPage = Number(this.$route.query.page) || 1;
        
        //検索条件(STEP詳細からブラウザバックでもとのページ表示するための処理)
        this.selectedSort = {
            category: this.$route.query.category || '',
            selectedEstimatedTime: this.$route.query.selectedEstimatedTime || '',
            sort: this.$route.query.sort || '',
        };

        this.keyword = this.$route.query.keyword || '';
        
        //検索条件をバックエンドに渡し、検索結果を受け取る(初回の検索条件なしでも)
        this.searchSteps(false);
        
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

        //親にemitする情報(どの画面でどのボタンを表示するかの情報)を識別する
        handleClick(step) {
            
            if (this.listType === 'posted') {
                //投稿したSTEP編集
                this.$emit('edit-step', step);
            } else if (this.listType === 'challenged') {
                //当該STEP情報を保存しておく
                this.modalTargetStep = step;
                //modalひらく
                this.showModal({ text: 'STEPチャレンジをやめますか？' });
            } else if (this.listType === 'favorited') {
                //お気に入りキャンセル
                this.$emit('cancel-favorite', step.link_id);
            }
        },

        //進捗を表示するか否か
        canProgressBar(step) {
            return this.listType && this.listType === 'challenged' && step.progress_count;
        },

        //modalからチャレンジキャンセル処理へ遷移
        cancelChallenge() {
            if (!this.modalTargetStep) return;
            this.$emit('cancel-challenge', this.modalTargetStep.link_id);
            this.closeModal();
            this.modalTargetStep = null;
        },

        //画面ごとに表示するアイコン情報を返す
        getActionByListType() {
            if (this.listType === 'posted') {
                return { event: 'edit-step', icon: 'far fa-edit c-hover' };
            } else if (this.listType === 'challenged') {
                return { event: 'cancel-challenge', icon: 'fas fa-times c-hover' };
            } else if (this.listType === 'favorited') {
                return { event: 'cancel-favorite', icon: 'fa-solid fa-star c-hover' };
            }
            return { event: '', icon: '' };
        },
    }

};
</script>
