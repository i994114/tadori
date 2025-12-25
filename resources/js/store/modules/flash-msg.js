export const namespaced = true;

export const state = {
    flashMsg: '',
};

export const mutations = {
    setMsg(state, msg) {
        state.flashMsg = msg;
    },

    clearMsg(satate) {
        state.flashMsg = '';
    }
};

export const actions = {

    //表示するフラッシュメッセージをストア
    showMsg(context, payload = null) {

        //フラッシュメッセージをセット
        context.commit('setMsg', payload);

        //フラッシュメッセージをクリア
        setTimeout(() => {
            context.commit('clearMsg');
        },3000);
    },
};


