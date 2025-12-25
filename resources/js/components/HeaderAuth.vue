<template>
<div>
    <li class="l-header__item">
        <div
            class="l-header__userIcon"
            role="button"
            aria-haspopup="true"
            :aria-expanded="showFlag"
            @click.stop="toggleMenu"
        >
            <img :src="`/storage/uploads/${authUser.user_img}`" alt="" class="c-avatar c-avatar--primary c-avatar--up">
            
        </div>

        <ul class="l-header__item--dropped js-click-outside" v-show="showFlag">
            <li class="l-header__dropName">{{ authUser.name }}</li>
            <li class="l-header__dropMenu  c-hover" @click="goLogout">ログアウト</li>
            <li class="l-header__dropMenu c-hover" @click="goMypage">マイページ</li>
            <li  class="l-header__dropMenu c-hover" v-if="authUser.role === USER_TYPE.ADMIN"  @click="goAdminTop">管理用</li>
        </ul>
        
    </li>
</div>
</template>
<script>
import { mapGetters, mapState } from 'vuex';
import { USER_TYPE } from '../const';

export default {

    data() {
        return {
            USER_TYPE,
            showFlag: false,
        };
    },
    computed: {
        
        ...mapGetters('user', [
            'authUser', //ログインユーザ情報
            'isAuthenticated',  //ログイン済みか
            'isAdmin',  //管理者か
        ]),


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

        //ログアウトに遷移
        goLogout() {
            this.$router.push({ name: 'logout' });
        },

        //マイページに遷移
        goMypage() {
            this.$router.push({ name: 'mypage' });
        },

        //管理者ページに遷移
        goAdminTop() {
            this.$router.push({ name: 'admin-top' });
        }

    }

}
</script>