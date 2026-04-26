@extends('layouts.app')

@section('title', 'Data Peminjam')

@section('content')
<div class="content-header">
    <h1>Data Peminjam</h1>
</div>

<div class="search-container">
    <div class="search-group">
        <input type="text" id="searchNIS" placeholder="Cari berdasarkan NIS..." class="search-input">
        <button class="btn btn-danger" onclick="resetSearch()">Reset</button>
        <button class="btn btn-primary" onclick="openModal()">Tambah</button>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjam as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->nis }}</td>
                <td>{{ $item->kelas }}</td>
                <td>
                    <button class="btn btn-warning" onclick="editPeminjam({{ $item->id }})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger" onclick="hapusPeminjam({{ $item->id }})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Form -->
<div id="peminjamModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle" class="modal-title">Tambah Peminjam</h2>
        <form id="peminjamForm" onsubmit="submitForm(event)">
            <input type="hidden" id="peminjamId">
            <div class="form-group">
                <label for="nama">Nama Peminjam</label>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nis">NIS</label>
                <input type="text" id="nis" name="nis" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="kelas">Kelas</label>
                <input type="text" id="kelas" name="kelas" class="form-control" required>
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
    const modal = document.getElementById('peminjamModal');
    const form = document.getElementById('peminjamForm');
    let isEdit = false;

    function openModal() {
        isEdit = false;
        document.getElementById('modalTitle').textContent = 'Tambah Peminjam';
        form.reset();
        document.getElementById('peminjamId').value = '';
        modal.style.display = 'block';
    }

    function closeModal() {
        const modalContent = document.querySelector('.modal-content');
        modalContent.style.animation = 'slideOut 0.3s ease-out';
        
        setTimeout(() => {
            modal.style.display = 'none';
            modalContent.style.animation = '';
            document.getElementById('peminjamForm').reset();
            document.getElementById('peminjamId').value = '';
        }, 280);
    }

    function editPeminjam(id) {
        isEdit = true;
        document.getElementById('modalTitle').textContent = 'Edit Peminjam';
        document.getElementById('peminjamId').value = id;
        
        fetch(`/peminjam/${id}/edit`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('nama').value = data.nama;
            document.getElementById('nis').value = data.nis;
            document.getElementById('kelas').value = data.kelas;
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat mengambil data peminjam',
                showConfirmButton: false,
                timer: 1500,
                position: 'top-end',
                toast: true
            });
        });
    }

    function submitForm(event) {
        event.preventDefault();
        
        const formData = new FormData(form);
        const id = document.getElementById('peminjamId').value;
        
        const url = isEdit ? `/peminjam/${id}` : '/peminjam';
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
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500,
                    position: 'top-end',
                    toast: true
                }).then(() => {
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message,
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
                text: 'Terjadi kesalahan saat menyimpan data',
                showConfirmButton: false,
                timer: 1500,
                position: 'top-end',
                toast: true
            });
        });
    }

    function hapusPeminjam(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data peminjam akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/peminjam/${id}`, {
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top-end',
                            toast: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message,
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

    // Fungsi pencarian
    function searchPeminjam() {
        const searchNIS = document.getElementById('searchNIS').value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const nis = row.cells[2].textContent.toLowerCase(); // Kolom NIS
            
            if (nis.includes(searchNIS)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updateDisplayedNumbers();
    }

    function resetSearch() {
        document.getElementById('searchNIS').value = '';
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            row.style.display = '';
        });

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

    // Event listener untuk pencarian real-time
    document.getElementById('searchNIS').addEventListener('keyup', searchPeminjam);
</script>
@endsection

@section('additional_css')
<style>
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

    @keyframes slideIn {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
@endsection