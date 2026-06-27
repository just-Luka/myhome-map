<div id="confirm-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);backdrop-filter:blur(2px);align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border:1px solid var(--card-border);border-radius:14px;padding:28px 32px;min-width:300px;max-width:360px;box-shadow:0 20px 60px rgba(0,0,0,.6);text-align:center;">
        <div style="width:44px;height:44px;margin:0 auto 16px;background:var(--danger-bg);border-radius:50%;display:flex;align-items:center;justify-content:center;opacity:.8;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--danger-text)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </div>
        <div id="confirm-modal-title" style="font-size:15px;font-weight:700;color:var(--body-text);margin-bottom:6px;"></div>
        <div id="confirm-modal-desc"  style="font-size:13px;color:var(--subtle);margin-bottom:24px;line-height:1.5;"></div>
        <div style="display:flex;gap:10px;">
            <button id="confirm-modal-cancel" class="btn btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
            <button id="confirm-modal-ok"     class="btn btn-danger" style="flex:1;justify-content:center;"></button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal  = document.getElementById('confirm-modal');
    const btnOk  = document.getElementById('confirm-modal-ok');
    const btnCan = document.getElementById('confirm-modal-cancel');

    window.confirmAction = function ({ heading, body, confirmText = 'Remove', onConfirm }) {
        document.getElementById('confirm-modal-title').textContent = heading;
        document.getElementById('confirm-modal-desc').textContent  = body;
        btnOk.textContent = confirmText;
        btnOk.onclick     = () => { closeConfirmModal(); onConfirm(); };
        btnCan.onclick    = closeConfirmModal;
        modal.style.display = 'flex';
    };

    window.closeConfirmModal = function () {
        modal.style.display = 'none';
    };

    modal.addEventListener('click', e => { if (e.target === modal) closeConfirmModal(); });
})();
</script>
