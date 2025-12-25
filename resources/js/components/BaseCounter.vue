<template>
<div>
    <!-- 入力文字数のカウントを表示する -->
    <p class="c-textCounter" :class="{'c-textCounter--short': isShort}">{{counterLength}}/{{ maxLength }}文字</p>
</div>
</template>

<script>
export default {

    props: {
        counter: {
            type: [String, Number],
            default: '',
        },
        isShort: Boolean,
        maxLength: Number, // 入力可能な最大文字数
    },

    computed: {
        
        //入力文字数表示
        counterLength() {
            //undefined/null を防ぎつつ数値でも文字列化して長さを取る
            return String(this.counter ?? '').length;
        },
      },
}
</script>
