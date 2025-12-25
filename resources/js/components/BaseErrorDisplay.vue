<template>
<!-- バリデーションエラーを表示する -->
<div v-if="errors">
    <div v-for="(error, index) in errors" :key="index" class="c-error">
        <span class="c-error__invalid" role="alert">
            <strong>{{error}}</strong>
        </span>
    </div>
</div>
</template>
<script>
export default {
    props: {
        errors: [Array, Object],
    }
};
</script>