const storeCategoryUrl = "/category/store"; // Atau dari Blade langsung inject

document.getElementById('btnSimpanKategori').addEventListener('click', function() {
    const newCategory = document.getElementById('inputNamaKategori').value;

    fetch(storeCategoryUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: newCategory,
            keterangan: "-"
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            // Misal mau auto nambahin ke select option jenis barang
            const selectJenis = document.querySelector('select[name="jenisbarang"]');
            const newOption = new Option(data.name, data.id);
            selectJenis.add(newOption);

            alert('Kategori berhasil ditambahkan!');
            $('#ModalTambahKategori').modal('hide');
        } else {
            alert('Gagal menambahkan kategori.');
        }
    })
    .catch(error => console.error('Error:', error));
});

