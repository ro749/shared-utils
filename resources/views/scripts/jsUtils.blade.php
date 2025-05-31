<script>
function formatNumber(value) {
    if (typeof value !== 'number') return value;
    return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function show(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'block';
}

function hide(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}
</script>