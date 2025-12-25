<template>
    <div class="l-header__guest">

        <!-- PC/tab 用：横並び -->
        <div class="l-header__guestLinks">
            <li class="l-header__item c-hover" @click="$router.push({ name: 'login' })">ログイン</li>
            <li class="l-header__item l-header__item--colored c-hover" @click="$router.push({ name: 'user-register' })">新規登録</li>
        </div>

        <!-- SP用：ドロップダウン -->
        <div class="l-header__guestDropdown js-click-outside" @click.stop="toggleMenu">
            <li class="l-header__item">
                <div
                    class="l-header__userIcon"
                    role="button"
                    aria-haspopup="true"
                    :aria-expanded="showFlag"
                >
                    <i class="fas fa-angle-down"></i>
                </div>

                <ul class="l-header__item--dropped" v-show="showFlag">
                    <li class="l-header__dropMenu c-hover" @click="$router.push({ name: 'login' })">
                        ログイン
                    </li>
                    <li class="l-header__dropMenu c-hover" @click="$router.push({ name: 'user-register' })">
                        新規登録
                    </li>
                </ul>
            </li>
        </div>

    </div>
</template>

<script>

export default {
    
    data() {
        return {
            showFlag: false,
        };
    },

    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },

    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside);
    },

    methods: {

        //メニューの表示/非表示をトグルする
        toggleMenu() {
            this.showFlag = !this.showFlag;
        },

        //ドロップメニュー外をクリックしたか判定し、メニューを閉じる
        handleClickOutside(event) {
            const menu = this.$el.querySelector('.js-click-outside');
            
            if (menu && !menu.contains(event.target)) {
                this.showFlag = false;
            }
        },
    }

};
</script>
