// ─────────────────────────────────────────────────────────────────────
// Handlers globais de UI — funcionam em qualquer página que use as
// convenções de data-attribute abaixo.
// ─────────────────────────────────────────────────────────────────────

document.addEventListener('click', (e) => {

    // ── Carrosséis horizontais (data-carousel-prev/next="ID") ────────
    const prev = e.target.closest('[data-carousel-prev]');
    const next = e.target.closest('[data-carousel-next]');
    if (prev || next) {
        const btn      = prev || next;
        const targetId = btn.dataset.carouselPrev || btn.dataset.carouselNext;
        const target   = document.getElementById(targetId);
        if (target) {
            const passo = Math.max(target.clientWidth * 0.6, 200);
            target.scrollBy({ left: prev ? -passo : passo, behavior: 'smooth' });
        }
        return;
    }

    // ── Toggle hidden (data-toggle="ID") ─────────────────────────────
    const toggleBtn = e.target.closest('[data-toggle]');
    if (toggleBtn) {
        const target = document.getElementById(toggleBtn.dataset.toggle);
        if (target) {
            target.classList.toggle('hidden');
            if (!target.classList.contains('hidden')) {
                target.querySelector('textarea, input[type=text]')?.focus();
            }
        }
        return;
    }

    // ── Modal: abrir (data-modal-open="ID") ──────────────────────────
    const opener = e.target.closest('[data-modal-open]');
    if (opener) {
        const modal = document.getElementById(opener.dataset.modalOpen);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.querySelector('input[type=text], textarea')?.focus();
        }
        return;
    }

    // ── Modal: fechar (data-modal-close="ID") ────────────────────────
    const closer = e.target.closest('[data-modal-close]');
    if (closer) {
        const modal = document.getElementById(closer.dataset.modalClose);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        return;
    }

    // ── Modal: clique no backdrop fecha ──────────────────────────────
    const backdrop = e.target.closest('[data-modal-backdrop]');
    if (backdrop && e.target === backdrop) {
        backdrop.classList.add('hidden');
        backdrop.classList.remove('flex');
    }
});

// ── Modal: ESC fecha qualquer um aberto ──────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('[data-modal-backdrop].flex').forEach((m) => {
        m.classList.add('hidden');
        m.classList.remove('flex');
    });
});
