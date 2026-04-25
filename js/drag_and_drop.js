const drag_drop_area = document.getElementById("drag_drop_area");
let draggedBookingId = null;

drag_drop_area.addEventListener("dragstart", (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (!card || card.dataset.bookingId === "empty") return;
    if (card.closest('.cc-plus-table-area')) return;
    draggedBookingId = card.dataset.bookingId;
    card.classList.add('opacity-50');
});

drag_drop_area.addEventListener("dragend", (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (card) card.classList.remove('opacity-50');
    draggedBookingId = null;
});

drag_drop_area.addEventListener("dragover", (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (!card) return;
    if (card.closest('.cc-plus-table-area')) return;
    e.preventDefault();
});

drag_drop_area.addEventListener("drop", async (e) => {
    const card = e.target.closest('.cc-detail-student-card');
    if (!card || !draggedBookingId) return;
    if (card.closest('.cc-plus-table-area')) return;
    e.preventDefault();

    const targetBookingId = card.dataset.bookingId;
    if (targetBookingId === draggedBookingId) return;

    const td = card.closest('td');
    const body = new FormData();
    let url;

    if (targetBookingId === "empty") {
        url = 'php_do/cc_booking_move_do.php';
        body.append('booking_id', draggedBookingId);
        body.append('to_slot_id', td.dataset.slotId);
        body.append('to_time_id', td.dataset.timeId);
    } else {
        url = 'php_do/cc_booking_swap_do.php';
        body.append('booking_id_a', draggedBookingId);
        body.append('booking_id_b', targetBookingId);
    }

    try {
        const res = await fetch(url, { method: 'POST', body });
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
