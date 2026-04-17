// テーブル用のハンバーガーメニュー追加
// ハンバーガーメニューの開閉処理
// １．必要な要素を取得する（hamburgerボタン、body要素）
let humSwitch = document.getElementById('cc-burger');
let humTable = document.getElementById('cc-hum');
// ２．hamburgerボタンにクリックイベントを登録
// ３．hamburgerボタンがクリックされたら、body要素に「open」クラスを付けたり外したりする
humSwitch.addEventListener('click', () => {
    // if(bodyElm.classList.contains('open')){
    // bodyElm.classList.remove('open');
    // }else{
    // bodyElm.classList.add('topPage');
    // }
    humSwitch.classList.toggle('open-Table');
    humTable.classList.toggle('active-Table');
})