<!-- 必須キャリコンテーブル

<div class="kan_btn kan_open-btn"><button type="button">任意キャリコンを開く</button></div>
<div class="cc-menu-re">
    <div class="cc-detail-table-area-hum">
        <div class="kan_btn kan_close-btn"><button type="button">閉じる</button></div>
        <table class="kan-table cc-detail-table cc-detail-table-optional" style="background-color: white;">
            <thead class="cc-detail-thead">
                <tr class="cc-detail-headTr">
                    <?php foreach ($cc_times as $cc_time): ?>
                        <th class="cc-detail-th"><?= $cc_time["name"] ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="cc-detail-tbody">
                <?php foreach ($cc_slots as $cc_slot): ?>
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> -->