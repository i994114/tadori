import Vue from "vue";
import Vuex from "vuex";

import * as user from './modules/user';
import * as flashMsg from './modules/flash-msg';
import * as category from './modules/category';
import * as currentStep from './modules/current-step';
import * as stepList from './modules/step-list';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        user,
        flashMsg,
        category,
        currentStep,
        stepList,
    },
});