//ドラッグ＆ドロップ用コマンド
//ドラッグ開始
const drag_drop_area = document.getElementById("drag_drop_area");
let drag_ele = null;
let old_td = null;
let new_td = null;

drag_drop_area.addEventListener("dragstart", (e) => {
    if (e.target.closest('.cc-detail-student-card') && (e.target.dataset.bookingId != "empty")) {
        e.dataTransfer.setData('text/plain', e.target.dataset.bookingId);
        drag_ele = e.target;    //ここの要素取れてるか怪しい
        old_td = e.target.closest('td');
        e.target.classList.add('opacity-50');
    }
});

drag_drop_area.addEventListener('dragend', (e) => {
    if (e.target.closest('.cc-detail-student-card')) {
        e.target.classList.remove('opacity-50');
    }
});

// ドラッグして何かの上にいるとき
drag_drop_area.addEventListener("dragover", (e) => {
    if (e.target.closest('.cc-detail-student-card')) {
        e.preventDefault();
    }
});

// // ドラッグして何かの上から離れたとき
// drag_drop_area.addEventListener("dragleave", (e) => {
//     if (e.target.closest('.cc-detail-student-card')) {
//         e.target.closest('td').classList.remove('accent-cell');
//     }
// });

// ドロップしたとき
drag_drop_area.addEventListener("drop", (e) => {
    if (e.target.closest('.cc-detail-student-card')) {
        e.preventDefault();
        new_td = e.target.closest('td');
        old_td = null;
    }
});