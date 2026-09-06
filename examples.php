<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete PHP PDO CRUD Guide & Code Reference</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .guide-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .guide-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .guide-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .guide-badge {
            font-size: 13px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .badge-create { background: #dcfce7; color: #15803d; }
        .badge-read   { background: #e0f2fe; color: #0369a1; }
        .badge-update { background: #fef3c7; color: #b45309; }
        .badge-delete { background: #fee2e2; color: #b91c1c; }

        .guide-desc {
            font-size: 14.5px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .code-box {
            background: #0f172a;
            color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            font-family: Consolas, Monaco, "Courier New", monospace;
            font-size: 14px;
            line-height: 1.6;
            overflow-x: auto;
            border: 1px solid #1e293b;
        }
        .code-box .kw  { color: #f43f5e; font-weight: bold; }
        .code-box .var { color: #fbbf24; }
        .code-box .fn  { color: #c084fc; }
        .code-box .str { color: #4ade80; }
        .code-box .cmt { color: #64748b; font-style: italic; }
        
        .steps-list {
            margin: 16px 0 0 0;
            padding-left: 20px;
            color: #334155;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body style="background: #f1f5f9;">

<div class="guide-container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 style="margin: 0 0 6px 0; font-size: 28px; color: #0f172a;">📖 Full PHP PDO CRUD Code Guide</h1>
            <p style="margin: 0; color: #64748b; font-size: 15px;">Complete reference for writing database queries in PHP using PDO & SQLite</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-blue" style="text-decoration: none; padding: 10px 18px; font-weight: 600;">
                ← Back to Live Dashboard
            </a>
        </div>
    </div>

    <div class="guide-card" style="border-left: 6px solid #22c55e;">
        <div class="guide-header">
            <span class="guide-badge badge-create">1. CREATE</span>
            <h2 style="margin: 0; font-size: 22px; color: #0f172a;">INSERT — Adding New Records to the Database</h2>
        </div>
        <p class="guide-desc">
            To insert data safely without hacking vulnerabilities (SQL injection), use <strong>prepared statements</strong> with colon placeholders (like <code>:name</code>, <code>:email</code>).
        </p>

        <pre class="code-box"><code><span class="var">$sql</span> = <span class="str">"INSERT INTO users (name, email, role, status) 
        VALUES (:name, :email, :role, :status)"</span>;

<span class="var">$stmt</span> = <span class="var">$pdo</span>-&gt;<span class="fn">prepare</span>(<span class="var">$sql</span>);

<span class="var">$stmt</span>-&gt;<span class="fn">execute</span>([
    <span class="str">':name'</span>   =&gt; <span class="str">'Alice Smith'</span>,
    <span class="str">':email'</span>  =&gt; <span class="str">'alice@example.com'</span>,
    <span class="str">':role'</span>   =&gt; <span class="str">'Developer'</span>,
    <span class="str">':status'</span> =&gt; <span class="str">'active'</span>
]);

<span class="kw">echo</span> <span class="str">"User inserted successfully!"</span>;</code></pre>

        <ol class="steps-list">
            <li><strong><code>$pdo-&gt;prepare()</code></strong> compiles the query structure first.</li>
            <li><strong><code>$stmt-&gt;execute([...])</code></strong> safely maps actual values into the placeholders.</li>
        </ol>
    </div>

    <div class="guide-card" style="border-left: 6px solid #0284c7;">
        <div class="guide-header">
            <span class="guide-badge badge-read">2. READ</span>
            <h2 style="margin: 0; font-size: 22px; color: #0f172a;">SELECT — Fetching Data from the Database</h2>
        </div>
        <p class="guide-desc">
            Use <strong><code>fetchAll()</code></strong> to get all rows as a list, or <strong><code>fetch()</code></strong> to retrieve one single matching row by ID.
        </p>

        <h3 style="font-size: 16px; color: #0369a1; margin-bottom: 8px;">A. Fetch All Rows:</h3>
        <pre class="code-box"><code><span class="var">$stmt</span> = <span class="var">$pdo</span>-&gt;<span class="fn">query</span>(<span class="str">"SELECT * FROM users ORDER BY id ASC"</span>);

<span class="var">$allUsers</span> = <span class="var">$stmt</span>-&gt;<span class="fn">fetchAll</span>();

<span class="kw">foreach</span> (<span class="var">$allUsers</span> <span class="kw">as</span> <span class="var">$user</span>) {
    <span class="kw">echo</span> <span class="str">"ID: "</span> . <span class="var">$user</span>[<span class="str">'id'</span>] . <span class="str">" | Name: "</span> . <span class="var">$user</span>[<span class="str">'name'</span>] . <span class="str">" | Email: "</span> . <span class="var">$user</span>[<span class="str">'email'</span>] . <span class="str">"&lt;br&gt;"</span>;
}</code></pre>

        <h3 style="font-size: 16px; color: #0369a1; margin: 20px 0 8px 0;">B. Fetch a Single Row by ID:</h3>
        <pre class="code-box"><code><span class="var">$idToFind</span> = 1;

<span class="var">$stmt</span> = <span class="var">$pdo</span>-&gt;<span class="fn">prepare</span>(<span class="str">"SELECT * FROM users WHERE id = :id"</span>);
<span class="var">$stmt</span>-&gt;<span class="fn">execute</span>([<span class="str">':id'</span> =&gt; <span class="var">$idToFind</span>]);

<span class="var">$singleUser</span> = <span class="var">$stmt</span>-&gt;<span class="fn">fetch</span>();

<span class="kw">echo</span> <span class="str">"Found: "</span> . <span class="var">$singleUser</span>[<span class="str">'name'</span>] . <span class="str">" ("</span> . <span class="var">$singleUser</span>[<span class="str">'email'</span>] . <span class="str">")"</span>;</code></pre>
    </div>

    <div class="guide-card" style="border-left: 6px solid #f59e0b;">
        <div class="guide-header">
            <span class="guide-badge badge-update">3. UPDATE</span>
            <h2 style="margin: 0; font-size: 22px; color: #0f172a;">UPDATE — Modifying Existing Records</h2>
        </div>
        <p class="guide-desc">
            Modifies values in existing rows. <strong>Always specify <code>WHERE id = :id</code></strong> so you only update the target record.
        </p>

        <pre class="code-box"><code><span class="var">$sql</span> = <span class="str">"UPDATE users 
        SET name = :name, role = :role, status = :status, updated_at = CURRENT_TIMESTAMP 
        WHERE id = :id"</span>;

<span class="var">$stmt</span> = <span class="var">$pdo</span>-&gt;<span class="fn">prepare</span>(<span class="var">$sql</span>);
<span class="var">$stmt</span>-&gt;<span class="fn">execute</span>([
    <span class="str">':name'</span>   =&gt; <span class="str">'Alice Smith (Promoted)'</span>,
    <span class="str">':role'</span>   =&gt; <span class="str">'Principal Architect'</span>,
    <span class="str">':status'</span> =&gt; <span class="str">'active'</span>,
    <span class="str">':id'</span>     =&gt; 1
]);

<span class="kw">echo</span> <span class="str">"User #1 updated successfully!"</span>;</code></pre>
    </div>

    <div class="guide-card" style="border-left: 6px solid #ef4444;">
        <div class="guide-header">
            <span class="guide-badge badge-delete">4. DELETE</span>
            <h2 style="margin: 0; font-size: 22px; color: #0f172a;">DELETE — Removing Records from the Database</h2>
        </div>
        <p class="guide-desc">
            Permanently deletes records from the database. <strong>Always specify <code>WHERE id = :id</code></strong>.
        </p>

        <pre class="code-box"><code><span class="var">$sql</span> = <span class="str">"DELETE FROM users WHERE id = :id"</span>;

<span class="var">$stmt</span> = <span class="var">$pdo</span>-&gt;<span class="fn">prepare</span>(<span class="var">$sql</span>);
<span class="var">$stmt</span>-&gt;<span class="fn">execute</span>([
    <span class="str">':id'</span> =&gt; 5
]);

<span class="kw">echo</span> <span class="str">"User #5 deleted successfully!"</span>;</code></pre>
    </div>

    <div style="text-align: center; margin-top: 30px; margin-bottom: 40px;">
        <a href="index.php" class="btn btn-blue" style="text-decoration: none; padding: 12px 28px; font-size: 15px; font-weight: 600;">
            ← Back to Live Dashboard (index.php)
        </a>
    </div>

</div>

</body>
</html>
