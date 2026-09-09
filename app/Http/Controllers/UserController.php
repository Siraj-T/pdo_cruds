<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $users = User::orderBy('id', 'asc')->get();
        $editUser = null;

        if ($request->has('edit')) {
            $editUser = User::find($request->query('edit'));
        }

        return view('users.index', compact('users', 'editUser'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email',
            'role'   => 'required|string|max:100',
            'status' => 'required|in:active,pending,inactive',
        ]);

        User::create($validated);

        return redirect()->route('users.index')->with('success', '✓ User created successfully!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $users = User::orderBy('id', 'asc')->get();
        $editUser = $user;

        return view('users.index', compact('users', 'editUser'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'   => 'required|string|max:100',
            'status' => 'required|in:active,pending,inactive',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', '✓ User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', '✓ User deleted successfully!');
    }

    /**
     * Run a live Eloquent ORM snippet.
     */
    public function runSnippet(Request $request): View|RedirectResponse
    {
        $snippet = trim($request->input('code', ''));

        if (empty($snippet)) {
            return redirect()->route('users.index')->with('error', 'Please enter some Eloquent code before clicking Run.');
        }

        $users = User::orderBy('id', 'asc')->get();
        $customResults = null;
        $message = null;
        $errorMessage = null;

        try {
            // Strip any wrapping php tags
            $cleanCode = preg_replace('/^\s*<\?(php)?/i', '', $snippet);
            $cleanCode = preg_replace('/\?>\s*$/', '', $cleanCode);

            // Execute snippet in isolated context with App\Models\User imported
            ob_start();
            $result = eval("use App\Models\User; return (" . rtrim($cleanCode, ';') . ");");
            $output = ob_get_clean();

            if ($result instanceof \Illuminate\Support\Collection || is_array($result)) {
                $customResults = $result;
                $count = count($result);
                $message = "✓ Eloquent executed successfully! Returned {$count} record(s).";
            } elseif ($result instanceof User) {
                $customResults = collect([$result]);
                $message = "✓ Eloquent executed successfully! Single User record returned.";
            } elseif (is_numeric($result) || is_string($result) || is_bool($result)) {
                $printed = is_bool($result) ? ($result ? 'true' : 'false') : (string)$result;
                $message = "✓ Eloquent executed successfully! Result: {$printed}";
            } else {
                $message = "✓ Eloquent code executed successfully! Database updated below.";
                if (!empty($output)) {
                    $message .= " Output: " . strip_tags($output);
                }
            }

            // Refresh users if not custom result
            if ($customResults === null) {
                $users = User::orderBy('id', 'asc')->get();
            } else {
                $users = $customResults;
            }

        } catch (Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            $errorMessage = "Eloquent Execution Notice: " . $e->getMessage();
        }

        return view('users.index', [
            'users'          => $users,
            'editUser'       => null,
            'executedCode'   => $snippet,
            'customResults'  => $customResults,
            'flashMessage'   => $message,
            'flashError'     => $errorMessage,
        ]);
    }

    /**
     * Show the Eloquent vs. Vanilla PDO comparison guide.
     */
    public function guide(): View
    {
        return view('guide');
    }
}
