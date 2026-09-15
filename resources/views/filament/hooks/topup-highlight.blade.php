@if(request()->query('highlight'))
<style>
    .topup-highlight-row {
        outline: 2px solid #3b82f6 !important;
        outline-offset: 2px;
        border-radius: 0.5rem;
        animation: topup-highlight-fade 3s ease-out;
    }
    @keyframes topup-highlight-fade {
        0% { background-color: rgba(59, 130, 246, 0.22); }
        100% { background-color: rgba(59, 130, 246, 0.06); }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.querySelector('.topup-highlight-row');
        if (el) {
            setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'center' }), 300);
        }
    });
</script>
@endif
