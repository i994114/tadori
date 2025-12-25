<template>
<div class="p-searchArea">
    <div class="p-searchArea--left">
        <base-input
            type="text"
            v-model="searchText"
            :isSearch="true"
        ></base-input>
    </div>
    <div class="p-searchArea--right c-hover">
        <i class="fa-solid fa-magnifying-glass" @click="$emit('search')" ></i>
    </div>
</div>
</template>
<script>
export default {

    props: {
        value: String,
    },

    data() {
        return {
            searchText: this.value || '', //入力された検索語
        };
    },

    mounted() {
        this.searchText =this.value; //入力された検索語
    },

    watch: {
        value(newValue) {
            this.searchText = newValue;
        },

        searchText(newValue) {
            this.$emit('input', newValue);
        }
    },

}
</script>