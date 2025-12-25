import { USER_TYPE } from "../../const";
import store from '../../store';

export const namespaced = true;

export const state = {
    categories: [],
    allCategories: []
};

export const mutations = {
    
    //論理削除したカテゴリを除外したカテゴリ一覧
    setCategories(state, data) {
        state.categories = data;
    },

    //すべてのカテゴリ一覧
    setAllCategories(state, data) {
        state.allCategories = data;
    }

};


export const actions = {

    //すべてのカテゴリ情報を取得
    async getCategories(context, type = null) {
        let response;
        
        //管理者によるカテゴリの追加、変更、削除のときは画面再表示のため、再取得
        //それ以外のときは429エラー対策で取得しない
        if (type !== 'setting') {
            //すでに取得済みなら再取得しない(429エラー対策)
            if (type && state.allCategories.length > 0) return;
            if (!type && state.categories.length > 0) return;
        }

        if (type) {
            //論理削除込みの一覧を取得
            response = await axios.get('/api/all-categories');
            context.commit('setAllCategories', response.data);
        } else {
            //論理削除したカテゴリを除いた一覧取得
            response = await axios.get('/api/categories');
            context.commit('setCategories', response.data);
        }
    }
}