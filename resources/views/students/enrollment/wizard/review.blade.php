@extends('students.enrollment.wizard.layout')

@section('step-content')
<div class="p-6">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">Review & Submit Application</h3>
        <p class="text-sm text-gray-600 mt-1">Please review all the information below before submitting your enrollment application.</p>
    </div>

    <!-- Personal Information Review -->
    <div class="mb-8">
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Personal Information
                </h4>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Full Name:</span>
                        <span class="text-gray-900">{{ auth()->user()->name }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Email:</span>
                        <span class="text-gray-900">{{ auth()->user()->email }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Phone:</span>
                        <span class="text-gray-900">{{ $enrollmentRequest->phone }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Date of Birth:</span>
                        <span class="text-gray-900">{{ $enrollmentRequest->date_of_birth ? $enrollmentRequest->date_of_birth->format('F j, Y') : 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Gender:</span>
                        <span class="text-gray-900">{{ ucfirst($enrollmentRequest->gender ?? 'Not specified') }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="font-medium text-gray-700">Address:</span>
                        <span class="text-gray-900">{{ $enrollmentRequest->address }}</span>
                    </div>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('student.enrollment.wizard.step', ['step' => 'personal-info']) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Review -->
    <div class="mb-8">
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Uploaded Documents
                </h4>
            </div>
            <div class="p-4">
                @if($enrollmentRequest->documents->count() > 0)
                    <div class="space-y-3">
                        @foreach($enrollmentRequest->documents as $document)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $document->documentType->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $document->original_filename }} • {{ $document->file_size_human }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $document->status_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                    </span>
                                    <a href="{{ route('student.enrollment.documents.download', $document) }}" 
                                       class="text-sm text-indigo-600 hover:text-indigo-900">Download</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No documents uploaded.</p>
                @endif
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('student.enrollment.wizard.step', ['step' => 'documents']) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Selection Review -->
    <div class="mb-8">
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Course Selection
                </h4>
            </div>
            <div class="p-4">
                @if($enrollmentRequest->course)
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-900">{{ $enrollmentRequest->course->title }}</div>
                        @if($enrollmentRequest->course->description)
                            <div class="text-sm text-gray-600 mt-1">{{ $enrollmentRequest->course->description }}</div>
                        @endif
                        @if($enrollmentRequest->course->instructor)
                            <div class="text-xs text-gray-500 mt-1">Instructor: {{ $enrollmentRequest->course->instructor->name }}</div>
                        @endif
                    </div>
                    
                    @if($enrollmentRequest->reason)
                        <div class="mt-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Reason for Enrollment:</div>
                            <div class="text-sm text-gray-900 p-3 bg-gray-50 rounded-md">{{ $enrollmentRequest->reason }}</div>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-500">No course selected.</p>
                @endif
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('student.enrollment.wizard.step', ['step' => 'course-selection']) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Summary -->
    <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Before You Submit</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Please ensure that all information provided is accurate and complete. Once submitted:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Your application will be reviewed by our admissions team</li>
                        <li>We will verify your uploaded documents for authenticity</li>
                        <li>You will receive email notifications about your application status</li>
                        <li>The review process typically takes 3-5 business days</li>
                        <li>You may be contacted if additional information is required</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="mb-6">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input type="checkbox" id="terms_agreement" name="terms_agreement" 
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
            </div>
            <div class="ml-3">
                <label for="terms_agreement" class="text-sm text-gray-700">
                    I confirm that all information provided is accurate and complete. I understand that providing false information may result in the rejection of my application or dismissal from the program. I agree to the 
                    <a href="#" class="text-indigo-600 hover:text-indigo-900">terms and conditions</a> 
                    and 
                    <a href="#" class="text-indigo-600 hover:text-indigo-900">privacy policy</a>.
                    <span class="text-red-500">*</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Submit Form -->
    <form method="POST" action="{{ route('student.enrollment.wizard.submit') }}" id="submitForm">
        @csrf
        
        <!-- Navigation -->
        <div class="flex justify-between">
            <a href="{{ route('student.enrollment.wizard.step', ['step' => 'course-selection']) }}" 
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <svg class="mr-2 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Back to Course Selection
            </a>
            
            <button type="submit" id="submitButton" disabled
                    class="px-6 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed transition-all duration-200">
                <svg class="mr-2 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Submit Application
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const termsCheckbox = document.getElementById('terms_agreement');
    const submitButton = document.getElementById('submitButton');
    const submitForm = document.getElementById('submitForm');
    
    // Enable/disable submit button based on terms agreement
    termsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            submitButton.disabled = false;
            submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-indigo-500', 'focus:ring-offset-2');
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-indigo-500', 'focus:ring-offset-2');
        }
    });
    
    // Confirmation dialog before submission
    submitForm.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to submit your enrollment application? This action cannot be undone.')) {
            e.preventDefault();
        } else {
            // Disable button to prevent double submission
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin mr-2 -ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            `;
        }
    });
});
</script>
@endsection