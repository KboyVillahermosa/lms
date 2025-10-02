@extends('students.enrollment.wizard.layout')

@section('step-content')
<div class="p-6">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
        <p class="text-sm text-gray-600 mt-1">Please provide your personal details for the enrollment application.</p>
    </div>

    <form method="POST" action="{{ route('student.enrollment.wizard.personal-info') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Phone Number <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" id="phone" 
                       value="{{ old('phone', $enrollmentRequest->phone) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="+1 (555) 123-4567" required>
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                    Date of Birth <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date_of_birth" id="date_of_birth" 
                       value="{{ old('date_of_birth', $enrollmentRequest->date_of_birth ? $enrollmentRequest->date_of_birth->format('Y-m-d') : '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       max="{{ date('Y-m-d', strtotime('-16 years')) }}" required>
                @error('date_of_birth')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Gender -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Gender <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <div class="flex items-center">
                    <input type="radio" name="gender" id="gender_male" value="male" 
                           {{ old('gender', $enrollmentRequest->gender) === 'male' ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <label for="gender_male" class="ml-2 text-sm text-gray-700">Male</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" name="gender" id="gender_female" value="female" 
                           {{ old('gender', $enrollmentRequest->gender) === 'female' ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <label for="gender_female" class="ml-2 text-sm text-gray-700">Female</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" name="gender" id="gender_other" value="other" 
                           {{ old('gender', $enrollmentRequest->gender) === 'other' ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <label for="gender_other" class="ml-2 text-sm text-gray-700">Other</label>
                </div>
            </div>
            @error('gender')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div class="mt-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                Current Address <span class="text-red-500">*</span>
            </label>
            <textarea name="address" id="address" rows="3" 
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Enter your complete current address including street, city, state, and postal code..."
                      required>{{ old('address', $enrollmentRequest->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Important Note -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Important Information</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>The information you provide must match your official documents. You'll need to upload supporting documents in the next step, including:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Government-issued photo ID</li>
                            <li>Birth certificate</li>
                            <li>Academic transcripts</li>
                            <li>Proof of current address</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mt-8">
            <div></div> <!-- Empty div for spacing -->
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Continue to Documents
                <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection