<template>
<div class="l-sidebar">
    <sidebar-right-mypage v-if="type === 'mypage'" ></sidebar-right-mypage>
    <sidebar-right-general v-else ></sidebar-right-general>
</div>
</template>
<script>
export default {
    props: {
        type: String,
    }
}
</script>