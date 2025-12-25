<template>
<div>
    <base-step-list
        title="投稿したSTEP一覧"
        list-type="posted"
        url="/api/user/posted-steps"
        :formatter="formatPosted"
        @edit-step="editStep"
    ></base-step-list>
</div>
</template>
<script>
export default {
    methods: {
        //STEP編集
        editStep(step) {
            
            //STEP情報をVuexに登録
            this.$store.dispatch('currentStep/fetchCurrentStep', step );
            
            //編集画面へ遷移
            this.$router.push({ name: 'step-edit', params: { id: step.id } });
        },

        //一覧コンポ表示のためのデータ整形：投稿したSTEP
        formatPosted(step) {
            return {
                id: step.id,
                step_name: step.step_name,
                category_name: step.category?.category_name || '',
                total_estimated_time: step.total_estimated_time,
                created_at: step.created_at,
                deleted_at: step.deleted_at,
                challenges_count: step.challenges_count,
                
                link_id: step.id,   //一覧上のアイコンクリック時のリンク先id
                
                owner: {
                    name: step.owner?.name || '',
                    user_img: step.owner?.user_img || '',
                },
            };

        },

        //STEP編集
        editStep(step) {
            
            //STEP情報をVuexに登録
            this.$store.dispatch('currentStep/fetchCurrentStep', step );
            
            //編集画面へ遷移
            this.$router.push({ name: 'step-edit', params: { id: step.id } });
        },
    },
}
</script>