<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Complete Your Enrollment</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Step 1: Personal Info -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $enrollmentRequest->isStepCompleted('personal_info') ? 'bg-green-500 text-white' : (request()->route('step') == 'personal-info' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                                @if($enrollmentRequest->isStepCompleted('personal_info'))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    1
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Personal Info</span>
                        </div>

                        <!-- Connector -->
                        <div class="w-8 h-0.5 {{ $enrollmentRequest->isStepCompleted('personal_info') ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                        <!-- Step 2: Documents -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $enrollmentRequest->hasAllRequiredDocuments() ? 'bg-green-500 text-white' : (request()->route('step') == 'documents' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                                @if($enrollmentRequest->hasAllRequiredDocuments())
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    2
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Documents</span>
                        </div>

                        <!-- Connector -->
                        <div class="w-8 h-0.5 {{ $enrollmentRequest->hasAllRequiredDocuments() ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                        <!-- Step 3: Course Selection -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $enrollmentRequest->isStepCompleted('course_selection') ? 'bg-green-500 text-white' : (request()->route('step') == 'course-selection' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                                @if($enrollmentRequest->isStepCompleted('course_selection'))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    3
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Course Selection</span>
                        </div>

                        <!-- Connector -->
                        <div class="w-8 h-0.5 {{ $enrollmentRequest->isStepCompleted('course_selection') ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                        <!-- Step 4: Review -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $enrollmentRequest->isStepCompleted('review') ? 'bg-green-500 text-white' : (request()->route('step') == 'review' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                                @if($enrollmentRequest->isStepCompleted('review'))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    4
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Review & Submit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $enrollmentRequest->completion_percentage }}%"></div>
                </div>
                <div class="text-sm text-gray-600 mt-2">{{ $enrollmentRequest->completion_percentage }}% Complete</div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Step Content -->
            <div class="bg-white shadow rounded-lg">
                @yield('step-content')
            </div>
        </div>
    </div>
</x-app-layout>