<style>
    /* Utilitaires commercial — même charte que l’admin (rouge Domini) */
    .commercial-muted {
        color: #666;
        font-size: 0.875rem;
    }

    .commercial-back-link {
        display: inline-block;
        margin-bottom: 1.5rem;
        color: #FF0000;
        font-weight: 600;
        text-decoration: none;
    }

    .commercial-back-link:hover {
        text-decoration: underline;
    }

    .commercial-page-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .commercial-filter-bar {
        margin-bottom: 1.5rem;
    }

    .commercial-filter-bar select,
    .commercial-form-select {
        padding: 0.5rem;
        border: 2px solid #E5E5E5;
        border-radius: 6px;
        background: white;
        font-size: 0.9375rem;
        min-width: 220px;
    }

    .commercial-form-grid {
        display: grid;
        gap: 1.5rem;
    }

    .commercial-form-grid--2 {
        grid-template-columns: repeat(2, 1fr);
    }

    @media (max-width: 640px) {
        .commercial-form-grid--2 {
            grid-template-columns: 1fr;
        }
    }

    .commercial-form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #000000;
    }

    .commercial-form-input,
    .commercial-form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #E5E5E5;
        border-radius: 8px;
        font-size: 1rem;
        font-family: inherit;
        background: #fff;
        color: #000;
    }

    .commercial-form-input:focus,
    .commercial-form-group textarea:focus,
    .commercial-form-select:focus {
        outline: none;
        border-color: #FF0000;
    }

    .commercial-section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #000000;
        margin: 0 0 1rem;
    }

    .commercial-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .commercial-box-header {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .commercial-alert {
        background-color: #FFF3E0;
        color: #E65100;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #FF0000;
    }

    .commercial-alert a {
        color: #FF0000;
        font-weight: 600;
        margin-left: 0.75rem;
        text-decoration: none;
    }

    .commercial-alert a:hover {
        text-decoration: underline;
    }

    .commercial-table-wrap {
        overflow-x: auto;
    }

    .commercial-pagination {
        padding: 1rem 0 0.25rem;
    }

    .commercial-casier-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .commercial-map-block {
        margin-top: 0.5rem;
    }
</style>
