<template>
<div>    
    <base-step-list
        title="すべてのSTEP一覧"
        :loaded="loaded"
        :formatter="formatIndex"
        url="/api/steps"
    ></base-step-list>
</div>
</template>
<script>
export default {
    data() {
        return {
            loaded: false, //items一覧取得処理が終わったか否か(true:完了)
        };
    },

    methods: {

        //一覧コンポ表示のためのデータ整形：投稿したSTEP
        formatIndex(step) {
            return {
                id: step.id,
                step_name: step.step_name,
                category_name: step.category?.category_name || '',
                total_estimated_time: step.total_estimated_time,
                created_at: step.created_at,
                challenges_count: step.challenges_count,

                owner: {
                    name: step.owner?.name || '',
                    user_img: step.owner?.user_img || '',
                },

                action: {
                    event: '',
                    icon: ''
                }
            };
        },

        //STEP編集
        editStep(id) {
            
            //次の画面にいくまえに、step_idをVuexに登録
            this.$store.dispatch('currentStep/fetchCurrentStep', step);

            //次の画面に遷移
            this.$router.push({ name: 'step-edit', params: { id: id } });
        },
    },
}
</script>