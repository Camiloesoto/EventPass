<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Log de acceso al listado de usuarios
        Log::info('Admin accedió al listado de usuarios', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email,
            'filters' => $request->all()
        ]);

        $query = User::withCount(['orders', 'tickets', 'waitlistEntries']);

        // Filtros
        if ($request->filled('is_admin')) {
            $query->where('is_admin', $request->is_admin);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest('created_at')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Log::info('Admin accedió al formulario de creación de usuario', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email
        ]);

        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->has('is_admin'),
                'email_verified_at' => now()
            ]);

            Log::info('Admin creó un nuevo usuario', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'is_admin' => $user->is_admin
            ]);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear usuario desde admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'error' => $e->getMessage(),
                'user_data' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Error al crear el usuario. Inténtalo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Log::info('Admin visualizó usuario', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        $user->load(['orders.items.ticketType.event', 'tickets.orderItem.ticketType.event', 'waitlistEntries.event']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Log::info('Admin accedió al formulario de edición de usuario', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $oldData = $user->toArray();
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_admin' => $request->has('is_admin')
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            Log::info('Admin actualizó usuario', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'old_data' => $oldData,
                'new_data' => $updateData
            ]);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar usuario desde admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'user_data' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario. Inténtalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // No permitir que un admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            Log::warning('Admin intentó eliminarse a sí mismo', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'user_id' => $user->id
            ]);

            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        try {
            DB::beginTransaction();

            $userData = $user->toArray();
            $user->delete();

            Log::info('Admin eliminó usuario', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'user_id' => $user->id,
                'user_name' => $userData['name'],
                'user_email' => $userData['email'],
                'user_data' => $userData
            ]);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar usuario desde admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al eliminar el usuario. Inténtalo de nuevo.');
        }
    }
}