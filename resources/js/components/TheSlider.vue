<template>
<div v-if="recentSteps.length > 0" class="p-slider">

    <!-- 戻るアイコン -->
    <i class="fa-solid fa-angle-left p-slider__prev"  :class="{'is-disabled': !canPrev}" @click="prevSlider"></i>        

    <div class="p-slider__itemArea">
        <div class="p-slider__items" :style="sliderStyle">
            <div class="p-slider__item c-hover"  
                v-for="step in recentSteps" 
                :key="step.id"  
                ref="sliderItems"
                :style="itemStyle"
                 @click="goStepShow(step)"
            >
                <div class="p-slider__title">{{ step.step_name }}</div>
                <div class="p-slider__category">{{ step.category ? step.category.category_name : '' }}</div>
                <div class="p-slider__date">{{ removeTime(step.created_at) }}</div>

                <!-- ユーザ情報 -->
                <div class="p-slider__userArea">
                    <img class="c-avatar c-avatar--small" :src="`/storage/uploads/${step.owner.user_img}`">
                    <span class="">{{ step.owner.name }}</span>
                </div>                
            </div>
        </div>
    </div>

    <!-- 次へアイコン -->
    <i class="fa-solid fa-angle-right p-slider__next" :class="{'is-disabled': !canNext}" @click="nextSlider"></i>

</div>
<div class="p-slider--empty" v-else>
    <p>現在、投稿されたSTEPはまだありません。</p>
</div>
</template>
<script>
import { mapGetters } from 'vuex';
import { goStepShow, removeTime } from '../common';

export default {
    data() {
        return {
            currentSlider: 0,
            slidesToShow: 3, // 初期表示枚数
        };
    },

    mounted() {
        window.addEventListener('resize', this.updateSlidesToShow);
    },

    beforeDestroy() {
        window.removeEventListener('resize', this.updateSlidesToShow);
    },

    computed: {
        
        ...mapGetters('stepList', [
            'recentSteps', //最新12件のSTEP
        ]),
        
        //スライド全体のスタイル
        sliderStyle() {
            const translate = (100 / this.slidesToShow) * this.currentSlider;
            return {
                display: 'flex',
                transition: 'transform 0.3s ease',
                transform: `translateX(-${translate}%)`
            };
        },

        // 各スライド要素のスタイル
        itemStyle() {
            return {
                flex: `0 0 ${100 / this.slidesToShow}%`,
                boxSizing: 'border-box'
            };
        },

        //戻るボタン表示可否
        canPrev() {
            return this.currentSlider > 0;
        },

        //次へボタン表示可否
        canNext() {
            return this.currentSlider < this.recentSteps.length - this.slidesToShow;
        },

    },

    methods: {

        //タイムスタンプから時間を消す
        removeTime,

        //詳細ページに遷移
        goStepShow,
        
        //画面サイズに対する表示数(判定値は_variables.scssとあわせること)
        updateSlidesToShow() {
            const width = window.innerWidth;
            if (width < 600) {
                this.slidesToShow = 1;
            } else if (width < 980) {
                this.slidesToShow = 2;
            } else {
                this.slidesToShow = 3;
            }

            if (this.currentSlider > this.recentSteps.length - this.slidesToShow) {
                this.currentSlider = Math.max(0, this.recentSteps.length - this.slidesToShow);
            }
        },

        //前のスライドに戻る
        prevSlider() {
            if (this.currentSlider > 0) {
                this.currentSlider--;
            }
        },

        //次のスライドに戻る
        nextSlider() {
            if (this.currentSlider < this.recentSteps.length - this.slidesToShow) {
                this.currentSlider++;
            }
        },
    }
}
</script>
