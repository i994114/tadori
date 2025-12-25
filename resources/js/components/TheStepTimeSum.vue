<template>
<div>
    <button class="c-btn c-btn--small" type="button" @click="sumSubStepEstimatedTime">子STEP合計を反映</button>
</div>
</template>
<script>
import { mapState } from 'vuex';

export default {
    props: {
        value: [String, Number],
    },

    data() {
        return {
            sumEstimatedTime: 0,
        };
    },

    computed: {
        ...mapState({
            currentStep: state => state.currentStep.currentStep,
        }),
    },

    methods: {

        //当該STEPのもつ子STEPの目安達成時間の和を求める
        async sumSubStepEstimatedTime() {
            const response = await axios.get(`/api/step/${this.currentStep.id}/sub-steps-sum`);
            this.sumEstimatedTime = response.data;

            //親にemit
            this.$emit('input', this.sumEstimatedTime);
        }
    },
}
</script>