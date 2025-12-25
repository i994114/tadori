<template>
<div>
    <step-show-form
        title="STEP"
        :type="STEP_TYPE.STEP_SHOW"
        url="steps"
        next="step-edit"
        :id="currentStep.id"
    ></step-show-form>
</div>
</template>
<script>
import { mapState } from 'vuex';
import { STEP_TYPE } from '../const';

export default {
    props: ['id'],
    
    data() {
        return {
            STEP_TYPE,
        };
    },

    computed: {
        ...mapState({
            currentStep: state => state.currentStep.currentStep
        })
    }
}
</script>