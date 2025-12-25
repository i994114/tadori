<template>
<div>
    <step-show-form
        title="子STEP"
        :type="STEP_TYPE.SUB_STEP_SHOW"
        url="sub_steps"
        next="sub-step-edit"
        :id="Number(id)"
    ></step-show-form>
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
    }
}
</script>