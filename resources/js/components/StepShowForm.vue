<template>
<div>
    <!-- パンくずリスト -->
    <base-breadcrumb
        :type="type"
        :subStepId="id"
    ></base-breadcrumb>

    <h2 class="c-title c-title--inner">{{title}}詳細</h2>

    <div class="c-mainArea">
        <div v-if="step && Object.keys(step).length > 0" class="p-stepShow c-container">

            <!-- STEP名 -->
            <div class="p-stepShow__item">
                <div class="p-stepShow__stepHead">

                    <div class="p-stepShow__stepHead--left">
                        <!-- タイトル -->
                        <h3 class="p-stepShow__itemTitle p-stepShow__intro">{{ title }}名:</h3>

                        <!-- カテゴリ -->
                        <div class="p-stepShow__category">
                            <span class="p-stepShow__categoryName p-stepShow__intro">{{ step.category ? step.category.category_name : '' }}</span>
                        </div>
                    </div>
        
                    <div class="p-stepShow__stepHead--right">
                        <!-- 編集リンク -->
                        <router-link v-if="isOwner" :to="{name: next, params: {id: id}}"><i class="far fa-edit"></i></router-link>
                    </div>

                </div>
                <!-- STEP名 -->
                <p class="p-stepShow__stepName p-stepShow__intro">{{ showDeletedStatus(step.deleted_at) }}{{ displayStepName }}</p>
            </div>

            <!-- STEP詳細 -->
            <div class="p-stepShow__detail p-stepShow__item">
                <h3>{{ title }}詳細:</h3>
                <p>{{ displayStepDetail }}</p>
            </div>

            <!-- 目安達成時間 -->
            <div class="p-stepShow__detail p-stepShow__item">
                <h3>目安達成時間:</h3>
                <p>{{ minutesToHours(displayEstimatedTime) }}</p>
            </div>

            <!-- STEP画像 -->
            <div class="p-stepShow__item" v-if="displayStepImg">
                <h3>{{ title }}画像:</h3>
                <img class="p-stepShow__image" :src="displayStepImg ? `/storage/uploads/${displayStepImg}` : ''" alt="STEP image" />

            </div>

            <div class="p-stepShow__caption">
                <!-- 作成日時 -->
                <div class="p-stepShow__createdAt">
                    <h3>STEP作成日:</h3>
                    <span>{{ removeTime(step.created_at) }}</span>
                </div>

                <!-- オーナー情報 -->
                <div class="p-stepShow__owner p-stepShow__item">
                    <img
                        class="c-avatar c-avatar--primary"
                        :src="step.owner && step.owner.user_img ? `/storage/uploads/${step.owner.user_img}` : ''"
                        alt="owner image"
                    />
                    <router-link
                        v-if="step.owner"
                        :to="{ name: 'user-show', params: { id: step.owner.id } }"
                        class="p-stepShow__ownerName c-hover"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                    {{ step.owner ? step.owner.name : '' }}
                    </router-link>
                </div>
            </div>

            <!-- チャレンジ/キャンセルボタン -->
            <div v-if="!isOwner" class="c-area c-area__deleteArea">
                <button  v-if="canChallenge" class="c-btn c-btn--primary" @click="storeChallenge">チャレンジする</button>
                <button  v-else-if="canChallengeCancel" class="c-btn c-btn--danger" @click="openModal"><i class="fas fa-times c-hover">チャレンジをキャンセルする</i></button>
            </div>

            <!-- Modal -->
            <div v-if="modal.show">
                <base-modal
                    :title="modal.title"
                    :text="modal.text"
                    @confirm="cancelStep"
                    @cancel="closeModal"
                ></base-modal>
            </div>

            <div class="p-stepShow__userOptions">
                <!-- お気に入り登録 -->
                <div v-if="isFavorited">
                    <i class="fa-solid fa-star  c-hover u-p-m" @click="deleteFavorite" ></i>
                </div>
                <div v-else-if="!isDeleted">
                    <i class="fa-regular fa-star c-hover u-p-m" @click="createFavorite"></i>
                </div>

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

            <!-- 子STEPリストリンク -->
            <div class="c-linkSentence c-linkSentence--expand">
                <router-link class="c-linkSentence__word" :to="{ name: 'sub-step-list', params: {id: id} }">
                    子STEP一覧をみる<i class="fa-solid fa-angles-right"></i>
                </router-link>
            </div>

        </div>
        <div v-else>...読み込み中</div>

        <!-- サイドバー -->
        <sidebar-right-component></sidebar-right-component>
    </div>
</div>
</template>
<script>
import { mapGetters, mapState } from 'vuex';
import { STEP_TYPE } from '../const';
import { decideNextRoute, minutesToHours, removeTime, showDeletedStatus } from '../common';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],
    
    props: {
        id: [String, Number],
        title: String,
        type: Number,
        url: String,
        next: String,
    },

    data() {
        return {
            STEP_TYPE,

            localId: this.id,
            step: {},
            myChallenge: [],   //ログインユーザのチャレンジ情報

            challengeId: '',
            favoritedId: '', //当該STEPのお気に入りID
        };
    },

    async mounted() {
        this.getData();
    },

    computed: {

        ...mapGetters('user', [
            'authUser',  //ログインユーザ情報
            'isAuthenticated', //ログイン済みか
        ]),

        ...mapState({
            currentStep: state => state.currentStep.currentStep,
        }),

        // 当該STEPのオーナーか
        isOwner() {
            if (!this.authUser || !this.currentStep) return false;
            return this.currentStep && this.currentStep.owner_id === this.authUser.id;
        },


        //チャレンジ可能か
        canChallenge() {
            //現在の画面がSTEP詳細か
            if (this.type !== STEP_TYPE.STEP_SHOW) {
                return false;
            }

            //ログイン済みか
            if (!this.isAuthenticated) {
                return false;
            }

            //当該STEPのオーナーでないか
            if (this.step.owner_id === this.authUser.id) {
                return false;
            }

            //すでにチャレンジ済でないか
            if (this.myChallenge && Object.keys(this.myChallenge).length > 0) {
                return false;
            }

            return  true;
        },

        //チャレンジキャンセル可能か
        canChallengeCancel() {
            
            //現在の画面がSTEP詳細以外か
            if (this.type !== STEP_TYPE.STEP_SHOW) {
                return false;
            }

            //ログイン済みでないか
            if (!this.isAuthenticated) {
                return false;
            }
            
            //チャレンジしてないか
            if (!this.myChallenge || Object.keys(this.myChallenge).length === 0) {
                return false;
            }

            return true;
        },

        // ツイッターの投稿内容の生成
        twitterShareText() {
            if (!this.step) return '';

            const title = this.step.step_name;

            return encodeURIComponent(
                `📚「${title}」にチャレンジしてみませんか？\n\n他の人の学びのSTEPから、自分に合った成長の道を見つけよう！\n\n#STEP #成長の記録\n\n`
            );
        },

        //ツイッターでシャアするURLの生成
        twitterShareUrl() {
            const pageUrl = encodeURIComponent(this.getCurrentPageURL());
            return `https://twitter.com/intent/tweet?text=${this.twitterShareText}&url=${pageUrl}`;
        },

        //当該STEPをお気に入り登録済みか否か
        isFavorited() {
            return this.favoritedId;
        },

        //当該STEPは論理削除されていないか(されていないならお気に入り登録、ツイッター可能)
        isDeleted() {
            return !!this.step.deleted_at
        },

        //STEP/子STEP表示切り替え:STEP名
        displayStepName() {
            if (!this.step) return '';
            if (this.url === 'steps') {
                return this.step.step_name || '';
            } else if (this.url === 'sub_steps') {
                return this.step.sub_step_name || '';
            }
        },

        //STEP/子STEP表示切り替え:STEP詳細
        displayStepDetail() {
            if (!this.step) return '';
            if (this.url === 'steps') {
                return this.step.step_detail || '';
            } else if (this.url === 'sub_steps') {
                return this.step.sub_step_detail || '';
            }
        },

        //STEP/子STEP表示切り替え:STEP画像
        displayStepImg() {
            if (!this.step) return '';
            if (this.type === STEP_TYPE.STEP_SHOW) {
                return this.step.step_img || '';
            } else if (this.type === STEP_TYPE.SUB_STEP_SHOW) {
                return this.step.sub_step_img || '';
            }
        },

        //STEP/子STEP表示切り替え:目安達成時間
        displayEstimatedTime() {
            if (!this.step) return '';
            if (this.type === STEP_TYPE.STEP_SHOW) {
                return this.step.total_estimated_time || '';
            } else if (this.type === STEP_TYPE.SUB_STEP_SHOW) {
                return this.step.estimated_time || '';
            }
        }

    },

    watch: {

        //サイドバー右のstep showをクリックしたとき、いまstep showmにいても画面切り替わるための処理
        '$route.params.id': {
            immediate: true, 
            handler(newId) {
                this.localId = newId;
                this.getData();
            }
        }
    },

    methods: {

        minutesToHours,
        removeTime,
        showDeletedStatus,
        
        //Modal画面表示
        openModal() {
            let msg = 'チャレンジをキャンセルしますか？';

            this.showModal({
                text: msg
            });
        },

        //直前のルートから、ユーザ操作後の戻り先を決める
        decideNextRoute,

        //画面開いた時の初期化処理一式
        async getData() {
            await this.getStep();

            //開く画面は詳細画面でログイン済みか
            if (this.type === STEP_TYPE.STEP_SHOW && this.authUser) {
                await this.getMyChlallenge();
            }

            //ログイン済ならお気に入り取得
            if (this.authUser) {
                await this.getUserFavorites();
            }
        },

        //当該STEP情報を取得
        async getStep() {
            const response = await axios.get(`/api/${this.url}/${this.localId}`);
            this.step = response.data;
        },

        //当該STEPに対するログインユーザのチャレンジ情報取得
        async getMyChlallenge() {
            if (!this.currentStep || !this.currentStep.id) return;
            const response = await axios.get(`/api/user/step/${this.currentStep.id}/challenge`); 
            this.myChallenge = response.data;
        },

        //当該ユーザのお気に入り情報を取得
        async getUserFavorites() {
            if (!this.currentStep || !this.currentStep.id) return;
            const response = await axios.get('/api/user/favorites');
            
            this.favorites = response.data;

            //当該STEPをお気に入り登録しているか
            const exists = this.favorites.filter(step => {
                return step.pivot && this.authUser ? step.pivot.step_id === this.currentStep.id && step.pivot.user_id === this.authUser.id : false;
            });
            
            if (exists.length >= 1) {
                this.favoritedId = exists[0].pivot.id;
            } else {
                this.favoritedId = '';
            }
        },
        
        //チャレンジ記録を保存
        async storeChallenge() {
            const response = await axios.post('/api/challenges', {
                'step_id': this.currentStep.id,
                'challenge_id': this.myChallenge.id,
            });
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //チャレンジ⇔キャンセルボタン切り替え
            await this.getMyChlallenge();
        },

        //チャレンジキャンセル処理
        async cancelStep() {
            //modal閉じる
            this.closeModal();
            
            const response = await axios.delete(`/api/challenges/${this.myChallenge.id}`);
            
            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //画面遷移
            //(decideNextRouteは.jsであり、this.が使えないので、$routerを渡す)
            decideNextRoute(this.$router);
        },

        //お気に入り登録
        async createFavorite() {
            const response = await axios.post('/api/favorites', {
                step_id: this.currentStep.id,
                user_id: this.authUser.id,
            });
            this.$store.dispatch('flashMsg/showMsg', 'お気に入り登録しました。');
            this.favoritedId = response.data.id;

        },

        //お気に入り削除
        async deleteFavorite() {
            const response = await axios.delete(`/api/favorites/${this.favoritedId}`);
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);
            this.favoritedId = '';
        },

        //現在のファイルパスを取得
        getCurrentPageURL() {
            return window.location.href;
        }
    }
}
</script>
