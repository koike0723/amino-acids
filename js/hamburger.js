// ヘッダー用ハンバーガーメニュー追加
// ハンバーガーメニューの開閉処理
// １．必要な要素を取得する（hamburgerボタン、body要素）
const humBtn = document.getElementById('menu-toggle');
const humMenu = document.getElementById('hum-menu');
const bodyElm = document.body;

humBtn.addEventListener('change', () => {
    if (humBtn.checked) {
        humMenu.classList.add('is-open');
        bodyElm.classList.add('active');
    } else {
        humMenu.classList.remove('is-open');
        bodyElm.classList.remove('active');
    }
});