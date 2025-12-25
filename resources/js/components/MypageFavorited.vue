getMyChallengedSteps<template>
<div>
    <base-step-list
        title="お気に入りしたSTEP一覧"
        list-type="favorited"
        url="/api/user/favorites"
        :formatter="formatFavorited"
        @cancel-favorite="cancelFavorite"
    ></base-step-list>
</div>
</template>
<script>
import pagination from '../pagination'; 
   
export default {
    mixins: [pagination],

    methods: {
        //一覧コンポ表示のためのデータ整形：投稿したSTEP
        formatFavorited(step) {
            return {
                id: step.id,
                step_name: step.step_name,
                category_name: step.category?.category_name || '',
                total_estimated_time: step.total_estimated_time,
                created_at: step.created_at,
                challenges_count: step.challenges_count,
                
                link_id: step.pivot.id,     //一覧上のアイコンクリック時のリンク先id

                owner: {
                    name: step.owner?.name || '',
                    user_img: step.owner?.user_img || '',
                },
            };
        },

        //お気に入り解除
        async cancelFavorite(id) {
            
            const response = await axios.delete(`/api/favorites/${id}`);
            this.$store.dispatch('flashMsg/showMsg', response.data.msg);
            
            //一覧再表示
            this.removeStepById(id);
        },
    },
}
</script>