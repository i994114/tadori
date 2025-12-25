<template>
<div>
    <!-- 項目 -->
    <label class="c-label">
        {{ title }}
        
        <!-- 必須マーク -->
        <span :class="{'c-label__requiredFieldMark' : isRequired}" v-if="isRequired">*</span>
    </label>

    <textarea
        v-model="textareaContent"
        :placeholder="placeholder"
        @keydown.enter="blockEmptyLine"
        class="c-textarea"
    ></textarea>
    <base-counter
        :counter="textareaContent"
        :maxLength="maxLength"
    ></base-counter>
    <base-error-display
        :errors="errors"
    ></base-error-display>
</div>
</template>
<script>
export default {
    
    props: {
        title: String,
        value: String,
        placeholder: String,
        isRequired: Boolean,
        maxLength: Number,
        errors: Array,
    },
    
    data() {
        return {
            textareaContent: this.value,
        };
    },
    
    watch: {

        //親→子：親からの初期値を通知
        value(newValue) {
            this.textareaContent = newValue;
        },
        
        //子→親：ユーザによる入力変更を通知
        textareaContent(newValue) {
            this.$emit('input', newValue)
        }
    },

    methods: {

        //入力時に空白行を打てないようにする(改行しまくる悪戯防止のため。)
        //※普通の改行（1行分）は許可する
        blockEmptyLine(e) {
            const lines = this.textareaContent.split('\n');
            const currentLine = lines[lines.length - 1];
            
            if (currentLine.trim() === '') {
                // 空行のとき Enter キーを無効化
                e.preventDefault();
            }
        },
    }

}
</script>