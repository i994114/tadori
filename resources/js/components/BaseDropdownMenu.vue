<template>
<div>
    <!-- 項目 -->
    <label class="c-label">
        {{ title }}
        
        <!-- 必須マーク -->
        <span :class="{'c-label__requiredFieldMark' : isRequired}" v-if="isRequired">*</span>
    </label>
    
    <select :class="['c-optionbox', extraClass]" v-model="selectData">
        <option :value="null" >選択してください</option>
        <option v-for="dropdownMenu in dropdownMenus" :key="dropdownMenu.id" :value="dropdownMenu.id" >
            {{ dropdownMenu[displayMenu] }}
        </option>
    </select>

    <!-- エラー表示 -->
    <base-error-display
        :errors="errors"
    ></base-error-display>
</div>
</template>
<script>
import { DROPDOWN_TYPE } from '../const';
export default {

    props: {
        DROPDOWN_TYPE,
        dropdownMenus: [],
        value: [String, Number],
        title: String,
        type: String,
        isRequired: Boolean,
        extraClass: String,
        errors: Array,
    },

    data() {
        return {
            selectData: this.value,
        };
    },

    computed: {
        displayMenu() {
            switch (this.type) {
                case DROPDOWN_TYPE.CATEGORIES:
                    return 'category_name';
                case DROPDOWN_TYPE.SORTMENU:
                    return 'name';
                default:
                    break;
            }
        }
    },

    watch: {

        //親→子：親からの初期値を通知
        value(newValue) {
            this.selectData = newValue;
        },

        //ユーザが選択したドロップダウンメニューを親に通知
        selectData(newValue) {
            this.$emit('input', newValue);
        },
    }
};
</script>
