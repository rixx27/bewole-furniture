document.addEventListener('DOMContentLoaded', function () {

    const priceInput = document.getElementById('price');

    if (priceInput) {

        // Format nilai awal (misal saat edit)
        if (priceInput.value !== '') {
            let value = priceInput.value.replace(/\D/g, '');
            priceInput.value = new Intl.NumberFormat('id-ID').format(value);
        }

        // Format saat mengetik
        priceInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');

            if (value === '') {
                this.value = '';
                return;
            }

            this.value = new Intl.NumberFormat('id-ID').format(value);
        });

        // Sebelum form dikirim, hilangkan titik
        priceInput.form.addEventListener('submit', function () {
            priceInput.value = priceInput.value.replace(/\./g, '');
        });
    }

});