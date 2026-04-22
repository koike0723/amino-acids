// テーブル用のハンバーガーメニュー追加
// ハンバーガーメニューの開閉処理
// １．必要な要素を取得する（hamburgerボタン、body要素）
const humSwitch = document.getElementById('table-toggle');
const humTable = document.getElementById('cc-menu');
const bodytable = document.body;
// ２．hamburgerボタンにクリックイベントを登録
humSwitch.addEventListener('change', () => {
    // ３．hamburgerボタンがクリックされたら、body要素に「open」クラスを付けたり外したりする
    if (humSwitch.checked) {
        humTable.classList.add('open-Table');
        bodytable.classList.add('active-Table');
    } else {
        humTable.classList.remove('close-Table');
        bodytable.classList.remove('active-Table');
    }
});