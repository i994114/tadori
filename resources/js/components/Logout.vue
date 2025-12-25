<template>
    <div>
        <p>ログアウト中...</p>
    </div>
</template>
<script>
import { STORAGE_NAMES } from '../const';

export default {

    created() {
        this.logout();

    },



    methods: {
        
        //ログアウト処理
        async logout() {

            const response = await axios.post('/api/logout');

            //ローカルストレージのトークン情報を削除
            this.$store.dispatch('user/logout');

            this.$store.dispatch('flashMsg/showMsg', response.data.message);

            //ログイン画面に遷移
            this.$router.replace({name: 'login'});
        },
    },
}
</script>