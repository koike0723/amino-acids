// ヘッダー用ハンバーガーメニュー追加
// ハンバーガーメニューの開閉処理＆アニメーション追加
// １．必要な要素を取得する（hamburgerボタン、body要素）
document.addEventListener('DOMContentLoaded', () => {
    const humMenu = document.getElementById('hum-menu');
    const hamburger = document.getElementById('hamburger');
    const bodyElm = document.body;

    // ハンバーガーボタンをクリックした時の処理
    hamburger.addEventListener('click', () => {
        // activeクラスがあれば削除、なければ追加
        hamburger.classList.toggle('active');
        // is-openクラスがあれば削除、なければ追加
        humMenu.classList.toggle('is-open');
        // 背景スクロール固定用
        bodyElm.classList.toggle('active');
    });
});