//概要：定数一覧

//ユーザプロフィールのデフォルトアイコンファイル名
//export const DEFAULT_ICON_FILE = 'default_icon.png';

//STEPのデフォルト画像ファイル名(後ろの数字は重複防止の適当な数字)
//export const DEFAULT_STEP_IMG = 'no_image20347273873907523435523635.jpg'

//

//文字数制限
export const MAX_LENGTH = {
    USER_NAME: 25,
    USER_PROFILE: 1000,
    USER_EMAIL: 254,    //ツイッターに倣い設定
    USER_PASSWORD: 50,    //ツイッターに倣い設定

    STEP_NAME: 50,      
    STEP_DETAIL: 1000,
    ESTIMATED_TIME: 100, //所要時間
    
    CATEGORY_NAME: 10,
    
    DISPLAY_STEP_NAME: 25, //一覧表示するSTEP名の表示上限文字数
    
    CONTACT_MESSAGE: 1000,  //お問い合わせページの文字数。とりあえず他のテキストボックスと同じにした
  };
  
// ドロップダウンタイプ
export const DROPDOWN_TYPE = {
    CATEGORIES: 'categories',          // STEPカテゴリ
  
};


//並び替えメニュー
export const SORT_MENU = [
    { id: 11, name: '新しい順' },
    { id: 12, name: '古い順' },
    { id: 21, name: 'チャレンジ多' },
    { id: 22, name: 'チャレンジ少' },
    { id: 31, name: '達成時間長' },
    { id: 32, name: '達成時間短' },
];


//ユーザ権限
export const USER_TYPE = {
    ADMIN: 1,   //システム管理者
    USER: 2,    //一般利用者
}

//STEP、子SETP識別子
export const STEP_TYPE = {
  STEP_CREATE: 1,
  SUB_STEP_CREATE: 2,
  
  STEP_EDIT: 11,
  SUB_STEP_EDIT: 12,

  STEP_SHOW: 21,
  SUB_STEP_SHOW: 22,

  STEP_INDEX: 31,
  SUB_STEP_INDEX: 32,
}

//ローカルストレージ名
export const STORAGE_NAMES = {
    ACCESS_TOKEN: 'access_token', //トークン
    REFRESH_EXPIRES: 'refresh_expires', //リフレッシュトークン期限
    
    STEP_ID: 'step id', //カレントのSTEP ID
};

// 販売価格帯
export const PRICE_RANGE_MENU = [
  { id: 1, name: '〜100円', value: '0-100' },
  { id: 2, name: '101〜300円', value: '101-300' },
  { id: 3, name: '301〜600円', value: '301-600' },
  { id: 4, name: '601〜1,000円', value: '601-1000' },
  { id: 5, name: '1,001〜2,000円', value: '1001-2000' },
];



