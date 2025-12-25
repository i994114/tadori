import { STORAGE_NAMES } from "../../const";

export const namespaced = true;

export const state = {
    currentStep: {},
};

export const getters = {
    currentStep: state => state.currentStep,
};

export const mutations = {
    setCurrentStep(state, step) {
        state.currentStep = step;
    },
    clearCurrentStep(state) {
        state.currentStep = null;
    }
};

export const actions = {

    //当該STEP情報をセット
    async fetchCurrentStep(context, data = null) {
        if (data) { //通常時          
            context.commit('setCurrentStep', data);

            // localStorage に保存
            localStorage.setItem(STORAGE_NAMES.STEP_ID, data.id);
        } else {    //リロード時
            //現在使っているSTEP IDを取得
            const stepId = localStorage.getItem(STORAGE_NAMES.STEP_ID);

            if (stepId) {
                const response = await axios.get(`/api/steps/${stepId}`);
                context.commit('setCurrentStep', response.data);
            }
        }
    },

    //セットしてSTEP情報をクリア
    clearCurrentStepData(context) {
        context.commit('setCurrentStep');
    }

}