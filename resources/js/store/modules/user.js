import { LOGIN_TYPE, USER_TYPE, STORAGE_NAMES } from '../../const';

export const namespaced = true;

export const state = {
    user: null,
};

export const getters = {
    authUser: state => state.user,
    isAuthenticated: state => state.user !== null,
    isVerifiedEmail: state => state.user && state.user.verified_email_at !== null,
    isAdmin: state => state.user && state.user.role === USER_TYPE.ADMIN,
} ;

export const mutations = {

    setUser(state, userData) {
        state.user = userData;
    },

    clearUser(state) {
        state.user = null;
    }
};

export const actions = {
    
    async login(context, payload = null) {
        let response;

        //アクセストークンを取得
        const token = localStorage.getItem(STORAGE_NAMES.USER_ACCESS_TOKEN);
        //トークンを付加するためのヘッダーを定義(この時点では、Vuexのログイン種別が算出されていないので、決め打ちでUserトークンを定義)
        const headers =  {
            'Authorization': token? `Bearer ${token}` : '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };

        try {
            response = await axios.get('/api/me', {
                headers
            });

            //レスポンスを処理
            context.commit('setUser', response.data);
            return response.data;

        } catch(e) {
            console.error('Error during login:', e);
        }
    },

    logout(context) {
        //ローカルストレージのアクセストークンを削除
        localStorage.removeItem(STORAGE_NAMES.ACCESS_TOKEN);
        localStorage.removeItem(STORAGE_NAMES.REFRESH_EXPIRES);
        localStorage.removeItem(STORAGE_NAMES.STEP_ID);
        context.commit('clearUser');
    }
    
}
