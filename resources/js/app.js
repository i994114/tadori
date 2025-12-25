/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import Vue from 'vue';
import router from './router';
import axios from 'axios';
import store from './store';
import '@fortawesome/fontawesome-free/css/all.css';
import { STORAGE_NAMES } from './const';
import Paginate from 'vuejs-paginate';
import draggable from 'vuedraggable';
import stepListMixin from './stepListMixin';
import modalMixin from './modalMixin';
import VueSlickCarousel from 'vue-slick-carousel';
import 'vue-slick-carousel/dist/vue-slick-carousel.css';
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css';


//リクエスト処理：グローバルなaxios処理をinterceptorsに設定
axios.interceptors.request.use(
    (config) => {
        
        //ローカルストレージからアクセストークンを取得
        const token = localStorage.getItem(STORAGE_NAMES.ACCESS_TOKEN);

        //トークン不要APIリスト
        const publicEndpoints = [
            { method: 'get',  url: '/api/categories' },
            { method: 'get',  url: '/api/steps' },
            { method: 'get',  url: '/api/users' },
            { method: 'get',  url: '/api/steps-for-sidebar' },
            { method: 'get',  url: '/api/recent-steps' },

            { method: 'post', url: '/api/login' },
            { method: 'post', url: '/api/users' },
            { method: 'post', url: '/api/contact-email' },
        ];

        //後で比較するときに大文字・小文字の違いを気にしなくて済むように小文字化して、
        const method = config.method.toLowerCase();
        const requestUrl = config.url;

        // 完全一致または startsWith() で部分一致も許可
        const isPublic = publicEndpoints.some(endpoint => {
            if (endpoint.method !== method) return false;
            
            // GETのときのみ startsWith を許可
            if (endpoint.method === 'get') {
                return requestUrl === endpoint.url || requestUrl.startsWith(endpoint.url + '/');
            }

            // GET以外は完全一致のみ（PUT/POST/DELETE で誤判定しない）
            return requestUrl === endpoint.url;
        });
        config.headers = {
            ...config.headers,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };

        if (!isPublic && token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        //リクエストエラー時の処理
        handleErrors(error);
        return Promise.reject(error);
    }
);

//レスポンス処理：成功、失敗どちらも実装
axios.interceptors.response.use(

    //成功時
    (response) => {
        return response;
    },

    //失敗時
    (error) => {
        handleErrors(error);
        return Promise.reject(error);       //これがないとリロードされ、入力フォームの場合は入力が消えてしまう
    }
);

function handleErrors(error) {
    if (error.response) {
        const status = error.response.status;

        // エラー処理に応じた処理
        switch (status) {
            case 500:
                store.dispatch('flashMsg/showMsg', 'サーバーで問題が発生しました。しばらくしてから再度お試しください。');
                break;
            case 403:
                store.dispatch('flashMsg/showMsg', 'この操作を行う権限がありません。');
                break;
            case 404:
                store.dispatch('flashMsg/showMsg', 'お探しのページやデータが見つかりません。');
                break;
            case 400:
                store.dispatch('flashMsg/showMsg', 'リクエスト内容に不正があります。入力内容をご確認ください。');
                break;
            case 422:
                store.dispatch('flashMsg/showMsg', '入力内容に誤りがあります。もう一度ご確認ください。');
                break;
            case 401:
                store.dispatch('flashMsg/showMsg', '認証に失敗しました。ログインしてください。');
                
                // ローカルストレージのトークン情報を削除
                store.dispatch('user/logout');
                
                router.push({ name: 'login' });
                break;
            default:
                store.dispatch('flashMsg/showMsg', '予期しないエラーが発生しました。しばらくしてから再度お試しください。');
                break;
        }
    } else {
        // ネットワークエラー
        store.dispatch('flashMsg/showMsg', 'ネットワークに接続できません。接続状況をご確認ください。');
    }
}


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('flash-msg', require('./components/FlashMsg.vue').default);
Vue.component('base-input', require('./components/BaseInput.vue').default);
Vue.component('base-search-input', require('./components/BaseSearchInput.vue').default);
Vue.component('base-error-display', require('./components/BaseErrorDisplay.vue').default);
Vue.component('base-dropdown-menu', require('./components/BaseDropdownMenu.vue').default);
Vue.component('base-textarea', require('./components/BaseTextarea.vue').default);
Vue.component('base-image-uploader', require('./components/BaseImageUploader.vue').default);
Vue.component('base-counter', require('./components/BaseCounter.vue').default);
Vue.component('base-breadcrumb', require('./components/BaseBreadcrumb.vue').default);
Vue.component('base-modal', require('./components/BaseModal.vue').default);

Vue.component('header-component', require('./components/HeaderComponent.vue').default);
Vue.component('header-guest', require('./components/HeaderGuest.vue').default);
Vue.component('header-auth', require('./components/HeaderAuth.vue').default);

Vue.component('step-show-form', require('./components/StepShowForm.vue').default);
Vue.component('step-and-sub-step-form', require('./components/StepAndSubStepForm.vue').default);
Vue.component('the-step-time-sum', require('./components/TheStepTimeSum.vue').default);
Vue.component('base-step-list', require('./components/BaseStepList.vue').default);

Vue.component('footer-component', require('./components/FooterComponent.vue').default);

Vue.component('sidebar-right-component', require('./components/SidebarRightComponent.vue').default);
Vue.component('sidebar-right-mypage', require('./components/SidebarRightMypage.vue').default);
Vue.component('sidebar-right-general', require('./components/SidebarRightGeneral.vue').default);
Vue.component('sidebar-left', require('./components/SidebarLeft.vue').default);

Vue.component('the-slider', require('./components/TheSlider.vue').default);


Vue.component('paginate', Paginate);
Vue.component('stepListMixin', stepListMixin);
Vue.component('modalMixin', modalMixin);
Vue.component('draggable', draggable);
Vue.component('vue-slick-carousel', VueSlickCarousel);

Vue.prototype.$axios = axios;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router: router,
    store: store,
});
