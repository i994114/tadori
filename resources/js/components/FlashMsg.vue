<template>
    <transition name="fade">
        <div v-if="flashMsg">
            <div class="c-flashMessage">{{flashMsg}}</div>
        </div>
    </transition>
</template>
<script>
import {mapState} from 'vuex';
export default {
    
    computed: {
        ...mapState('flashMsg', ['flashMsg'])
    },

}
</script>
<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity 2s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>