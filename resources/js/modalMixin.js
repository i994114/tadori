import { disableBodyScroll, enableBodyScroll } from "body-scroll-lock";
import { method } from "lodash"

export default {
    data() {
        return {
            modal: {
                show: false,    //modal開閉
                text: '',   //modal内の文章
            }
        }
    },

    methods: {
        //modalを開く
        showModal(opts = {}) {
            this.modal.show = true;
            this.modal.text = opts.text || '';

            // 現在のスクロール位置を記録
            this.scrollY = window.scrollY;

            // スクロール位置を維持したままbodyを固定
            document.body.style.position = 'fixed';
            document.body.style.top = `-${this.scrollY}px`; // ← スクロール位置を反映
            document.body.style.left = '0';
            document.body.style.right = '0';

        },

        closeModal() {
            this.modal.show = false;

            const scrollY = this.scrollY || 0;

            // 固定解除
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';

            // 元の位置に戻す
            window.scrollTo(0, scrollY);
        }
    }
}