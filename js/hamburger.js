// ヘッダー用ハンバーガーメニュー追加
// ハンバーガーメニューの開閉処理
// １．必要な要素を取得する（hamburgerボタン、body要素）
const humBtn = document.getElementById('menu-toggle');
const humMenu = document.getElementById('hum-menu');
const bodyElm = document.body; //背景固定などを行いたい場合に使用
// ２．hamburgerボタンにクリックイベントを登録
humBtn.addEventListener('change', () => {
    // ３．hamburgerボタンがクリックされたら、body要素に「open」クラスを付けたり外したりする
    humMenu.classList.toggle('is-open');
    bodyElm.classList.toggle('active');
    //デバッグ用
    console.log('Menu staatus:', humBtn.checked);
});