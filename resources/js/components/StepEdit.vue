<template>
<div>
    <step-and-sub-step-form
        title="STEP編集"
        :type="STEP_TYPE.STEP_EDIT"
        url="steps"
        :id="Number(id)"
    ></step-and-sub-step-form>
</div>
</template>
<script>
import { STEP_TYPE } from '../const';

export default {
    props: ['id'],

    data() {
        return {
            STEP_TYPE,
        };
    },
}
</script>
