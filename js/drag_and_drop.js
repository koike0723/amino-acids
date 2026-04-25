const drag_drop_area = document.getElementById("drag_drop_area");
let draggedBookingId  = null;
let isDraggingCcPlus  = false;

drag_drop_area.addEventListener("dragstart", (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (!card || card.dataset.bookingId === "empty") return;

    isDraggingCcPlus = card.dataset.isCcPlus === "1";

    draggedBookingId = card.dataset.bookingId;
    card.classList.add('opacity-50');
});

drag_drop_area.addEventListener("dragend", (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (card) card.classList.remove('opacity-50');
    draggedBookingId = null;
    isDraggingCcPlus = false;
});

drag_drop_area.addEventListener("dragover", (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (!card) return;

    if (isDraggingCcPlus) {
        // CC+からのドラッグ：CCライン枠の空きセルのみ許可
        if (card.closest('.cc-plus-table-area')) return;
        if (card.dataset.bookingId !== "empty") return;
    } else {
        // CCラインからのドラッグ：CC+エリアへのドロップは不可
        if (card.closest('.cc-plus-table-area')) return;
    }

    e.preventDefault();
});

drag_drop_area.addEventListener("drop", async (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (!card || !draggedBookingId) return;
    if (card.closest('.cc-plus-table-area')) return;
    e.preventDefault();

    const targetBookingId = card.dataset.bookingId;
    const td = card.closest('td');
    const body = new FormData();
    let url;

    if (isDraggingCcPlus) {
        // CC+確定登録：空きCCライン枠のみ
        if (targetBookingId !== "empty") return;
        url = 'php_do/cc_plus_to_line_do.php';
        body.append('cc_plus_booking_id', draggedBookingId);
        body.append('to_slot_id',         td.dataset.slotId);
        body.append('to_time_id',         td.dataset.timeId);
    } else {
        // CCライン内の移動・入れ替え
        if (targetBookingId === draggedBookingId) return;
        if (targetBookingId === "empty") {
            url = 'php_do/cc_booking_move_do.php';
            body.append('booking_id',  draggedBookingId);
            body.append('to_slot_id',  td.dataset.slotId);
            body.append('to_time_id',  td.dataset.timeId);
        } else {
            url = 'php_do/cc_booking_swap_do.php';
            body.append('booking_id_a', draggedBookingId);
            body.append('booking_id_b', targetBookingId);
        }
    }

    try {
        const res  = await fetch(url, { method: 'POST', body });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert('移動に失敗しました: ' + (data.message ?? ''));
        }
    } catch {
        alert('通信エラーが発生しました');
    }
});
