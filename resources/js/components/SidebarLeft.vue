<template>
<div class="l-sidebar">
    <form  @submit.prevent="$emit('search')" class="l-sidebar__form">
        
        <!-- カテゴリ検索 -->
        <base-dropdown-menu
            title="カテゴリ"
            :dropdownMenus="categories"
            v-model="categoryId"
            :type="DROPDOWN_TYPE.CATEGORIES"
            extraClass="c-optionbox--left"
        ></base-dropdown-menu>

        <!-- 目標達成時間別検索 -->
        <section class="l-sidebar__unit">
            <label class="l-sidebar__title">目安達成時間</label>
            <div class="l-sidebar__radioMenuitems">
                
                <label class="l-sidebar__radioMenuitem">
                    <input type="radio" v-model="selectedEstimatedTime" :value="null">
                    なし
                </label>

                <label class="l-sidebar__radioMenuitem">
                    <input type="radio" v-model="selectedEstimatedTime" value="short">
                    短め：～3時間
                </label>

                <label class="l-sidebar__radioMenuitem">
                    <input type="radio" v-model="selectedEstimatedTime" value="medium_short">
                    少し長め：3〜6時間
                </label>

                <label class="l-sidebar__radioMenuitem">
                    <input type="radio" v-model="selectedEstimatedTime" value="standard">
                    標準：6〜12時間
                </label>

                <label class="l-sidebar__radioMenuitem">
                    <input type="radio" v-model="selectedEstimatedTime" value="long">
                    じっくり：12〜24時間
                </label>

                <label class="l-sidebar__radioMenuitem">
                    <input type="radio" v-model="selectedEstimatedTime" value="very_long">
                    長期：1日以上
                </label>

            </div>
        </section>


        <!-- 並び替え -->
        <base-dropdown-menu
            title="並び替え"
            :dropdownMenus="SORT_MENU"
            v-model="sortId"
            :type="DROPDOWN_TYPE.SORTMENU"
            extraClass="c-optionbox--left"
        ></base-dropdown-menu>

        <section class="l-sidebar__unit">
            <div class="l-sidebar__searchBtnArea">
                <button class="c-btn c-btn--wide">検索する</button>
            </div>
        </section>
    </form>

</div>
</template>
<script>
import { mapState } from 'vuex'
import { SORT_MENU, DROPDOWN_TYPE } from '../const';

export default {

    props: {
        value: Object,
    },

    data() {
        return{
            SORT_MENU,
            DROPDOWN_TYPE,

            categoryId: null,
            selectedEstimatedTime: null,
            sortId: null,
        };
    },

    computed: {

        ...mapState({
            //論理削除したカテゴリ込みの一覧取得
            categories: state => state.category.allCategories,
        }),
    },

    watch: {
        value: {
            handler(newVal) {
                if (newVal) {
                    this.categoryId = newVal.category || null;
                    this.selectedEstimatedTime = newVal.selectedEstimatedTime || null;
                    this.sortId = newVal.sort || null;
                }
            },
            immediate: true, // 初回も実行
        },

        categoryId(newVal) {
            this.emitUpdate();
        },

        selectedEstimatedTime(newVal) {
            this.emitUpdate();
        },

        sortId(newVal) {
            this.emitUpdate();
        }

    },
    methods: {
        emitUpdate() {
            this.$emit('input', {
                category: this.categoryId,
                selectedEstimatedTime: this.selectedEstimatedTime,
                sort: this.sortId
            });
        }
    }
}
</script>