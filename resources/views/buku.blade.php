@extends('layouts.app')

@section('title', 'Data Buku')

@section('content')
<div class="content-header">
    <h1>Data Buku</h1>
</div>

<div class="search-container">
    <div class="search-group">
        <input type="text" id="searchInput" placeholder="Cari judul atau pengarang..." class="search-input">
        <input type="number" id="searchTahun" placeholder="Cari tahun terbit..." class="search-input">
        <button class="btn btn-primary" onclick="searchBuku()">Cari</button>
        <button class="btn btn-danger" onclick="resetSearch()">Reset</button>
        <button class="btn btn-primary" onclick="openModal()">Tambah</button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buku as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->judul }}</td>
                    <td>{{ $item->pengarang }}</td>
                    <td>{{ $item->penerbit }}</td>
                    <td>{{ $item->tahun_terbit }}</td>
                    <td>
                        <button class="btn btn-warning" onclick="editBuku({{ $item->id }})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger" onclick="hapusBuku({{ $item->id }})">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Form -->
<div id="bukuModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle" class="modal-title">Tambah Buku</h2>
        <form id="bukuForm" onsubmit="submitForm(event)">
            <input type="hidden" id="bukuId">
            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pengarang">Pengarang</label>
                <input type="text" id="pengarang" name="pengarang" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" 
                       required min="1900" max="2024">
            </div>
            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-danger" onclick="closeModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const modal = document.getElementById('bukuModal');
    const form = document.getElementById('bukuForm');
    let isEdit = false;

    function openModal() {
        modal.style.display = 'block';
        isEdit = false;
        document.getElementById('modalTitle').textContent = 'Tambah Buku';
        form.reset();
    }

    function closeModal() {
        const modalContent = document.querySelector('.modal-content');
        modalContent.style.animation = 'slideOut 0.3s ease-out';

        setTimeout(() => {
            modal.style.display = 'none';
            modalContent.style.animation = 'slideIn 0.3s ease-out';
        }, 280);
    }

    function editBuku(id) {
        isEdit = true;
        document.getElementById('modalTitle').textContent = 'Edit Buku';
        document.getElementById('bukuId').value = id;

        // Fetch data buku
        fetch(`/buku/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('judul').value = data.judul;
                document.getElementById('pengarang').value = data.pengarang;
                document.getElementById('penerbit').value = data.penerbit;
                document.getElementById('tahun_terbit').value = data.tahun_terbit;
                modal.style.display = 'block';
            });
    }

    function submitForm(event) {
        event.preventDefault();

        const formData = new FormData(form);
        const id = document.getElementById('bukuId').value;

        const url = isEdit ? `/buku/${id}` : '/buku';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('bukuForm').reset();
                    closeModal();

                    // Tampilkan Sweet Alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500,
                        position: 'top-end',
                        toast: true
                    }).then(() => {
                        // Reload halaman setelah Sweet Alert hilang
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Terjadi kesalahan',
                        showConfirmButton: false,
                        timer: 1500,
                        position: 'top-end',
                        toast: true
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat memproses data',
                    showConfirmButton: false,
                    timer: 1500,
                    position: 'top-end',
                    toast: true
                });
            });
    }

    function hapusBuku(id) {
        if (!id) return;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data buku akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/buku/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if (row) {
                                row.remove();

                                // Update nomor urut
                                const tbody = document.querySelector('tbody');
                                if (tbody) {
                                    Array.from(tbody.children).forEach((row, index) => {
                                        const firstCell = row.cells[0];
                                        if (firstCell) {
                                            firstCell.textContent = index + 1;
                                        }
                                    });
                                }
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            }).then(() => {
                                // Reload halaman setelah Sweet Alert hilang
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message || 'Terjadi kesalahan',
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat menghapus data',
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top-end',
                            toast: true
                        });
                    });
            }
        });
    }

    // Tambahkan keyframe untuk animasi keluar
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideOut {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    function searchBuku() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const searchTahun = document.getElementById('searchTahun').value;
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const judul = row.cells[1].textContent.toLowerCase();
            const pengarang = row.cells[2].textContent.toLowerCase();
            const tahun = row.cells[4].textContent;
            
            const matchText = judul.includes(searchText) || pengarang.includes(searchText);
            const matchTahun = !searchTahun || tahun === searchTahun;
            
            if (matchText && matchTahun) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update nomor urut yang ditampilkan
        updateDisplayedNumbers();
    }

    function resetSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchTahun').value = '';
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            row.style.display = '';
        });

        // Reset nomor urut
        updateDisplayedNumbers();
    }

    function updateDisplayedNumbers() {
        let visibleIndex = 1;
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                row.cells[0].textContent = visibleIndex++;
            }
        });
    }

    // Tambahkan event listener untuk pencarian real-time
    document.getElementById('searchInput').addEventListener('keyup', searchBuku);
    document.getElementById('searchTahun').addEventListener('keyup', searchBuku);
    document.getElementById('searchTahun').addEventListener('change', searchBuku);
</script>
@endsection