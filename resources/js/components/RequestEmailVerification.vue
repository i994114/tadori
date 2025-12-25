<template>
<div class="c-container">
    メール認証をお願いします再送は<a @click="sendAuthEmail(true)" >こちらをクリックしてください</a>
</div>
</template>
<script>
import { mapState } from 'vuex';

export default {

    async created() {
        this.sendAuthEmail(false);    //本画面遷移時点でメールを飛ばすようにする
    },

    methods: {

        //認証用メールを再送する
        async sendAuthEmail(sts) {
            try {
                const response = await axios.get('/email/resend');
                if (sts) {
                    //再送ボタンをおしたときのみ、再送メッセージを表示
                    this.$store.dispatch('flashMsg/showMsg', response.data.msg);
                }
            } catch(e) {
                this.$store.dispatch('flashMsg/showMsg', '処理に失敗しました。もう一度お試しください');
            }
        }
    }
}
</script>