<template>
<div>
    <!-- modal画面 -->
    <div class="c-modal">
        <p>{{ text }}</p>
        <div class="c-modal__btnArea">
            <button class="c-btn c-btn--shown" @click="onConfirm">はい</button>
            <button class="c-btn c-btn--shown" @click="onCancel">いいえ</button>
        </div>
    </div>
    <!-- 外側 -->
    <div class="c-modal__mask"></div>
</div>
</template>
<script>
export default {
    props: {
        text: String,
    },

    methods: {
        onConfirm() {
            this.$emit('confirm')
        },

        onCancel() {
            this.$emit('cancel');
        },
    }
}
</script>