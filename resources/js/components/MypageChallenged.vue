getMyChallengedSteps<template>
<div>
    <base-step-list
        title="チャレンジしたSTEP一覧"
        list-type="challenged"
        url="/api/user/challenges"
        :formatter="formatChallenged"
        @cancel-challenge="cancelChallenge"
    ></base-step-list>
</div>
</template>
<script>
import pagination from '../pagination';

export default {
    mixins: [pagination],
    
    methods: {

        //一覧コンポ表示のためのデータ整形：投稿したSTEP
        formatChallenged(step) {
            return {
                id: step.id,
                step_name: step.step_name,
                category_name: step.category?.category_name || '',
                total_estimated_time: step.total_estimated_time,
                created_at: step.created_at,
                deleted_at: step.deleted_at,
                challenges_count: step.challenges_count,
                
                link_id: step.pivot.id,   //一覧上のアイコンクリック時のリンク先id

                owner: {
                    name: step.owner?.name || '',
                    user_img: step.owner?.user_img || '',
                },

                progress_count: {
                    rate: step.progress_count?.rate || 0,
                    cleared: step.progress_count?.cleared || 0,
                    total: step.progress_count?.total || 0
                }
            };
        },

        //チャレンジキャンセル処理
        async cancelChallenge(id) {

            const response = await axios.delete(`/api/challenges/${id}/`);
            
            //フラッシュメッセージ
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);

            //一覧再表示
            this.removeStepById(id);
        },
    },
}
</script>