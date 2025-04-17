<form method="POST" action="{{ route('users.destroy', $user) }}" 
    onsubmit="return confirm('Are you sure you want to delete this user?');"
    class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-500 hover:text-red-700">
        Delete
    </button>
</form>