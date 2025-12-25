import { method } from "lodash";

export default {
    
    computed: {

        //STEP詳細ページに渡すパラメータ
        routeQuery() {
            return {
                page: this.currentPage,
                keyword: this.keyword,
                category: this.selectedSort.category,
                sort: this.selectedSort.sort,
                selectedEstimatedTime: this.selectedSort.selectedEstimatedTime
            }
        }
    },

    watch: {
        '$route.query'(newQuery) {
            //ページ番号
            this.currentPage = Number(newQuery.page) || 1;

            //検索条件
            this.selectedSort = {
                category: newQuery.category || '',
                selectedEstimatedTime: newQuery.selectedEstimatedTime || '',
                sort: newQuery.sort || '',
            };
            this.keyword = newQuery.keyword || '';
        }
    },

    methods: {

        //検索条件をバックエンドに渡し、検索結果を受け取る(初回の検索条件なしでも)
        //resetPageは、詳細ページから戻るボタン押した時、詳細ページにいくまえの一覧を表示するための処理
        async searchSteps(resetPage = true) {

            try {
                if (resetPage) {
                    this.currentPage = 1;
                }
                
                this.updateRouteQuery();

                const response = await axios.get(this.url, {
                    params: {
                        categoryId: this.selectedSort.category,
                        selectedEstimatedTime: this.selectedSort.selectedEstimatedTime,
                        sortId: this.selectedSort.sort,
                        keyword: this.keyword,
                    },
                });
                
                //取得したーデータに加え、一覧に表示するアイコン情報を追加する
                const stepWithAction = response.data.map(step => {
                    const formatted = this.formatter? this.formatter(step) : step;
                    
                    return {
                        ...formatted,
                        action: this.getActionByListType()
                    };
                });

                this.setSteps(stepWithAction);

                //データ反映後に paginate の内部 state を合わせる
                this.$nextTick(() => {
                    // key を変えて再マウント（念のため）
                    this.forcePageVersion++;
                    this.$refs.paginate.selected = Number(this.currentPage);
                });
            } catch {
                //app.jsで共通化のためなし
            } finally {
                this.loaded = true;
            }
        },
        
        //URL更新用
        //(STEP詳細からブラウザバックしたとき、もとのページ表示するため、STEP詳細遷移前の検索条件を設定)
        updateRouteQuery() {
            this.$router.replace({
                query: {
                    page: this.currentPage,
                    
                    category: this.selectedSort.category,
                    selectedEstimatedTime: this.selectedSort.selectedEstimatedTime,
                    sort: this.selectedSort.sort,
                    keyword: this.keyword,
                },
            });
        },

    },
};