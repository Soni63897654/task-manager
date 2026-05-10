@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="d-md-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Media Library</h4>
                    <p class="text-muted small">Manage and view your documents and images</p>
                </div>
                <div class="d-flex flex-column align-items-md-end gap-2 mt-3 mt-md-0">
                    <div class="d-flex gap-2">
                        <input type="file" id="multiFiles" multiple accept=".jpg,.jpeg,.png,.pdf" class="d-none">
                        <button class="btn btn-outline-dark rounded-pill px-4" onclick="$('#multiFiles').click()">
                            <i class="bi bi-plus-lg me-1"></i> Select Files
                        </button>
                        <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="uploadFiles()">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Upload Now
                        </button>
                    </div>
                    <div id="selectedFilesList" class="text-muted small mt-1"></div>
                </div>
            </div>

            <div id="uploadStatusHint" class="alert alert-light border-0 shadow-sm text-center py-2 mb-4 d-none">
                <span class="spinner-border spinner-border-sm text-primary me-2"></span> Processing files...
            </div>

            <div class="row g-4" id="filePreviewArea">
                @forelse($files as $file)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-6 file-wrapper">
                        <div class="file-card">
                            @php 
                                $isPdf = str_ends_with(strtolower($file->original_name), '.pdf');
                            @endphp
                            
                            <div class="file-preview shadow-sm">
                                @if($isPdf)
                                    <div class="pdf-icon-wrapper bg-danger-subtle text-danger">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </div>
                                @else
                                    <img src="{{ asset('storage/' . $file->file_path) }}" loading="lazy">
                                @endif
                                
                                <div class="file-actions">
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-light btn-sm rounded-circle shadow">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5" id="noFilesMsg">
                        <div class="mb-3 text-muted" style="font-size: 3rem;"><i class="bi bi-folder2-open"></i></div>
                        <p class="text-muted fw-medium">No files uploaded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; }
    .file-preview { position: relative; aspect-ratio: 1/1; background: #fff; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid #eee; }
    .file-preview img { width: 100%; height: 100%; object-fit: cover; }
    .pdf-icon-wrapper { font-size: 3rem; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .file-actions { position: absolute; inset: 0; background: rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.2s; backdrop-filter: blur(2px); }
    .file-preview:hover .file-actions { opacity: 1; }
    .file-name { font-size: 0.85rem; font-weight: 500; color: #333; }
    .file-meta { font-size: 0.65rem; color: #888; }
</style>
@endsection

@push('scripts')
<script>
// Selection preview logic
$('#multiFiles').on('change', function() {
    let files = this.files;
    let listArea = $('#selectedFilesList');
    listArea.html(''); 

    if (files.length > 0) {
        let fileNames = Array.from(files).map(f => `<span class="badge bg-secondary-subtle text-dark border me-1">${f.name}</span>`).join('');
        listArea.html(`<strong>Ready to upload:</strong> ${fileNames}`);
    }
});

function uploadFiles() {
    let fileInput = $('#multiFiles')[0];
    let files = fileInput.files;

    if (files.length === 0) {
        Swal.fire({ icon: 'info', title: 'Wait!', text: 'Please select files first.' });
        return;
    }

    let formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('_token', "{{ csrf_token() }}");

    $('#uploadStatusHint').removeClass('d-none');

    $.ajax({
        url: "{{ route('files.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            $('#uploadStatusHint').addClass('d-none');
            $('#selectedFilesList').html(''); // Clear the preview list
            $('#noFilesMsg').remove(); 
            
            res.files.forEach(file => {
                let isPdf = file.name.toLowerCase().endsWith('.pdf');
                let fileExt = file.type.split('/')[1].toUpperCase();
                
                let html = `
                <div class="col-xl-2 col-lg-3 col-md-4 col-6 file-wrapper">
                    <div class="file-card">
                        <div class="file-preview shadow-sm">
                            ${isPdf ? `<div class="pdf-icon-wrapper bg-danger-subtle text-danger"><i class="bi bi-file-earmark-pdf"></i></div>` : `<img src="${file.url}">`}
                            <div class="file-actions">
                                <a href="${file.url}" target="_blank" class="btn btn-light btn-sm rounded-circle shadow"><i class="bi bi-eye-fill"></i></a>
                            </div>
                        </div>
                        <div class="file-info mt-2">
                            <span class="file-name text-truncate d-block">${file.name}</span>
                            <span class="file-meta text-uppercase">${fileExt}</span>
                        </div>
                    </div>
                </div>`;
                $('#filePreviewArea').prepend(html);
            });

            $('#multiFiles').val(''); 
            Swal.fire({ icon: 'success', title: 'Done!', text: 'Files uploaded successfully.' });
        },
        error: function (xhr) {
            $('#uploadStatusHint').addClass('d-none');
            
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorHtml = '<ul class="text-start">';
                $.each(errors, function(key, value) {
                    errorHtml += `<li>${value[0]}</li>`;
                });
                errorHtml += '</ul>';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Rejected',
                    html: errorHtml
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Oops!', text: 'Internal Server Error.' });
            }
        }
    });
}
</script>
@endpush