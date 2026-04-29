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

    .public-submit-loading-file-status {
        margin-top: 0.6rem;
        min-height: 1.25rem;
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 600;
        line-height: 1.4;
        word-break: break-word;
    }

    .public-submit-loading-progress {
        margin-top: 1.25rem;
    }

    .public-submit-loading-progress-track {
        height: 0.55rem;
        overflow: hidden;
        border-radius: 9999px;
        background: #dbeafe;
    }

    .public-submit-loading-progress-bar {
        height: 100%;
        width: 0%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2563eb, #16a34a);
        transition: width 180ms ease;
    }

    .public-submit-loading-progress-text {
        margin-top: 0.65rem;
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 700;
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
        <p class="public-submit-loading-file-status hidden" data-submit-upload-file-status></p>
        <div class="public-submit-loading-progress hidden" data-submit-upload-progress>
            <div class="public-submit-loading-progress-track">
                <div class="public-submit-loading-progress-bar" data-submit-upload-progress-bar></div>
            </div>
            <p class="public-submit-loading-progress-text" data-submit-upload-progress-text>0%</p>
        </div>
    </div>
</div>
