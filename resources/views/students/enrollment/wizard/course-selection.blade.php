@extends('students.enrollment.wizard.layout')

@section('step-content')
<div class="p-6">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">Course Selection</h3>
        <p class="text-sm text-gray-600 mt-1">Choose the course you would like to enroll in and provide your reason for enrollment.</p>
    </div>

    <form method="POST" action="{{ route('student.enrollment.wizard.course-selection') }}">
        @csrf
        
        <!-- Course Selection -->
        <div class="mb-6">
            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                Select Course <span class="text-red-500">*</span>
            </label>
            
            @if($courses->count() > 0)
                <div class="grid gap-4">
                    @foreach($courses as $course)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors">
                            <label for="course_{{ $course->id }}" class="cursor-pointer block">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="radio" name="course_id" id="course_{{ $course->id }}" 
                                               value="{{ $course->id }}" 
                                               {{ old('course_id', $enrollmentRequest->course_id) == $course->id ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" required>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium text-gray-900">{{ $course->title }}</div>
                                        @if($course->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ $course->description }}</div>
                                        @endif
                                        
                                        <!-- Course Details -->
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                            @if($course->instructor)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Instructor: {{ $course->instructor->name }}
                                                </div>
                                            @endif
                                            
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Created: {{ $course->created_at->format('M Y') }}
                                            </div>
                                            
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 mr-1 bg-green-400 rounded-full"></span>
                                                    Active
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="border border-gray-200 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no active courses available for enrollment at this time.</p>
                </div>
            @endif
            
            @error('course_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Reason for Enrollment -->
        <div class="mb-6">
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                Reason for Enrollment <span class="text-red-500">*</span>
            </label>
            <textarea name="reason" id="reason" rows="4" 
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Please explain why you want to enroll in this course, your learning objectives, and how it fits your educational goals..."
                      required>{{ old('reason', $enrollmentRequest->reason) }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Provide a detailed explanation of your motivation and goals for taking this course.</p>
            @error('reason')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Prerequisites Notice -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Important Note</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>By selecting a course and providing your reason for enrollment, you are indicating your serious intent to participate. Please ensure that:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>You meet any prerequisites for the selected course</li>
                            <li>You have the time commitment required for successful completion</li>
                            <li>Your enrollment reason clearly demonstrates your motivation and goals</li>
                            <li>You understand the course requirements and expectations</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between">
            <a href="{{ route('student.enrollment.wizard.step', ['step' => 'documents']) }}" 
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <svg class="mr-2 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Back to Documents
            </a>
            
            @if($courses->count() > 0)
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Continue to Review
                    <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @else
                <button disabled class="px-6 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                    No Courses Available
                </button>
            @endif
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand reason textarea as user types
    const reasonTextarea = document.getElementById('reason');
    if (reasonTextarea) {
        reasonTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    }
    
    // Highlight selected course card
    const radioButtons = document.querySelectorAll('input[name="course_id"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove highlight from all cards
            document.querySelectorAll('.border-gray-200').forEach(card => {
                card.classList.remove('border-indigo-500', 'bg-indigo-50');
                card.classList.add('border-gray-200');
            });
            
            // Highlight selected card
            if (this.checked) {
                const card = this.closest('.border');
                card.classList.remove('border-gray-200');
                card.classList.add('border-indigo-500', 'bg-indigo-50');
            }
        });
        
        // Set initial state
        if (radio.checked) {
            const card = radio.closest('.border');
            card.classList.remove('border-gray-200');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
        }
    });
});
</script>
@endsection