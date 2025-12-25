//共通メソッドを定義する。

import { DEFAULT_ICON_FILE, ITEM_STATUS, PREFECTURE_MENU, STEP_TYPE } from "./const";
import store from './store';
import router from './router';

//日時を「YYYY-MM-DD」形式に整形
export function removeTime(datetimeStr) {
    if (!datetimeStr  || typeof datetimeStr !== 'string') {
        return '';
    }
    return datetimeStr.slice(0, 10);
}

//分を時間に変換
export function minutesToHours(time) {
    
    // 数値でない場合の防御
    if (!time && time !== 0) return '';

    // 数値にキャスト
    const numTime = Number(time);

    // 上限チェック（28800分 = 480時間）
    if (numTime > 28800) {
        return '上限の480時間を超えています';
    }

    const hours = Math.floor(numTime / 60);
    const minutes = numTime % 60;

    let result = '';
    if (hours) result += `${hours}時間`;
    if (minutes) result += `${minutes}分`;

    // どちらも0なら「0分」とする
    return result || '0分';
}

//STEP削除済みか否かを判定し、STEP表示の際に当該STEPが削除済みかわかるようにする
export function showDeletedStatus(deleted_at) {
    if (deleted_at) {
        return '(削除済み)';
    } else {
        return '';
    }
}

//パンくずリスト
export function makeBreadcrumbs(type, stepId, subStepId, isLoggedIn) {
    
    //トップ
    const top = isLoggedIn
        ? { text: 'マイページ', to: { name: 'mypage' } }
        : { text: 'STEP一覧', to: { name: 'step-index' } };

    const crumbs = [top];

    //以降の階層
    if (isLoggedIn) {
        switch (type) {
            case STEP_TYPE.STEP_CREATE:
                crumbs.push({ text: 'STEP作成' });
                break;
            case STEP_TYPE.STEP_SHOW:
                crumbs.push(
                    { text: 'STEP詳細' }
                );
                break;
            case STEP_TYPE.SUB_STEP_INDEX:
                crumbs.push(
                    { text: 'STEP詳細', to: { name: 'step-show', params: { id: stepId } } },
                    { text: '子STEP一覧' }
                );
                break;
            case STEP_TYPE.STEP_EDIT:
                crumbs.push(
                    { text: 'STEP詳細', to: { name: 'step-show', params: { id: stepId } } },
                    { text: 'STEP編集' }
                );
                break;

            case STEP_TYPE.SUB_STEP_CREATE:
                crumbs.push(
                    { text: 'STEP詳細', to: { name: 'step-show', params: { id: stepId } } },
                    { text: '子STEP一覧', to: { name: 'sub-step-list', params: { id: stepId } } },
                    { text: '子STEP作成' }
                );
                break;
            case STEP_TYPE.SUB_STEP_SHOW:
                crumbs.push(
                    { text: 'STEP詳細', to: { name: 'step-show', params: { id: stepId } } },
                    { text: '子STEP一覧', to: { name: 'sub-step-list', params: { id: stepId } } },
                    { text: '子STEP詳細' }
                );
                break;
            case STEP_TYPE.SUB_STEP_EDIT:
                crumbs.push(
                    { text: '子STEP一覧', to: { name: 'sub-step-list', params: { id: stepId } } },
                    { text: '子STEP詳細', to: { name: 'sub-step-show', params: { id: subStepId } } },
                    { text: '子STEP編集' }
                );
                break;

            default:
                break;
        }
    } else {
        switch (type) {
            case STEP_TYPE.SUB_STEP_INDEX:
                crumbs.push(
                    { text: 'STEP詳細', to: { name: 'step-show', params: { id: stepId } } },
                    { text: '子STEP一覧' }
                );
                break;
            case STEP_TYPE.STEP_SHOW:
                crumbs.push(
                    { text: 'STEP詳細' }
                );
                break;

           case STEP_TYPE.SUB_STEP_SHOW:
                crumbs.push(
                    { text: 'STEP詳細', to: { name: 'step-show', params: { id: stepId } } },
                    { text: '子STEP一覧', to: { name: 'sub-step-list', params: { id: stepId } } },
                    { text: '子STEP詳細' }
                );
                break;

            default:
                break;
        }
    }

    return crumbs;
}

//直前のルートから、ユーザ操作後の戻り先を決める
export function decideNextRoute(router)
{
    if (window.history.length > 1) {
        router.go(-1);
    } else {
        //戻り先がない場合は一律でSTEP一覧に戻すこととする
        router.push({ name: 'step-index' });
    }
}

//開こうとしているSTEP IDをVuexに登録したうえで、一覧上でクリックしたSTEP詳細に遷移する
//(ぱんくずリストによる遷移を可能にするため)
export function goStepShow(step) {

    //次の画面にいくまえに、step_idをVuexに登録
    store.dispatch('currentStep/fetchCurrentStep', step);

    //次の画面に遷移
    router.push({ name: 'step-show', params: { id: step.id } });
}