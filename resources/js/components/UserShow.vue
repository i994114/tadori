<template>
<div>
    <h2 class="c-title c-title--outer">利用者情報</h2>
    <div class="c-mainArea">
        <div class="p-profile c-container c-container--colored">

            <div class="p-profile__container">
                <!-- 左カラム：画像 -->
                <div class="p-profile__profileImg">
                    <img class="c-avatar c-avatar--big" :src="`/storage/uploads/${user.user_img}`" />
                </div>

                <!-- 右カラム：情報 + 自己紹介 -->
                <div class="p-profile__description p-profile__description--few">
                    <p class="u-mb-l">{{ user.name }}</p>
                </div>
            </div>

            <!-- 下部：自己紹介 -->
            <div class="p-profile__profileMessage">
                {{ user.user_profile }}
            </div>
        </div>

    </div>
</div>
</template>
<script>
export default {
    
    data() {
        return {
            user: '',
        };
    },

    mounted() {
        const id = this.$route.params.id;
        this.getUser(id);
    },

    methods: {

        async getUser(id) {
            const response = await axios.get(`/api/users/${id}`);
            this.user = response.data;
        }
    }
}
</script>