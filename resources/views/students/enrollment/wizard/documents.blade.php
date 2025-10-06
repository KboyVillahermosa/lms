@extends('students.enrollment.wizard.layout')

@section('step-content')
<div class="p-6">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">Document Upload</h3>
        <p class="text-sm text-gray-600 mt-1">Please upload the required documents for verification. All documents must be clear and legible.</p>
    </div>

    <div class="space-y-6">
        @foreach($documentTypes as $documentType)
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h4 class="text-base font-medium text-gray-900 flex items-center">
                            {{ $documentType->name }}
                            @if($documentType->is_required)
                                <span class="text-red-500 ml-1">*</span>
                            @else
                                <span class="text-sm text-gray-500 ml-2">(Optional)</span>
                            @endif
                        </h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $documentType->description }}</p>
                        
                        <!-- Upload Instructions -->
                        @if($documentType->instructions)
                            <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                <p class="text-xs text-gray-700">📋 {{ $documentType->instructions }}</p>
                            </div>
                        @endif
                        
                        <!-- File Requirements -->
                        <div class="mt-2 text-xs text-gray-500">
                            <span>Accepted formats: {{ $documentType->accepted_formats_string }}</span>
                            <span class="mx-2">•</span>
                            <span>Max size: {{ $documentType->max_file_size_mb }}MB</span>
                        </div>
                    </div>
                    
                    <!-- Upload Status -->
                    @if(isset($uploadedDocuments[$documentType->id]))
                        @php $document = $uploadedDocuments[$documentType->id]; @endphp
                        <div class="ml-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $document->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                        </div>
                    @endif
                </div>

                @if(isset($uploadedDocuments[$documentType->id]))
                    @php $document = $uploadedDocuments[$documentType->id]; @endphp
                    <!-- Uploaded Document -->
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V6H8a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    <path fill-rule="evenodd" d="M8 4a2 2 0 012-2h2a2 2 0 012 2v2H8V4z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ $document->original_filename }}</p>
                                    <p class="text-xs text-green-600">{{ $document->file_size_human }} • Uploaded {{ $document->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('student.enrollment.documents.download', $document) }}" 
                                   class="text-sm text-indigo-600 hover:text-indigo-900">Download</a>
                                @if($document->status === 'rejected' || $document->needsResubmission())
                                    <span class="text-gray-300">|</span>
                                    <span class="text-sm text-orange-600">Resubmission Required</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($document->admin_notes)
                            <div class="mt-3 p-3 bg-white border border-green-200 rounded-md">
                                <p class="text-sm text-gray-700"><strong>Admin Notes:</strong> {{ $document->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Upload Form -->
                @if(!isset($uploadedDocuments[$documentType->id]) || $uploadedDocuments[$documentType->id]->needsResubmission() || $uploadedDocuments[$documentType->id]->status === 'rejected')
                    <form method="POST" action="{{ route('student.enrollment.wizard.documents') }}" enctype="multipart/form-data" class="upload-form">
                        @csrf
                        <input type="hidden" name="document_type_id" value="{{ $documentType->id }}">
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <div class="mt-4">
                                <label for="document_{{ $documentType->id }}" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        {{ isset($uploadedDocuments[$documentType->id]) ? 'Upload Replacement Document' : 'Upload Document' }}
                                    </span>
                                    <span class="mt-1 block text-xs text-gray-500">
                                        Click to browse or drag and drop
                                    </span>
                                </label>
                                <input type="file" name="document" id="document_{{ $documentType->id }}" 
                                       accept="{{ collect($documentType->accepted_formats)->map(fn($format) => '.' . $format)->join(',') }}"
                                       class="hidden file-input" 
                                       data-max-size="{{ $documentType->max_file_size }}"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Selected File Display -->
                        <div class="file-preview hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-2 text-sm text-blue-800 file-name"></span>
                                    <span class="ml-2 text-xs text-blue-600 file-size"></span>
                                </div>
                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    Upload
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Progress Summary -->
    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
        @php
            $requiredDocs = $documentTypes->where('is_required', true)->count();
            $uploadedRequired = $uploadedDocuments->whereIn('document_type_id', $documentTypes->where('is_required', true)->pluck('id'))->count();
        @endphp
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-900">Document Upload Progress</p>
                <p class="text-xs text-gray-600">{{ $uploadedRequired }} of {{ $requiredDocs }} required documents uploaded</p>
            </div>
            <div class="text-right">
                @if($uploadedRequired >= $requiredDocs)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Complete
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $requiredDocs - $uploadedRequired }} required documents remaining
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex justify-between mt-8">
        <a href="{{ route('student.enrollment.wizard.step', ['step' => 'personal-info']) }}" 
           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
            <svg class="mr-2 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Back to Personal Info
        </a>
        
        @if($uploadedRequired >= $requiredDocs)
            <a href="{{ route('student.enrollment.wizard.step', ['step' => 'course-selection']) }}" 
               class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Continue to Course Selection
                <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
        @else
            <button disabled class="px-6 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                Complete Required Documents First
            </button>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input changes
    document.querySelectorAll('.file-input').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const preview = this.closest('form').querySelector('.file-preview');
            const maxSize = parseInt(this.dataset.maxSize) * 1024; // Convert KB to bytes
            
            if (file) {
                // Validate file size
                if (file.size > maxSize) {
                    alert(`File size too large. Maximum size is ${Math.round(maxSize / 1024)}KB`);
                    this.value = '';
                    preview.classList.add('hidden');
                    return;
                }
                
                // Show preview
                preview.querySelector('.file-name').textContent = file.name;
                preview.querySelector('.file-size').textContent = formatFileSize(file.size);
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        });
    });
    
    // File size formatter
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
    
    // Drag and drop functionality
    document.querySelectorAll('.upload-form').forEach(form => {
        const dropZone = form.querySelector('.border-dashed');
        const fileInput = form.querySelector('.file-input');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight(e) {
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        }
        
        function unhighlight(e) {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        }
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        }
    });
});
</script>
@endsection