<template>
<div>
    <step-and-sub-step-form
        title="子STEP編集"
        :type="STEP_TYPE.SUB_STEP_EDIT"
        url="sub_steps"
        :id="Number(id)"
    ></step-and-sub-step-form>
</div>
</template>
<script>
import { STEP_TYPE } from '../const'

export default {
    props: ['id'],

    data() {
        return {
            STEP_TYPE,
        };
    },
}
</script>