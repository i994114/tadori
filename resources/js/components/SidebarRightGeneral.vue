<template>
<div>
    <!-- チャレンジ数の多いSTEP -->
    <div class="l-sidebar__rankingArea">
        <h3 class="l-sidebar__rankingTitle" >チャレンジSTEP</h3>
        <ul v-if="mostChallengedSteps.length" class="l-sidebar__rankingList">
            <li class="l-sidebar__rankingItem" v-for="(step, index) in mostChallengedSteps" :key="`challenge-${step.id}`">
                <!-- トップ3 -->
                <span v-if="index &lt; 3" class="l-sidebar__rankHigh" :class="'l-sidebar__rankHigh--best' + (index + 1)">
                    <i :class="index &lt; 2? 'fas fa-crown' : 'fas fa-medal'"></i>
                </span>
                <!-- それ以外 -->
                <span v-else class="l-sidebar__rankNormal">{{ index + 1 }}</span>

                <!-- 詳細ページリンク -->
                <p  class="l-sidebar__stepName c-hover" @click="goStepShow(step)"> {{ step.step_name }}</p>
            </li>
        </ul>
        <p v-else>まだありません</p>
    </div>

    <!-- 新着STEP -->
    <div class="l-sidebar__rankingArea l-sidebar__rankingArea--rest">
        <h3 class="l-sidebar__rankingTitle">新着STEP</h3>
        <ul v-if="newSteps.length" class="l-sidebar__rankingList">
            <li class="l-sidebar__rankingItem" v-for="(step, index) in newSteps" :key="`new-${step.id}`">
                <!-- 順位 -->
                <span class="l-sidebar__rankNormal">{{ index + 1 }}</span>

                <!-- 詳細ページリンク -->
                <p  class="l-sidebar__stepName c-hover" @click="goStepShow(step)"> {{ step.step_name }}</p>
            </li>
        </ul>
        <p v-else>まだありません</p>
    </div> 
</div>
</template>
<script>
import { mapGetters } from 'vuex';
import { goStepShow } from '../common';

export default {

    computed: {
        ...mapGetters('stepList', [
            'newSteps', //最新のSTEP
            'mostChallengedSteps',  //チャレンジ数の多いSTEP
        ]),
    },

    methods: {
        goStepShow,
    }
}
</script>