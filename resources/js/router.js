import Vue from 'vue';
import VueRouter from 'vue-router';
import store from './store';
import { STORAGE_NAMES } from './const';

//コンポーネントをインポート
import Welcome from './components/Welcome.vue'

import Login from './components/Login.vue'
import Logout from './components/Logout.vue'

import UserRegister from './components/UserRegister.vue';
import UserShow from './components/UserShow.vue';
import UserEdit from './components/UserEdit.vue';


import EmailVerifiedRedirect from './components/EmailVerifiedRedirect.vue';


import PrivacyPolicy from './components/ThePrivacyPolicy.vue';
import TermsOfService from './components/TheTermsOfService.vue';

import PasswordResetEmail from './components/PasswordResetEmail.vue'
import PasswordResetForm from './components/PasswordResetForm.vue';
import RequestEmailVerification from './components/RequestEmailVerification.vue';
import PasswordChange from './components/PasswordChange.vue';
import EmailChange from './components/EmailChange.vue';

import Mypage from './components/Mypage.vue';
import MypagePosted from './components/MypagePosted.vue';
import MypageChallenged from './components/MypageChallenged.vue';
import MypageFavorited from './components/MypageFavorited.vue';

import StepCreate from './components/StepCreate.vue';
import StepShow from './components/StepShow.vue';
import StepEdit from './components/StepEdit.vue';
import StepIndex from './components/StepIndex.vue';

import SubStepList from './components/SubStepList.vue';
import SubStepEdit from './components/SubStepEdit.vue';
import SubStepShow from './components/SubStepShow.vue';


import AdminTop from './components/AdminTop.vue';
import CategorySetting from './components/CategorySetting.vue';


Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/', name: 'welcome', component: Welcome },

        { path: '/login', name: 'login', component: Login },
        { path: '/logout', name: 'logout', component: Logout },

        { path: '/email-verified-redirect', name: 'email-verified-redirect', component: EmailVerifiedRedirect, props: route => ({ status: route.query.status, type: route.query.type, email: route.query.email }) },

        { path: '/user-register', name: 'user-register', component: UserRegister },
        { path: '/user-show/:id', name:'user-show', component: UserShow },
        { path: '/user-edit/:id', name: 'user-edit', component: UserEdit, meta: { requiresAuth: true } },


        { path: '/privacy-policy', name: 'privacy-policy', component: PrivacyPolicy },
        { path: '/terms-of-service', name: 'terms-of-service', component: TermsOfService },

        { path: '/password-reset-email', name: 'password-reset-email', component: PasswordResetEmail },
        { path: '/password-reset-form/:token', name: 'password-reset-form', component: PasswordResetForm, props: route => ({ token: route.params.token, email: route.query.email }) },
        { path: '/request-email-verification', name: 'request-email-verification', component:  RequestEmailVerification },

  
        { path: '/email-change/:id', name: 'email-change', component: EmailChange, meta: { requiresAuth: true } },
        { path: '/password-change/:id', name: 'password-change', component: PasswordChange, meta: { requiresAuth: true } },


        { path: '/mypage', name: 'mypage', component: Mypage, meta: { requiresAuth: true } },
        { path: '/mypage-posted', name: 'mypage-posted', component: MypagePosted, meta: { requiresAuth: true } },
        { path: '/mypage-challenged', name: 'mypage-challenged', component: MypageChallenged, meta: { requiresAuth: true } },
        { path: '/mypage-favorited', name: 'mypage-favorited', component: MypageFavorited, meta: { requiresAuth: true } },
     
        { path: '/step-create', name: 'step-create', component: StepCreate, meta: { requiresAuth: true } },
        { path: '/step-show/:id', name: 'step-show', component: StepShow, props: true },
        { path: '/step-edit/:id', name: 'step-edit', component: StepEdit, props: true, meta: { requiresAuth: true }},

        { path: '/step-index', name: 'step-index', component: StepIndex },

        { path: '/step/:id/sub-step-list', name: 'sub-step-list', component: SubStepList },
        { path: '/sub-step-edit/:id', name: 'sub-step-edit', component: SubStepEdit, props: true, meta: { requiresAuth: true } },
        { path: '/sub-step-show/:id', name: 'sub-step-show', component: SubStepShow, props: true, eta: { requiresAuth: true } },

        { path: '/admin-top', name: 'admin-top', component: AdminTop, meta: { requiresAdmin: true } },
        { path: '/category-setting', name: 'category-setting', component: CategorySetting, meta: { requiresAdmin: true } },

    ]
});


// セッション復元処理が完了済みかどうかを示すフラグ
let sessionRestored = false;
//STEP一覧をさいしょに取得済か
let stepsLoaded = false;
/*
 セッション復元処理
 ・ローカルストレージのアクセストークンを使い、Vuexの認証情報を再設定する
 ・すでに復元済みなら何もしない（早期リターン）
*/
async function restoreSessionIfNeeded() {
    // すでに復元済みなら再実行しない（2回目以降の呼び出しをスキップ）
    if (sessionRestored) return;

    // ローカルストレージからアクセストークンを取得
    const accessToken = localStorage.getItem(STORAGE_NAMES.ACCESS_TOKEN);

    // トークンがなければ復元不要としてフラグだけ立てて終了
    if (!accessToken) {
        sessionRestored = true;
        return;
    }

    // トークンのデコードと有効期限チェックを行う
    let decoded;
    try {
        decoded = decodeToken(accessToken);
    } catch (e) {
        // トークンのデコードに失敗したら復元を中止
        sessionRestored = true;
        return;
    }

    // トークンが期限切れなら復元を中止
    const isValidToken = checkTokenExpiration(decoded.exp);
    if (!isValidToken) {
        sessionRestored = true;
        return;
    }

    // Vuexの認証情報をチェックし、未認証なら再ログインを実行
    const isAuthenticated = store.getters['user/isAuthenticated'];
    if (!isAuthenticated) {
        await store.dispatch('user/login', { id: decoded.sub });
    }

    // 復元完了フラグを立てる
    sessionRestored = true;
}



//ログインしないと見れないページかを判定
router.beforeEach(async (to, from, next) => {
    
    // セッションを復元（未修復なら）
    await restoreSessionIfNeeded();

    // 共通データ読みだし
    await loadCommonData();
    
    //一度だけ実行
    if (!stepsLoaded) {
        await setStepsData();
        stepsLoaded = true;
    }

    //次の遷移先はログイン済みでないと入れないページか
    if (to.meta.requiresAuth || to.meta.requiresAdmin) {
        //次の遷移先を判定する
        await jdgNextRoute(to, next);
    } else {
        //未ログインでも遷移可能なため、そのまま遷移
        next();
    }
});


//トークンとログイン情報をもとに、次の遷移先を判定
async function jdgNextRoute(to, next) {

    //ログイン情報を取得
    const isAuthenticated = store.getters['user/isAuthenticated'];
    const isVerifiedEmail = store.getters['user/isVerifiedEmail'];

    //管理者権限ユーザのログインか否かの情報を取得
    const isAdmin = store.getters['user/isAdmin'];

    //トークンとその有効性はすでに restoreSessionIfNeeded で確認済みなはずだが、念のためチェック
    const accessToken = localStorage.getItem(STORAGE_NAMES.ACCESS_TOKEN);
    let decodedToken = null;
    let isValidToken = false;
    if (accessToken) {
        decodedToken = decodeToken(accessToken);
        isValidToken = checkTokenExpiration(decodedToken.exp);
    }

    if (isAuthenticated && isValidToken) {   //ログイン済みおよびアクセストークンが有効期限内か
        
        //メール認証済みかを判定
        if (!isVerifiedEmail) {
            //メール認証を促すページに遷移
            next({ name: 'request-email-verification' });
        }

        //つぎに遷移するページが管理者用か否かを判定
        if (to.meta.requiresAdmin) {
            if (isAdmin) {
                //管理者ページに遷移
                next();
            } else {
                store.dispatch('flashMsg/showMsg', '権限がありません');
            }
        } else {
            //ログイン済みなら遷移可能なページに遷移
            next();
        }
        return;

    } else {        //ログイン情報はあるが、アクセストークンが期限切れか
        //リフレッシュトークンを取得し、有効期限をチェック
        const refreshTokenExpires = localStorage.getItem(STORAGE_NAMES.REFRESH_EXPIRES); 
        const isValidRefreshToken = checkTokenExpiration(decodedToken.exp + refreshTokenExpires * 60);  //アクセストークン期限にリフレッシュトークンの期限を足す
        if (isValidRefreshToken) {
            console.group('fdasfas')
            //リフレッシュトークンが有効なため、アクセストークンを更新
            const response = await axios.post('/api/refresh', {refreshTokenRequest: true,});
            //トークンを更新したのでローカルストレージに再設定
            localStorage.setItem(STORAGE_NAMES.ACCESS_TOKEN, response.data.access_token);
            next();
            return;
        } else {
            //トークン期限切れのため、ログイン画面に遷移
            handleAuthenticationError();
            next({ name: 'login' });
            return;
        }

    }

    //それ以外は未認証としてログイン画面へ
    handleAuthenticationError();
    next({ name: 'login' });

}

//トークンのデコード
function decodeToken(token) {
    //ペイロード部分を取得
    const base64Url = token.split('.')[1];
    
    //"+"が"-"に、"/"が"_"に置き換えられているため、元のBase64形式に変換
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');

    return JSON.parse(window.atob(base64));
}


//トークンの有効期限の確認
function checkTokenExpiration(exp) {
    //JWT expはUNIX時間であり、単位がmsなのでを秒に変換
    const expireDate = new Date(exp * 1000);
    const now = new Date();

    return now < expireDate;
}

//認証NG時の認証データクリア処理
function handleAuthenticationError() {
    localStorage.removeItem(STORAGE_NAMES.ACCESS_TOKEN);
    localStorage.removeItem(STORAGE_NAMES.REFRESH_EXPIRES);
    localStorage.removeItem(STORAGE_NAMES.STEP_ID);
}

//Vuex情報読み出し
//(リロードしてもVuexデータを失わないようにするため)
async function loadCommonData() {
    await store.dispatch('category/getCategories', 'all');
    await store.dispatch('category/getCategories');
    await store.dispatch('currentStep/fetchCurrentStep');
}

//STEP一覧取得(429エラー対策のため、一度だけ取得)
async function setStepsData() {
    let response;

    response = await axios.get('/api/steps');
    await store.dispatch('stepList/showSteps', response.data);
}

export default router;
export { restoreSessionIfNeeded };
