// resources/js/mixins/pagination.js

export default {
    data() {
        return {
            perPage: 12,    //1ページに表示するSTEP数
            currentPage: 1,
            forcePageVersion: 0, //ページネーション再描画トリガー

        };
    },

    computed: {

        //一覧表示するSTEP情報
        steps() {
            return this.$store.state.stepList.steps;
        },

        //表示する総STEP数
        totalSteps() {
            return this.steps.length;
        },

        // 総ページ数
        pageCount() {
            return Math.ceil(this.totalSteps / this.perPage);
        },

        // 現在のページに表示するSTEP
        paginatedSteps() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.steps.slice(start, end);
        },

        // 現在表示中の開始インデックス
        currentStart() {
            return this.steps.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },

        // 現在表示中の終了インデックス
        currentEnd() {
            const end = this.currentPage * this.perPage;
            return end > this.totalSteps ? this.totalSteps : end;
        },

        //再描画用
        forcePage() {
            return Number(this.currentPage);
        }

    },

    watch: {
        //再描画用
        '$route.query.page'(newVal) {
            if (!newVal) return;
            const newPage = Number(newVal);
            if (newPage !== this.currentPage) {
                this.currentPage = newPage;

                //paginate内部を強制的に再描画
                this.forcePageVersion++;
                this.$nextTick(() => {
                    if (this.$refs.paginate && this.$refs.paginate.selected) {
                        this.$refs.paginate.selected = this.currentPage;
                    }
                });
            }
        },
    },

    mounted() {
        // ページ番号復元
        this.$nextTick(() => {
            this.currentPage = Number(this.$route.query.page) || 1;
            
            // searchStepsメソッドが定義されているコンポーネントでのみ検索処理を実行する
            // （pagination.jsは共通mixinのため、すべての利用先でsearchStepsが存在するとは限らない）
            if (typeof this.searchSteps === 'function') {
                this.searchSteps(false);
            }
        });
    },


    methods: {
        // ページ変更時の処理
        handlePageChange(pageNum) {
            this.currentPage = Number(pageNum);

            // クエリに page を反映
            this.$router.push({
                query: {
                    ...this.$route.query,
                    page: this.currentPage,
                }
            });

            //一番上のSTEPまでスクロール
            const targetElement = document.querySelector('.c-searchTitle');
            if (targetElement) {
            const topPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({ top: topPosition, behavior: 'smooth' });
            }

        },


        // APIで取得したデータをVuexセット
        setSteps(data) {
            this.$store.dispatch('stepList/showSteps', data);
        },

        //削除、キャンセル後に一覧表示を更新
        removeStepById(id) {

            //VuexのSTEP一覧から当該STEPを削除
            this.$store.dispatch('stepList/removeStepById', id);
            
            //削除、キャンセルしたので表示するSTEP数を再計算

            //ページ番号調整
            if (this.currentPage > this.pageCount) {
                this.currentPage = this.pageCount || 1;
            }
        }
    },
};
