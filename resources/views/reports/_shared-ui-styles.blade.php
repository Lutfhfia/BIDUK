<style>
    .print-type-card {
        position: relative;
        border: 1.5px solid #dce3ed;
        border-radius: 10px;
        padding: 18px 12px;
        min-height: 145px;
        text-align: center;
        cursor: pointer;
        background: #fff;
        transition: all .2s ease;
        overflow: hidden;
    }

    .print-type-card:hover {
        border-color: var(--biduk-primary, #1a7a4c);
        background: var(--biduk-primary-light, #e8f5ee);
        transform: translateY(-1px);
    }

    .print-type-card.active {
        border-color: var(--biduk-primary, #1a7a4c);
        background: var(--biduk-primary-light, #e8f5ee);
        box-shadow: 0 0 0 1px rgba(26, 122, 76, .15);
    }

    .radio-circle {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 18px;
        height: 18px;
        border: 1.5px solid #cbd5e1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        font-size: 11px;
    }

    .print-type-card.active .radio-circle {
        border-color: var(--biduk-primary, #1a7a4c);
        background: var(--biduk-primary, #1a7a4c);
        color: #fff;
    }

    .print-icon {
        font-size: 35px;
        color: var(--biduk-primary, #1a7a4c);
        margin-bottom: 7px;
    }

    .print-type-card h6 {
        margin-bottom: 5px;
        color: #212529;
    }

    .print-type-card small {
        display: block;
        line-height: 1.45;
    }

    .preview-toolbar {
        min-height: 42px;
        border: 1px solid #e3e8ef;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
    }

    .preview-wrapper {
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
        background: #343a40;
        border: 1px solid #dce3ed;
    }

    .preview-frame {
        display: block;
        width: 100%;
        height: 620px;
        border: 0;
        background: #fff;
    }

    .preview-placeholder {
        min-height: 620px;
        border: 2px dashed #dce3ed;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }

    .preview-placeholder-icon {
        width: 75px;
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--biduk-primary-light, #e8f5ee);
        color: var(--biduk-primary, #1a7a4c);
        font-size: 38px;
    }

    .information-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff8e8;
        color: #f59e0b;
        font-size: 20px;
    }

    .form-select,
    .form-control {
        min-height: 42px;
        border-color: #dce3ed;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: var(--biduk-primary, #1a7a4c);
        box-shadow: 0 0 0 0.15rem rgba(26, 122, 76, 0.15);
    }

    .alert {
        border-radius: 8px;
    }

    .student-search-item {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .student-search-item:hover,
    .student-search-item:focus {
        background-color: var(--biduk-primary-light, #e8f5ee) !important;
    }

    @media (max-width: 991.98px) {
        .preview-frame,
        .preview-placeholder {
            min-height: 500px;
            height: 500px;
        }
    }
</style>
