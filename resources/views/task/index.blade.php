@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER & FILTERS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h4 class="fw-bold mb-0">Tasks</h4>
                
                <div class="d-flex gap-2">
                    <input type="text" id="searchTerm" class="form-control" style="width: 180px;" placeholder="Search title..." onkeyup="searchTask()">
                    
                    <select id="filterStatus" class="form-select" style="width: 130px;" onchange="loadTasks(1)">
                        <option value="">Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>

                    <input type="date" id="filterDate" class="form-control" style="width: 150px;" onchange="loadTasks(1)">

                    <button class="btn btn-light border" onclick="resetFilters()">
                        <i class="fa fa-rotate-right"></i>
                    </button>
                    
                    <button class="btn btn-primary px-4" onclick="openModal()">
                        <i class="fa fa-plus me-1"></i> Add Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Description</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="taskList"></tbody>
            </table>
        </div>
    </div>

    <div id="paginationLinks" class="mt-4 d-flex justify-content-center"></div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form id="taskForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="task_id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" id="title" name="title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Due Date</label>
                            <input type="date" id="due_date" name="due_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<style>
    .error { color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: block; }
    input.error, textarea.error, select.error { border: 1px solid #dc3545; }
</style>

<script>
let currentPage = 1;
let searchTimer;
$(document).ready(function () {
    loadTasks();
    $("#taskForm").validate({
        rules: {
            title: { required: true, minlength: 3 },
            status: { required: true },
            due_date: { required: true }
        },
        messages: {
            title: "Please enter a title (min 3 chars)",
            due_date: "Please select a date"
        },
        submitHandler: function(form) {
            executeSave();
        }
    });
});

function loadTasks(page = 1) {
    currentPage = page;
    let search = $("#searchTerm").val();
    let status = $("#filterStatus").val();
    let due_date = $("#filterDate").val();

    $.ajax({
        url: "/fetch-task-list",
        type: "GET",
        data: { page, search, status, due_date },
        success: function (res) {
            let html = "";
            if (!res.data || res.data.length === 0) {
                html = `<tr><td colspan="5" class="text-center py-5 text-muted">No Tasks Found</td></tr>`;
            } else {
                res.data.forEach(task => {
                    let badge = (task.status === 'completed') ? 'bg-success' : 'bg-warning text-dark';
                    let taskJson = JSON.stringify(task).replace(/"/g, '&quot;');

                    html += `
                    <tr>
                        <td class="ps-4 fw-semibold">${task.title}</td>
                        <td><span class="badge ${badge}">${task.status}</span></td>
                        <td><small class="text-muted">${task.due_date ?? '-'}</small></td>
                        <td>${task.description ? task.description.substring(0, 30) + '...' : '-'}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary border-0 me-1" onclick='editTask(${taskJson})'>
                                <i class="fa fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteTask(${task.id})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }
            $("#taskList").html(html);
            renderPagination(res);
        }
    });
}

function renderPagination(res) {
    let html = "";
    if (res.links && res.links.length > 3) {
        html += '<ul class="pagination pagination-sm mb-0">';
        res.links.forEach(link => {
            let pageNum = link.url ? new URL(link.url, window.location.origin).searchParams.get('page') : null;
            html += `
                <li class="page-item ${link.active ? 'active' : ''} ${link.url ? '' : 'disabled'}">
                    <a class="page-link" href="javascript:void(0)" onclick="${pageNum ? `loadTasks(${pageNum})` : ''}">
                        ${link.label.replace('&laquo; Previous', '‹').replace('Next &raquo;', '›')}
                    </a>
                </li>`;
        });
        html += '</ul>';
    }
    $("#paginationLinks").html(html);
}

function openModal() {
    $("#modalTitle").text('Add New Task');
    $("#taskForm")[0].reset();
    $("#task_id").val('');
    $(".error").remove(); 
    $("#taskModal").modal('show');
}

function editTask(task) {
    $("#modalTitle").text('Edit Task');
    $(".error").remove();
    $("#task_id").val(task.id);
    $("#title").val(task.title);
    $("#description").val(task.description);
    $("#status").val(task.status);
    $("#due_date").val(task.due_date);
    $("#taskModal").modal('show');
}

// 2. Add / Update Logic with SweetAlert
function executeSave() {
    let id = $("#task_id").val();
    let url = id ? "/tasks/update" : "/tasks/store"; 
    
    $.ajax({
        url: url,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            title: $("#title").val(),
            description: $("#description").val(),
            status: $("#status").val(),
            due_date: $("#due_date").val()
        },
        success: function(res) {
            $("#taskModal").modal('hide');
            loadTasks(id ? currentPage : 1);
            
            Swal.fire({
                icon: 'success',
                title: id ? 'Updated!' : 'Added!',
                text: 'Task saved successfully',
                timer: 1500,
                showConfirmButton: false
            });
        },
        error: function(err) {
            Swal.fire('Oops!', 'Something went wrong.', 'error');
        }
    });
}

// 3. Delete Logic with SweetAlert
function deleteTask(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This task will be permanently removed!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/tasks/delete",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", id: id },
                success: function() {
                    let rowCount = $('#taskList tr').length;
                    if (rowCount <= 1 && currentPage > 1) {
                        currentPage--; 
                    }
                    loadTasks(currentPage);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Task has been deleted.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}

function searchTask() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadTasks(1), 400);
}

function resetFilters() {
    $("#searchTerm, #filterDate").val('');
    $("#filterStatus").val('');
    loadTasks(1);
}
</script>
@endpush