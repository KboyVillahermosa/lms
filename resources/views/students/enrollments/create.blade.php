<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Submit Enrollment Request</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('student.enrollments.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select name="course_id" id="course_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select a course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                        @if($courses->count() === 0)
                            <p class="mt-2 text-sm text-gray-500">No courses available for enrollment at this time.</p>
                        @endif
                    </div>

                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Enrollment (Optional)</label>
                        <textarea name="reason" id="reason" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Explain why you want to enroll in this course...">{{ old('reason') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('student.enrollments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700" {{ $courses->count() === 0 ? 'disabled' : '' }}>
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>