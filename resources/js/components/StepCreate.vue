<template>
<div>
    <step-and-sub-step-form
        title="STEP作成"
        :type="STEP_TYPE.STEP_CREATE"
        url="steps"
    ></step-and-sub-step-form>
</div>
</template>
<script>
import { STEP_TYPE } from '../const';

export default {
    data() {
        return {
            STEP_TYPE,
        };
    },
}
</script>
