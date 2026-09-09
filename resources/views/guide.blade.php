@extends('layouts.app')

@section('title', 'Vanilla PDO vs. The Laravel Way - Architecture Guide')

@section('content')

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 20px;">
        <div>
            <h2 class="card-title">📖 Vanilla PHP (PDO) vs. The Laravel Way</h2>
            <p class="card-subtitle" style="margin-bottom: 0;">
                Comprehensive side-by-side guide demonstrating why frameworks like Laravel are standard in modern development.
            </p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-primary" style="text-decoration: none;">
            ← Back to Users Dashboard
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 28px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 16px;">
            <h4 style="color: #0f172a; margin-bottom: 6px;">1. Architecture: MVC</h4>
            <p style="font-size: 13px; color: #64748b;">
                In Vanilla PHP, HTML, SQL queries, and request handling were all mixed in <code>index.php</code>. Laravel separates concerns into <strong>Model</strong> (data), <strong>View</strong> (presentation), and <strong>Controller</strong> (logic).
            </p>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 16px;">
            <h4 style="color: #0f172a; margin-bottom: 6px;">2. Eloquent ORM</h4>
            <p style="font-size: 13px; color: #64748b;">
                Instead of writing raw SQL strings that are prone to syntax mistakes, Eloquent maps database rows to PHP objects with expressive syntax like <code>User::create()</code> and <code>$user->delete()</code>.
            </p>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 16px;">
            <h4 style="color: #0f172a; margin-bottom: 6px;">3. Security & Validation</h4>
            <p style="font-size: 13px; color: #64748b;">
                Laravel provides automatic <strong>CSRF tokens</strong> (<code>@csrf</code>), mass-assignment protection via <code>$fillable</code>, and robust form validation rules.
            </p>
        </div>
    </div>

    {{-- COMPARISON 1: READ --}}
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">
            🔍 1. Fetching All Users (READ)
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #f87171; font-weight: bold; margin-bottom: 8px;">// Vanilla PHP & PDO</div>
                <pre style="margin: 0; color: #cbd5e1;">$stmt = $pdo->prepare("SELECT * FROM users ORDER BY id ASC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);</pre>
            </div>
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">// The Laravel Way (Eloquent)</div>
                <pre style="margin: 0; color: #38bdf8;">$users = User::orderBy('id', 'asc')->get();</pre>
            </div>
        </div>
    </div>

    {{-- COMPARISON 2: CREATE --}}
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">
            ➕ 2. Inserting a New Record (CREATE)
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #f87171; font-weight: bold; margin-bottom: 8px;">// Vanilla PHP & PDO</div>
                <pre style="margin: 0; color: #cbd5e1;">$sql = "INSERT INTO users (name, email, role, status)
        VALUES (:name, :email, :role, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'   => $_POST['name'],
    ':email'  => $_POST['email'],
    ':role'   => $_POST['role'],
    ':status' => $_POST['status']
]);</pre>
            </div>
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">// The Laravel Way (Eloquent & Validation)</div>
                <pre style="margin: 0; color: #38bdf8;">$validated = $request->validate([
    'name'   => 'required|string|max:255',
    'email'  => 'required|email|unique:users',
    'role'   => 'required|string',
    'status' => 'required|in:active,pending,inactive',
]);

User::create($validated);</pre>
            </div>
        </div>
    </div>

    {{-- COMPARISON 3: UPDATE --}}
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">
            ✎ 3. Updating a Record (UPDATE)
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #f87171; font-weight: bold; margin-bottom: 8px;">// Vanilla PHP & PDO</div>
                <pre style="margin: 0; color: #cbd5e1;">$sql = "UPDATE users SET name = :name, email = :email, 
        role = :role, status = :status, 
        updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'   => $_POST['name'],
    ':email'  => $_POST['email'],
    ':role'   => $_POST['role'],
    ':status' => $_POST['status'],
    ':id'     => $_POST['id']
]);</pre>
            </div>
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">// The Laravel Way (Route Model Binding)</div>
                <pre style="margin: 0; color: #38bdf8;">// Route Model Binding automatically injects $user
public function update(Request $request, User $user)
{
    $validated = $request->validate([...]);
    $user->update($validated);
    return redirect()->route('users.index');
}</pre>
            </div>
        </div>
    </div>

    {{-- COMPARISON 4: DELETE --}}
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">
            ✕ 4. Deleting a Record (DELETE)
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #f87171; font-weight: bold; margin-bottom: 8px;">// Vanilla PHP & PDO</div>
                <pre style="margin: 0; color: #cbd5e1;">$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute([':id' => $_GET['delete']]);
header("Location: index.php?msg=deleted");</pre>
            </div>
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">// The Laravel Way (RESTful DELETE + CSRF)</div>
                <pre style="margin: 0; color: #38bdf8;">public function destroy(User $user)
{
    $user->delete();
    return redirect()->route('users.index');
}</pre>
            </div>
        </div>
    </div>

    {{-- COMPARISON 5: TEMPLATING --}}
    <div>
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">
            🎨 5. HTML Templating (Views)
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #f87171; font-weight: bold; margin-bottom: 8px;">// Raw PHP in HTML</div>
                <pre style="margin: 0; color: #cbd5e1;">&lt;?php foreach ($users as $user): ?&gt;
    &lt;tr&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($user['name']) ?&gt;&lt;/td&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($user['email']) ?&gt;&lt;/td&gt;
    &lt;/tr&gt;
&lt;?php endforeach; ?&gt;</pre>
            </div>
            <div style="background: #0f172a; color: #f8fafc; padding: 16px; border-radius: var(--radius-md); font-family: 'Fira Code', monospace; font-size: 13px; overflow-x: auto;">
                <div style="color: #34d399; font-weight: bold; margin-bottom: 8px;">// Laravel Blade Template</div>
@verbatim
                <pre style="margin: 0; color: #38bdf8;">@forelse ($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
    </tr>
@empty
    <tr><td colspan="2">No users found</td></tr>
@endforelse</pre>
@endverbatim
            </div>
        </div>
    </div>
</div>

@endsection
