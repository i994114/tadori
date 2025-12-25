<template>
<div class="c-img">
    <label class="c-label">{{ title }}</label>

    <!-- 描画 -->
    <label class="c-img__dropArea"  @dragover="onDragOver"  @dragleave="onDragLeave" @drop="onDrop" >
        <input type="file"  :name="name" class="c-img__uploadField" @change="handleImgChange">
        <img :src="itemUri" class="c-img__prev" >
        <span class="c-img__note">クリック、または画像ファイルをドラッグ＆ドロップ</span>
    </label>
    
    <!-- 選択動画削除ボタン -->
    <div class="c-img__deleteArea">
        <i class="far fa-trash-alt" @click="deleteImg" ></i>
    </div>

    <!-- エラー表示 -->
    <base-error-display :errors="errors" ></base-error-display>

</div>

</template>
<script>
export default {
    
    props: {
        title: String,
        value: [String, File],
        name: String,
        errors: Array,
    },
    
    data() {
        return {
            itemUri: this.value,
        };
    },

    watch: {

        //親→子データ受け取り
        value(newValue) {

            if (typeof newValue === 'string') { //DBに画像保存済みの場合
                this.itemUri = '/storage/uploads/' + newValue;
            } else if (newValue instanceof File) {  //画像選択操作された場合
                this.itemUri = newValue;
            } else {
            }
            
        },

    },

    methods: {

        //画像要素上でドラッグ操作をしていることを検知し、画面上に線を描画
        onDragOver(e) {
            e.preventDefault();
            e.currentTarget.style.border = '3px #ccc dashed';
        },

        //画像要素上からドラッグしているカーソルが離れたことを検知し、画面上の線を消す
        onDragLeave(e) {
            e.preventDefault();
            e.currentTarget.style.border = 'none';
        },

        //画像要素上に画像ファイルをドラッグしたことを検知し、ファイル名を取得する
        onDrop(e) {
            e.preventDefault();
            const file = e.dataTransfer.files[0];
            this.processFile(file);
        },

        //ユーザーがドラッグでなく、エリアクリックでファイルを選択したときに画像をプレビュー
        handleImgChange(e) {
            const file = e.target.files[0];
            this.processFile(file);
        },

        //選択した画像を削除する
        deleteImg() {
            this.processFile(null);
        },

        //画像ファイルを処理する共通のメソッド
        processFile(file) {
            
            if (file) {
                const reader = new FileReader();

                reader.onload = (event) => {
                    this.itemUri = event.target.result;
                };
                
                //指定した file を Base64エンコードされたデータURL に変換して読み込む
                //(これを書かないとonloadが発火しないので注意)
                reader.readAsDataURL(file);
            } else {
                this.itemUri = null;
            }
            //コントローラに渡す用のimgデータ
            this.$emit('input', file);
        },

    }
}
</script>