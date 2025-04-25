<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Durian Management') }}
            </h2>
        </div>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/durian.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-durian.css') }}">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Add Bootstrap CSS for modals -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="durian-container">
            <div class="durian-header">
                <h2 class="durian-title"></h2>
                <a href="{{ route('admin.durian.create') }}" class="add-button">
                    <i class="fas fa-plus add-button-icon"></i> Add New Durian
                </a>
            </div>

            <!-- Durian List -->
            <div class="durian-grid">
                @forelse ($durians as $durian)
                    <div class="durian-card">
                        <div class="durian-card-header">
                            <h3 class="durian-card-title">{{ $durian->name }}</h3>
                        </div>
                        <div class="durian-card-content">
                            <div class="durian-card-stat">
                                <span>Total Harvested:</span>
                                <span class="font-bold">{{ $durian->total }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons with Icons -->
                        <div class="durian-card-actions">
                            <a href="{{ route('admin.durian.show', $durian->id) }}" class="btn btn-success" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.durian.edit', $durian->id) }}" class="btn btn-primary edit" 
                               data-durian-id="{{ $durian->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger delete-durian-btn" 
                                    data-durian-id="{{ $durian->id }}" 
                                    data-durian-name="{{ $durian->name }}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-{{ $durian->id }}" method="POST" action="{{ route('durian.destroy', $durian->id) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state col-span-full">
                        <p>No durian varieties found. Add your first durian variety!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bootstrap JS for modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/durian.js') }}"></script>
    <script>
        // Initialize flash messages if they exist
        document.addEventListener('DOMContentLoaded', function() {
            // Check for flash messages from server-side redirects
            @if(session('success'))
                showSuccessModal("{{ session('success') }}");
            @endif
            
            @if(session('error'))
                showErrorModal("{{ session('error') }}");
            @endif
            
            @if(session('warning'))
                showWarningModal("{{ session('warning') }}");
            @endif
            
            @if(session('info'))
                showInfoModal("{{ session('info') }}");
            @endif
            
            // Add event listeners for delete buttons
            document.querySelectorAll('.delete-durian-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const durianId = this.getAttribute('data-durian-id');
                    const durianName = this.getAttribute('data-durian-name');
                    
                    showConfirmModal(
                        `Are you sure you want to delete "${durianName}"?`, 
                        function() {
                            document.getElementById(`delete-form-${durianId}`).submit();
                        },
                        null,
                        'Delete Confirmation'
                    );
                });
            });
            
            // Add event listeners for edit buttons
            document.querySelectorAll('.edit-durian-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const durianId = this.getAttribute('data-durian-id');
                    showInfoModal(
                        'You are about to edit this durian. Continue to the edit page?',
                        'Edit Durian',
                        function() {
                            window.location.href = "{{ route('admin.durian.edit', '') }}/" + durianId;
                        }
                    );
                });
            });
            
            // Add event listener for add button
            document.getElementById('addDurianBtn').addEventListener('click', function(e) {
                e.preventDefault();
                showInfoModal(
                    'You are about to add a new durian. Continue to the creation page?',
                    'Add New Durian',
                    function() {
                        window.location.href = "{{ route('admin.durian.create') }}";
                    }
                );
            });
        });
    </script>
</x-app-layout>
