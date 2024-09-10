<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Country;
use App\Models\Product;
use App\Models\Category;
use App\Models\AdminHomePageSection;
use Illuminate\View\View;
use DB;
use Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['adminRoles', 'adminStoreRole']]);
        $this->middleware('permission:role-create', ['only' => ['adminCreateRole', 'adminStoreRole']]);
        $this->middleware('permission:role-edit', ['only' => ['adminEditRole', 'adminUpdateRole']]);
        $this->middleware('permission:role-delete', ['only' => ['adminDestroyRole']]);

        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['adminProducts', 'adminStoreProduct']]);
        $this->middleware('permission:product-create', ['only' => ['adminCreateProduct', 'adminStoreProduct']]);
        $this->middleware('permission:product-edit', ['only' => ['adminEditProduct', 'adminUpdateproduct']]);
        $this->middleware('permission:product-delete', ['only' => ['adminDestroyProduct']]);
    }

    public function index()
    {
        $usersCount = User::count();
        $productsCount = Product::count();
        $cateogriesCount = Category::count();
        $sectionsCount = AdminHomePageSection::count();

        $usersLast = User::latest()->first();
        $productsLast = Product::latest()->first();
        $cateogriesLast = Category::latest()->first();
        $sectionsLast = AdminHomePageSection::latest()->first();

        return view('admin.index', compact(
            'usersCount',
            'productsCount',
            'cateogriesCount',
            'sectionsCount',
            'usersLast',
            'productsLast',
            'cateogriesLast',
            'sectionsLast'
        ));
    }

    // Start Admin User
    public function adminUsers(Request $request): View
    {
        $users = User::all();
        return view('admin.users.users', compact('users'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function adminCreateUser(): View
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('admin.users.create', compact('roles'));
    }

    public function adminStoreUser(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'picture' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $path = $file->store('public/users');
            $filename = basename($path);
        } else {
            $filename = null;
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['picture'] = $filename;

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('admin-users')
            ->with('success', 'User created successfully');
    }

    public function adminEditUser($id): View
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function adminUpdateUser(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
            'picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $user = User::find($id);

        if ($request->hasFile('picture')) {
            // Delete the old picture if it exists
            if ($user->picture) {
                Storage::delete('public/users/' . $user->picture);
            }

            // Store the new picture
            $file = $request->file('picture');
            $path = $file->store('public/users');
            $filename = basename($path);
            $input['picture'] = $filename; // Update picture field in input array
        }

        $user->update($input);

        // Remove existing roles and assign new ones
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('admin-users')
            ->with('success', 'User updated successfully');
    }

    public function adminDestroyUser($id): RedirectResponse
    {
        User::find($id)->delete();
        return redirect()->route('admin-users')
            ->with('success', 'User deleted successfully');
    }
    // Start Admin User

    // Start Admin Role
    public function adminRoles(Request $request): View
    {
        $roles = Role::all();
        return view('admin.roles.roles', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function adminCreateRole(): View
    {
        $permission = Permission::get();
        return view('admin.roles.create', compact('permission'));
    }

    public function adminStoreRole(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $permissionsID = array_map(
            function ($value) {
                return (int)$value;
            },
            $request->input('permission')
        );

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($permissionsID);

        return redirect()->route('admin-roles')
            ->with('success', 'Role created successfully');
    }

    public function adminEditRole($id): View
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('admin.roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    public function adminUpdateRole(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $permissionsID = array_map(
            function ($value) {
                return (int)$value;
            },
            $request->input('permission')
        );

        $role->syncPermissions($permissionsID);

        return redirect()->route('admin-roles')
            ->with('success', 'Role updated successfully');
    }

    public function adminDestroyRole($id): RedirectResponse
    {
        DB::table("roles")->where('id', $id)->delete();
        return redirect()->route('admin-roles')
            ->with('success', 'Role deleted successfully');
    }
    // End Admin Role

    // Start Admin Category
    public function adminCategories(Request $request): View
    {
        $categories = Category::all();
        return view('admin.categories.categories', compact('categories'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function adminCreateCategory(): View
    {
        return view('admin.categories.create');
    }

    public function adminStoreCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required'
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin-categories')
            ->with('success', 'Category created successfully.');
    }

    public function adminEditCategory($id): View
    {
        $category = Category::find($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function adminUpdateCategory(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $input = $request->all();

        $category = Category::find($id);

        $category->update($input);

        return redirect()->route('admin-categories')
            ->with('success', 'Category updated successfully');
    }

    public function adminDestroyCategory($id): RedirectResponse
    {
        Category::find($id)->delete();
        return redirect()->route('admin-categories')
            ->with('success', 'Category deleted successfully');
    }
    // End Admin Category

    // Start Admin Product
    public function adminProducts(Request $request): View
    {
        $products = Product::all();
        return view('admin.products.products', compact('products'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function adminCreateProduct(): View
    {
        return view('admin.products.create');
    }

    public function adminStoreProduct(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'picture' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $path = $file->store('public/products');
            $filename = basename($path);
        } else {
            $filename = null;
        }

        Product::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'price' => $request->price,
            'picture' => $filename,
        ]);

        return redirect()->route('admin-products')
            ->with('success', 'Product created successfully.');
    }

    public function adminEditProduct($id): View
    {
        $product = Product::find($id);
        return view('admin.products.edit', compact('product'));
    }

    public function adminUpdateproduct(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'picture' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $input = $request->all();

        $product = Product::find($id);

        if ($request->hasFile('picture')) {
            // Delete the old picture if it exists
            if ($product->picture) {
                Storage::delete('public/products/' . $product->picture);
            }

            // Store the new picture
            $file = $request->file('picture');
            $path = $file->store('public/products');
            $filename = basename($path);
            $input['picture'] = $filename;
        }

        $product->update($input);

        return redirect()->route('admin-products')
            ->with('success', 'Product updated successfully');
    }

    public function adminDestroyProduct($id): RedirectResponse
    {
        Product::find($id)->delete();
        return redirect()->route('admin-products')
            ->with('success', 'Product deleted successfully');
    }
    // End Admin Product

    // Start Admin Product
    public function adminHomepageSections(Request $request): View
    {
        $homepagesections = AdminHomePageSection::all();
        return view('admin.homepage.sections', compact('homepagesections'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function adminUpdateHomepageSections(Request $request)
    {
        $sections = $request->input('is_enabled', []);

        foreach ($sections as $sectionId => $isEnabled) {
            $section = AdminHomePageSection::find($sectionId);
            if ($section) {
                $section->is_enabled = $isEnabled;
                $section->save();
            }
        }

        return redirect()->back()->with('success', 'Homepage sections updated successfully.');
    }
    // End Admin Product

    // Start Admin Profile
    public function adminProfile(Request $request): View
    {
        $profileUser = Auth::user();
        $profileProducts = $profileUser->products;
        return view('admin.profile.profile', compact('profileUser', 'profileProducts'));
    }

    public function adminEditProfileUser($id): View
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('admin.profile.edit', compact('user', 'roles', 'userRole'));
    }

    public function adminProfileUpdateUser(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $input = $request->all();

        $user = User::find($id);

        if ($request->hasFile('picture')) {
            // Delete the old picture if it exists
            if ($user->picture) {
                Storage::delete('public/users/' . $user->picture);
            }

            // Store the new picture
            $file = $request->file('picture');
            $path = $file->store('public/users');
            $filename = basename($path);
            $input['picture'] = $filename; // Update picture field in input array
        }

        $user->update($input);

        return redirect()->route('admin-profile')
            ->with('success', 'User updated successfully');
    }
    // End Admin Profile

    // Start Admin Notification
    public function adminNotifications(): View
    {
        return view('admin.notification.notifications');
    }
    // End Admin Notification

    // Start Admin Countries
    public function adminCountries(Request $request): View
    {
        $countries = Country::select('id', 'code', 'active', 'name->en as name')
            ->orderBy('name', 'asc')
            ->paginate(50);

        return view('admin.international.countries', compact('countries'))
            ->with('i', ($request->input('page', 1) - 1) * 50);
    }
    // End Admin Countries
}
