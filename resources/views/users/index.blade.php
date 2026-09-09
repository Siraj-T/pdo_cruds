@extends('layouts.app')

@section('title', 'Users Management - Laravel Eloquent CRUD')

@section('content')

    @if (!empty($flashMessage))
        <div class="alert alert-success">
            <span>{{ $flashMessage }}</span>
        </div>
    @endif

    @if (!empty($flashError))
        <div class="alert alert-error">
            <strong>⚠ Notice:</strong> {{ $flashError }}
        </div>
    @endif

    {{-- CREATE OR EDIT USER CARD --}}
    <div class="card">
        <h2 class="card-title">
            {{ $editUser ? '✎ Edit User #' . $editUser->id . ' (Eloquent UPDATE)' : '+ Add New User (Eloquent CREATE)' }}
        </h2>
        <p class="card-subtitle">
            {{ $editUser ? 'Update user details using RESTful PUT request and Eloquent $user->update()' : 'Insert a new user record into SQLite using Eloquent User::create()' }}
        </p>

        <form method="POST" action="{{ $editUser ? route('users.update', $editUser) : route('users.store') }}">
            @csrf
            @if ($editUser)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        required 
                        value="{{ old('name', $editUser->name ?? '') }}" 
                        placeholder="e.g. Alice Smith"
                    >
                    @error('name')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        required 
                        value="{{ old('email', $editUser->email ?? '') }}" 
                        placeholder="e.g. alice@example.com"
                    >
                    @error('email')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">Role / Position</label>
                    <input 
                        type="text" 
                        id="role" 
                        name="role" 
                        class="form-input" 
                        required 
                        value="{{ old('role', $editUser->role ?? 'Developer') }}" 
                        placeholder="e.g. Backend Developer"
                    >
                    @error('role')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-input">
                        @php $currentStatus = old('status', $editUser->status ?? 'active'); @endphp
                        <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>active</option>
                        <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>pending</option>
                        <option value="inactive" {{ $currentStatus === 'inactive' ? 'selected' : '' }}>inactive</option>
                    </select>
                    @error('status')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn {{ $editUser ? 'btn-primary' : 'btn-success' }}" style="height: 42px; width: 100%;">
                        {{ $editUser ? 'Save Update' : 'Add User' }}
                    </button>
                    @if ($editUser)
                        <a href="{{ route('users.index') }}" class="btn btn-outline" style="height: 42px;">
                            Cancel
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- INTERACTIVE ELOQUENT CODE RUNNER --}}
    <div class="code-box card" style="background: #0f172a; border-color: #1e293b; color: #ffffff;">
        <div class="code-box-header">
            <div>
                <h3 style="font-size: 17px; font-weight: 700; color: #f8fafc; display: flex; align-items: center; gap: 8px;">
                    ⚡ Live Laravel Eloquent Code Runner
                </h3>
                <p style="font-size: 13px; color: #94a3b8; margin-top: 2px;">
                    Execute live <strong>Eloquent ORM</strong> expressions in real-time. Results will update the database and live table below!
                </p>
            </div>
            <span style="background: #312e81; color: #a5b4fc; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px;">
                Eloquent ORM
            </span>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; align-items: center;">
            <span style="font-size: 12.5px; color: #94a3b8; font-weight: 600;">Quick Eloquent Snippets:</span>
            <button type="button" class="btn-template" onclick="fillSnippet('all')">User::all()</button>
            <button type="button" class="btn-template" onclick="fillSnippet('active')">Where Active</button>
            <button type="button" class="btn-template" onclick="fillSnippet('create')">+ Eloquent Create</button>
            <button type="button" class="btn-template" onclick="fillSnippet('update')">✎ Eloquent Update</button>
            <button type="button" class="btn-template" onclick="fillSnippet('count')">User::count()</button>
        </div>

        <form method="POST" action="{{ route('users.run') }}">
            @csrf
            <textarea 
                id="code_input" 
                name="code" 
                class="code-textarea" 
                placeholder="Type or paste any Eloquent expression here... Example:
User::where('status', 'active')->orderBy('name', 'asc')->get();"
                required
            >{{ $executedCode ?? "User::where('status', 'active')->orderBy('id', 'asc')->get();" }}</textarea>

            <div style="margin-top: 14px; display: flex; gap: 10px; align-items: center;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px;">
                    ▶ Run Eloquent Code
                </button>
                <button type="button" class="btn btn-outline" style="color: #cbd5e1; border-color: #334155;" onclick="document.getElementById('code_input').value=''">
                    Clear Box
                </button>
                @if (isset($customResults))
                    <a href="{{ route('users.index') }}" class="btn btn-outline" style="color: #38bdf8; border-color: #0284c7; text-decoration: none;">
                        ⟲ Reset to Full Table
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- USERS DATABASE TABLE --}}
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 700; color: var(--slate-900);">
                    Live Database Table: <code>users</code>
                    <span style="font-size: 13px; font-weight: 500; color: var(--slate-500); margin-left: 6px;">
                        ({{ count($users) }} record(s) loaded)
                    </span>
                </h3>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">
                🔄 Refresh Table
            </a>
        </div>

        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th style="width: 140px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>#{{ $user->id ?? ($user['id'] ?? '') }}</strong></td>
                            <td>{{ $user->name ?? ($user['name'] ?? '') }}</td>
                            <td>{{ $user->email ?? ($user['email'] ?? '') }}</td>
                            <td>
                                <span class="badge badge-role">
                                    {{ $user->role ?? ($user['role'] ?? 'Developer') }}
                                </span>
                            </td>
                            <td>
                                @php $st = $user->status ?? ($user['status'] ?? 'active'); @endphp
                                <span class="badge badge-{{ $st }}">
                                    {{ $st }}
                                </span>
                            </td>
                            <td style="color: var(--slate-500); font-size: 12.5px;">
                                {{ isset($user->created_at) ? $user->created_at->format('Y-m-d H:i') : ($user['created_at'] ?? '') }}
                            </td>
                            <td>
                                <div class="actions-cell">
                                    @if (isset($user->id))
                                        <a href="{{ route('users.edit', $user) }}" class="btn-sm btn-edit">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete user {{ addslashes($user->name) }}?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-sm btn-delete">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" align="center" style="padding: 36px; color: var(--slate-500);">
                                No users found. Use the form above or the Eloquent Code Runner to create records!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
<script>
function fillSnippet(type) {
    const textarea = document.getElementById('code_input');
    const randomNum = Math.floor(Math.random() * 9000) + 1000;

    if (type === 'all') {
        textarea.value = `User::orderBy('id', 'asc')->get();`;
    } else if (type === 'active') {
        textarea.value = `User::where('status', 'active')->orderBy('name', 'asc')->get();`;
    } else if (type === 'create') {
        textarea.value = `User::create([
    'name'   => 'Taylor Otwell ' + randomNum,
    'email'  => 'taylor' + randomNum + '@laravel.test',
    'role'   => 'Lead Architect',
    'status' => 'active'
]);`;
    } else if (type === 'update') {
        textarea.value = `User::where('id', 1)->update([
    'role'   => 'Principal System Architect',
    'status' => 'active'
]);`;
    } else if (type === 'count') {
        textarea.value = `User::count();`;
    }

    textarea.focus();
}
</script>
@endsection
