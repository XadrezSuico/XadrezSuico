<style>
    /* Fallback estável: não depende só de utilitários Tailwind (Bootstrap legado sobrescreve alguns) */
    .v2-event-native .v2-action-grid {
        display: grid;
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .v2-event-native .v2-action-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .v2-event-native .v2-action-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .v2-event-native .v2-action-card {
        display: flex;
        min-height: 5.75rem;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 0.75rem;
        border: 1px solid #e9d5ff;
        background: #fff;
        padding: 1rem;
        text-align: center;
        text-decoration: none !important;
        color: #374151 !important;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: border-color 0.15s, background-color 0.15s;
    }
    .v2-event-native .v2-action-card:hover {
        border-color: #c084fc;
        background: #faf5ff;
        color: #581c87 !important;
    }
    .v2-event-native .v2-action-card--success {
        border-color: #a7f3d0;
        background: #ecfdf5;
        color: #065f46 !important;
    }
    .v2-event-native .v2-action-card--primary {
        border-color: #ddd6fe;
        background: #f5f3ff;
        color: #581c87 !important;
    }
    .v2-event-native .v2-action-card--warning {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e !important;
    }
    .v2-event-native .v2-action-card.is-disabled {
        opacity: 0.55;
        pointer-events: none;
    }
    .v2-event-native .v2-action-card__icon {
        display: inline-flex;
        height: 2rem;
        width: 2rem;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
    }
    .v2-event-native .v2-action-card__label {
        display: block;
        width: 100%;
        word-break: break-word;
    }
    .v2-event-native .v2-panel-grid {
        display: grid;
        gap: 1.5rem;
    }
    @media (min-width: 1024px) {
        .v2-event-native .v2-panel-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .v2-event-native .v2-panel-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .v2-event-native .v2-stat-grid {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .v2-event-native .v2-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .v2-event-native .v2-btn-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .v2-event-native .v2-btn-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (min-width: 1280px) {
        .v2-event-native .v2-stat-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .v2-event-native .v2-btn-grid {
        display: grid;
        gap: 0.75rem;
    }
    .v2-event-native .mt-3 { margin-top: 0.75rem; }
    .v2-event-native .mt-4 { margin-top: 1rem; }
    .v2-event-native .mt-6 { margin-top: 1.5rem; }
    .v2-event-native .space-y-4 > * + * { margin-top: 1rem; }
    .v2-event-native .border-t { border-top: 1px solid #f3e8ff; }
    .v2-event-native .pt-4 { padding-top: 1rem; }
</style>
