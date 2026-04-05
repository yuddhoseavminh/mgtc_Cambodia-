<style>
    .public-submit-loading-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.34);
        backdrop-filter: blur(2px);
    }

    .public-submit-loading-overlay.hidden {
        display: none;
    }

    .public-submit-loading-card {
        width: min(32rem, 100%);
        border-radius: 1.75rem;
        background: #ffffff;
        padding: 2rem 1.5rem 1.75rem;
        text-align: center;
        box-shadow: 0 24px 80px rgba(15, 23, 42, 0.22);
        animation: public-submit-loading-enter 180ms ease-out;
    }

    .public-submit-loading-title {
        color: #475569;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.4;
    }

    .public-submit-loading-spinner {
        width: 2.5rem;
        height: 2.5rem;
        margin: 1.15rem auto 0;
        border-radius: 9999px;
        border: 4px solid #2563eb;
        border-right-color: transparent;
        border-left-color: transparent;
        animation: public-submit-loading-spin 0.8s linear infinite;
    }

    @keyframes public-submit-loading-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes public-submit-loading-enter {
        from {
            opacity: 0;
            transform: translateY(8px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<div class="public-submit-loading-overlay hidden" data-submit-loading-overlay aria-hidden="true">
    <div class="public-submit-loading-card" role="status" aria-live="polite" aria-busy="true">
        <p class="public-submit-loading-title" data-submit-loading-message>សូមចាំបន្តិច....</p>
        <div class="public-submit-loading-spinner" aria-hidden="true"></div>
    </div>
</div>
