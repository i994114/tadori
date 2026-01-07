<template>
<div>
    <!-- フラッシュメッセージ -->
    <flash-msg />

    <header :class="['l-header', { 'l-header__welcomeHeader': isWelcomePage }]">
        <div class="l-header__container">

            <!-- ヘッダーロゴ -->
            <h1 class="l-header__logo c-hover" @click="jdgNextPage">TADORI</h1>

            <!-- ヘッダーメニュ -->
            <nav>
                <ul class="l-header__nav">
                    <div v-if="!isAuthenticated" class="l-header__items">
                        <!-- ログイン前 -->
                        <header-guest />
                    </div>
                    <div v-else class="l-header__items">
                        <!-- ログイン後 -->
                        <header-auth />
                    </div>
                </ul>
            </nav>
        </div>
    </header>
</div>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    
    data() {
        return {
            link: '',
            name: '',
        };
    },

    computed: {
        
        ...mapGetters('user', [
            'isAuthenticated',  //ログイン済みか
        ]),

        //現在開いているのがトップページか
        isWelcomePage() {
            return this.$route.name === 'welcome';
        }

    },

    methods: {
        //ヘッダーロゴクリック時の遷移先を判定
        jdgNextPage() {
            if (!this.isAuthenticated) {
                //ログイン前ならトップページに遷移
                this.$router.push({ name: 'welcome' });
            } else {
                //ログイン後ならSTEP一覧のリンクをつける
                this.$router.push({ name: 'step-index' });
            }
        },

    }
}
</script>