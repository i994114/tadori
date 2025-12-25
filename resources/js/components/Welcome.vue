<template>
<div class="p-welcome">
    <!-- ヒーロー画像 -->
    <section class="p-welcome__hero">
        <div class="p-welcome__heroContent">
            <h1 class="p-welcome__heroTitle">STEP</h1>
            <p class="p-welcome__heroSub">あなたの人生のSTEPを共有しよう</p>
        </div>
    </section>

    <!-- 悩み -->
    <section class="p-welcome__problemArea p-welcome__problemArea--dark">
        <div class="p-welcome__main">
            <h2 class="p-welcome__sectionHead p-welcome__sectionHead--light">こんなお悩みありませんか？</h2>

            <div class="p-welcome__problemLists">

                <div class="p-welcome__problemList p-welcome__problemList--unhighlighted">
                    <img class="p-welcome__problemImg" src="/storage/uploads/top_problem1_svqxdpsvqxdpsvqx.png" alt="学習方法が多すぎて、何から始めればいいかわからない">
                    <h3 class="p-welcome__listHead p-welcome__listHead--unhighlighted">学習方法が多く、何から始めればいいかわからない...</h3>
                    <p class="p-welcome__listText">教材や学習サービスが多すぎて、どれを選べばいいか迷ってしまう人が多く、最初の一歩が踏み出せない</p>
                </div>

                <div class="p-welcome__problemList p-welcome__problemList--unhighlighted">
                    <img class="p-welcome__problemImg" src="/storage/uploads/top_problem2_hvuf6bhvuf6bhvuf.png" alt="独学だと途中で挫折しやすい">
                    <h3 class="p-welcome__listHead p-welcome__listHead--unhighlighted">独学だとモチベーションに左右され、挫折しやすい...</h3>
                    <p class="p-welcome__listText">独学は自由度が高い反面、モチベーションを維持できず途中でやめてしまう人が多く、学習を続ける環境作りが課題</p>
                </div>

                <div class="p-welcome__problemList p-welcome__problemList--unhighlighted">
                    <img class="p-welcome__problemImg" src="storage/uploads/top_problem3_kzzb2lkzzb2lkzzb.png" alt="自分に合った学習順序や教材がわからない">
                    <h3 class="p-welcome__listHead p-welcome__listHead--unhighlighted">自分に合った学習順序や教材がわからない...</h3>
                    <p class="p-welcome__listText">自分に合う教材や順序がわからず、効率よく学べない</p>
                </div>

            </div>
        </div>
    </section>

    <!-- 親近感 -->
    <section class="p-welcome__affinityArea">
        <div class="p-welcome__main">
            <h2 class="p-welcome__sectionHead">学びの最初の一歩、迷ったことはありませんか？</h2>
            <p class="p-welcome__leadText">
                「何から始めればいいのか分からない」
                「独学だとすぐ挫折してしまう」
            </p>
            <p class="p-welcome__storyText">
                誰もが一度は通る道です。
                STEPでは、他の人がたどった学習の順番を見ながら、自分に合った学び方を見つけることができます。
                あなたも、迷いから一歩踏み出してみませんか？
            </p>
        </div>
    </section>

    <!-- 解決 -->
    <section class="p-welcome__problemArea p-welcome__problemArea--light">
        <div class="p-welcome__main ">
            <h2 class="p-welcome__sectionHead">すべてSTEPが解決します！</h2>

            <div class="p-welcome__problemLists">

                <div class="p-welcome__problemList p-welcome__problemList--highlighted">
                    <img class="p-welcome__problemImg" src="/storage/uploads/top_answer1_svqxdpsvqxdpsvqx.png" alt="学習方法が多すぎて、何から始めればいいかわからない">
                    <div class="p-welcome__listHead">
                        <h3 class="p-welcome__listHead p-welcome__listHead--highlighted">迷ったら、先輩の学習順序を真似できる！</h3>
                    </div>
                    <p class="p-welcome__listText">STEPでは、先輩学習者たちがたどった「学習の順序」をそのまま参考にできるため、何から始めればいいか迷うことはありません。</p>
                </div>

                <div class="p-welcome__problemList p-welcome__problemList--highlighted">
                    <img class="p-welcome__problemImg" src="/storage/uploads/top_answer2_hvuf6bhvuf6bhvuf.png" alt="独学だと途中で挫折しやすい">
                    <div class="p-welcome__listHead">
                        <h3 class="p-welcome__listHead p-welcome__listHead--highlighted">進捗を見える化して挫折を防ぐ！</h3>
                    </div>
                    <p class="p-welcome__listText">STEPでは、自分の学習進捗を一目で確認でき、他人の進捗も見られるため、独学でもモチベーションを維持しながら学習を続けられます。</p>
                </div>

                <div class="p-welcome__problemList p-welcome__problemList--highlighted">
                    <img class="p-welcome__problemImg" src="storage/uploads/top_answer3_kzzb2lkzzb2lkzzb.png" alt="自分に合った学習順序や教材がわからない">
                    <div class="p-welcome__listHead">
                        <h3 class="p-welcome__listHead p-welcome__listHead--highlighted">あなたに合った教材と学習順序を提示！</h3>
                    </div>
                    <p class="p-welcome__listText">STEPは、目的やレベルに合わせた最適な学習順序（STEP）を提案。初心者から上級者まで、自分にぴったりの学習計画で効率よく学べます。</p>
                </div>

            </div>
        </div>
    </section>

    <!-- 提案(最新STEP表示) -->
    <section class="p-welcome__stepListArea">
        <div class="p-welcome__main">
            <h2 class="p-welcome__sectionHead">最新のSTEPがどんどん更新されています</h2>
            <the-slider></the-slider>
        </div>
    </section>

    <!-- 行動 -->
    <section class="p-welcome__ctaArea">
        <div class="p-welcome__main">
            <h2 class="p-welcome__sectionHead">いますぐ無料で始めよう</h2>
            <p class="p-welcome__sentence">
                登録無料、すぐに使える！<br>
                先輩の学習STEPを参考に、あなたの学習計画をすぐ作れます。<br>
                効率的にスキルを身につけよう！
            </p>
            <div class="">
                <router-link class="c-btn c-btn--primary" :to="{ name: 'user-register' }">ユーザ登録はこちら</router-link>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="p-welcome__unit u-mb-welcome">
        <div class="p-welcome__main">
            <h2 class="p-welcome__sectionHead">よくあるご質問</h2>
            <div class="p-welcome__qas" v-for="(faq, index) in faqs" :key="index">

                <div class="p-welcome__qaItem">

                    <!-- 質問 -->
                    <div class="p-welcome__qa">
                        <div class="p-welcome__qa--left">
                            <div class="p-welcome__qaMark">Q</div>
                            <div class="p-welcome__question">{{ faq.question }}</div>
                        </div>

                        <!-- 回答表示トグル -->
                        <i v-if="!faq.show" class="fa-solid fa-chevron-down c-welcome__qaIcon" @click="toggleForm(index)" ></i>
                        <i v-else class="fa-solid fa-chevron-up c-welcome__qaIcon" @click="toggleForm(index)" ></i>
                    </div>

                    <!-- 回答 -->
                    <div v-if="faq.show" class="p-welcome__qa p-welcome__answer">
                        <div class="p-welcome__qaMark">A</div>
                        <div class="p-welcome__answerComment">{{ faq.answer }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- お問い合わせ -->
    <section class="p-welcome__supportArea">
        <div class="p-welcome__main">
            <h2 class="p-welcome__sectionHead">お問い合わせ</h2>
            <form class="p-welcome__contactForm" @submit.prevent="openModal">
                <!-- ユーザ名 -->
                <base-input
                    title="ユーザ名"
                    type="text"
                    v-model="name"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.USER_NAME"
                    :errors="errors['name']"
                ></base-input>

                <!-- メールアドレス -->
                <base-input
                    title="メールアドレス"
                    type="text"
                    v-model="email"
                    :isAutofocus="false"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.USER_EMAIL"
                    :errors="errors['email']"
                ></base-input>

                <!-- 問い合わせ内容 -->
                <base-textarea
                    title="お問い合わせ内容"
                    v-model="message"
                    placeholder="サービスに関する質問、意見をご記入ください"
                    :isCountable="true"
                    :maxLength="MAX_LENGTH.CONTACT_MESSAGE"
                    :errors="errors['message']"
                ></base-textarea>

                <div class="p-welcome__sendBtnArea">
                    <button class="c-btn c-btn--short">送信する</button>
                </div>
            </form>

            <!-- Modal -->
            <div v-if="modal.show">
                <base-modal
                    :title="modal.title"
                    :text="modal.text"
                    @confirm="submitForm"
                    @cancel="closeModal"
                ></base-modal>
            </div>
        </div>

    </section>

</div>
</template>
<script>
import { MAX_LENGTH } from '../const';
import modalMixin from '../modalMixin';

export default {
    mixins: [modalMixin],

    data() {
        return {
            MAX_LENGTH,

            name: '',
            email: '',
            message: '',
            errors: [],
            faqs: [

                {
                    question: "無料で利用できますか？",
                    answer: "はい、登録は無料です。STEPの閲覧や自分のSTEPの投稿も無料で行えます。", // 無料利用に関する回答
                    show: false,
                },
                {
                    question: "STEPに投稿するにはどうすればいいですか？",
                    answer: "会員登録後、マイページから「STEPを投稿する」ボタンをクリックして内容を入力するだけで投稿できます。", // 投稿方法の説明
                    show: false,
                },
                {
                    question: "他の人のSTEPを見るだけでもいいですか？",
                    answer: "はい、閲覧だけでも利用可能です。自分に合った学習順序を探すことができます。", // 閲覧専用利用の可否
                    show: false,
                },
                {
                    question: "STEPはどのような学習ジャンルに対応していますか？",
                    answer: "プログラミング、英語、資格、デザインなど幅広いジャンルに対応しています。ジャンルに制限はなく、自由にSTEPを作成可能です。", // 対応ジャンルの説明
                    show: false,
                },
                {
                    question: "STEPの使い方にルールはありますか？",
                    answer: "他のユーザーに役立つ内容であれば基本的に自由です。ただし、誹謗中傷や著作権侵害のある内容は投稿できません。", // 投稿ルールの説明
                    show: false,
                },
                {
                    question: "スマホでも利用できますか？",
                    answer: "はい、スマートフォンやタブレットからでも閲覧・投稿が可能です。", // モバイル対応の説明
                    show: false,
                },
            ],
        };
    },

    methods: {

        //Modal画面表示
        openModal() {
            let msg = 'お問合せを送信しますか？';

            this.showModal({
                text: msg
            });
        },

        //よくある質問の回答表示トグル
        toggleForm(id) {
            this.faqs[id].show = !this.faqs[id].show;
        },

       // お問い合わせ送信
        async submitForm() {
            try {
                //modalを閉じる
                this.closeModal();
                
                const response = await axios.post('/api/contact-email', {
                    type: 'contact',
                    name: this.name,
                    email: this.email,
                    message: this.message
                });

                this.$store.dispatch('flashMsg/showMsg', response.data.msg);

                // フォームをクリア
                this.name = '';
                this.email = '';
                this.message = '';
                this.errors = {};

                // ページの先頭へスクロール（スムース）
                if (this.$refs.topPage) {
                    this.$refs.topPage.scrollIntoView({ behavior: 'smooth' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } catch (e) {
                this.errors = e.response.data.errors;
            }
        }
    }
};

</script>
