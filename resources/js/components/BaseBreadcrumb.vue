<template>
<div class="c-breadcrumb">
    <!-- パンくずリスト -->
    <ul class="c-breadcrumb__nav">
        <li class="c-breadcrumb__item" v-for="(item, index ) in breadcrumItems" :key="index">
            <span v-if="index === breadcrumItems.length - 1">{{ item.text }}</span>
            <router-link v-else :to="item.to">{{ item.text }}</router-link>
        </li>
    </ul>
</div>
</template>
<script>
import { mapGetters, mapState } from 'vuex';
import { makeBreadcrumbs } from '../common';

export default {
    props: {
        type: Number,   //現在の画面
        subStepId: Number,
    },

    computed: {
        ...mapGetters('user',[
            'isAuthenticated',  //ログイン済か
        ]),

        ...mapState({
            currentStep: state => state.currentStep.currentStep,    //現在展開しているSTEP
        }),

        //現在の画面に対するパンくずリスト
        breadcrumItems() {
            if (!this.currentStep) {
                return;
            }
            return makeBreadcrumbs(this.type,  this.currentStep.id, this.subStepId, this.isAuthenticated);
        }
    },

    methods: {
        makeBreadcrumbs,
    }
}
</script>