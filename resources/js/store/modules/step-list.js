export const namespaced = true;

export const state = {
    steps: [],
};

export const getters = {
    
    //最新12件(トップページ用)
    recentSteps: state => state.steps
        .slice()
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 12),

    //新着5件（サイドバー用）
    newSteps: state => state.steps
        .slice() //コピーして元配列を破壊しない
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 5), //最新5件

    //チャレンジ数上位5件（サイドバー用）
    mostChallengedSteps: state => state.steps
        .slice()
        .sort((a, b) => (b.challenges_count || 0) - (a.challenges_count || 0))
        .slice(0, 5) //チャレンジ数上位5件
};

export const mutations = {
    setSteps(state, stepData) {
        state.steps = stepData;
    },
    updateSteps(state, id) {
        state.steps = state.steps.filter(step => step.link_id !== id)
    }
};

export const actions = {

    //取得したSTEP一覧をセット
    showSteps(context, payload = []) {
        context.commit('setSteps', payload);
    },

    //削除/キャンセルしたSTEPを一覧から除外
    removeStepById(context, id = null) {
         context.commit('updateSteps', id);
    }
};