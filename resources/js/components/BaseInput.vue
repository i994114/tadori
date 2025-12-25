<template>
<div>
    <label v-if="title" class="c-label" :class="{'c-label--normal': !isSearch}">
        {{ title }}
        <!-- 必須マーク -->
        <span :class="{'c-label__requiredFieldMark' : isRequired}" v-if="isRequired">*</span>
        <span v-if="rule" class="c-label__rule"> ※{{ rule }}</span>
    </label>
    
    <input
        class="c-input c-input--primary"
        :type="type"
        :name="name"
        :autofocus="isAutofocus"
        :placeholder="placeholder"
        v-model="inputValue"
        :disabled="isDisabled"
    >
    
    <div v-if="isCountable">
        <!-- 文字数カウント -->
        <base-counter
            :counter="inputValue"
            :maxLength="maxLength"
        ></base-counter>
    </div>

    <!-- エラー表示 -->
    <base-error-display
        :errors="errors"
    ></base-error-display>

</div>
</template>
<script>
export default {
    
    props: {
        title: String,
        type: String,
        name: String,
        rule: String,
        placeholder: String,
        value: [String, Number],
        isAutofocus: Boolean,
        isRequired: Boolean,
        isDisabled: Boolean,
        isCountable: Boolean,   //入力文字を数えるか否か
        isSearch: Boolean,  //検索窓からの呼び出しか
        maxLength: Number,
        errors: [Array, Object],
    },

    data() {
        return {
            inputValue: this.value,
        };
    },

    watch: {

        value(newValue) {
                this.inputValue = newValue;
        },

        inputValue(newValue) {
            this.$emit('input', newValue);
        }
    }
}
</script>