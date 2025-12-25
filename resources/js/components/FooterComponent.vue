<template>
    <div>
        <footer class="l-footer js-footer">
            <div class="l-footer__links">
                <router-link :to="{ name: 'step-index' }" class="l-footer__links">STEP一覧</router-link>
                <router-link :to="{ name: 'privacy-policy' }" class="l-footer__links" target="_blank" rel="noopener noreferrer">プライバシーポリシー</router-link>
                <router-link :to="{ name: 'terms-of-service' }" class="l-footer__links" target="_blank" rel="noopener noreferrer">利用規約</router-link>
                <router-link :to="{ name: 'user-show', params: { id: 1} }" class="l-footer__links" target="_blank" rel="noopener noreferrer">管理人について</router-link>
            </div>

            <div class="l-footer__copy">
                Copyright
                <router-link :to="{ name: 'welcome' }" class="l-footer__companyName">株式会社STEP</router-link>
                All Rights Reserved.
            </div>
        </footer>
    </div>
</template>
<script>
export default {
    mounted() {

        //フッター位置を一番下にする
        function adjustFooterPosition() {
            
            //フッター要素を取得
            const ftr = document.querySelector('.js-footer');

            //ウィンドウの高さを取得
            const windowHeight = window.innerHeight;

            //ウィンドウの高さがフッターの一番したよりも大きい場合、フッターを固定
            /*
                windowHeight: ウィンドウの高さ
                ftr.offsetTop: フッターの上部からウィンドウ上部までの距離
                ftr.offsetHeight: フッターの高さ
            */
           if (windowHeight > ftr.offsetTop + ftr.offsetHeight) {
                //フッターを固定
                ftr.style.position = 'fixed';
                ftr.style.top = windowHeight - ftr.offsetHeight + 'px';
           } else {
                //フッターを通常のフローに戻す
                ftr.style.position = 'static';
           }
        }
        //初回の実行
        adjustFooterPosition();

        //ウィンドウのリサイズイベントを監視
        window.addEventListener('resize', function() {
            adjustFooterPosition();
        });
    }
}
</script>