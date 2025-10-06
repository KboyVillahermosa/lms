<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Enrollment Application Status</h2>
            <a href="{{ route('student.enrollment.wizard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                New Application
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if($enrollmentRequests->count() > 0)
                <div class="space-y-6">
                    @foreach($enrollmentRequests as $request)
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <!-- Application Header -->
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $request->course ? $request->course->title : 'Course Selection Pending' }}
                                        </h3>
                                        <p class="text-sm text-gray-600">Application submitted {{ $request->created_at->format('F j, Y \a\t g:i A') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <!-- Overall Status -->
                                        @if($request->status === 'pending')
                                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                Under Review
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                                Approved - Enrolled
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">
                                                Application Rejected
                                            </span>
                                        @endif
                                        
                                        <!-- Progress Percentage -->
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ $request->completion_percentage }}% Complete</div>
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $request->completion_percentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <!-- Progress Timeline -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-900 mb-4">Application Progress</h4>
                                    <div class="flex items-center space-x-4">
                                        <!-- Personal Info -->
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $request->isStepCompleted('personal_info') ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                                @if($request->isStepCompleted('personal_info'))
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    1
                                                @endif
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">Personal Info</span>
                                        </div>
                                        
                                        <div class="w-8 h-0.5 {{ $request->isStepCompleted('personal_info') ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                        
                                        <!-- Documents -->
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $request->hasAllRequiredDocuments() ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                                @if($request->hasAllRequiredDocuments())
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    2
                                                @endif
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">Documents</span>
                                        </div>
                                        
                                        <div class="w-8 h-0.5 {{ $request->hasAllRequiredDocuments() ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                        
                                        <!-- Course Selection -->
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $request->isStepCompleted('course_selection') ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                                @if($request->isStepCompleted('course_selection'))
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    3
                                                @endif
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">Course Selection</span>
                                        </div>
                                        
                                        <div class="w-8 h-0.5 {{ $request->isStepCompleted('course_selection') ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                        
                                        <!-- Review -->
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $request->isStepCompleted('review') ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                                @if($request->isStepCompleted('review'))
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    4
                                                @endif
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">Submitted</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Status -->
                                @if($request->documents->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="text-sm font-medium text-gray-900 mb-3">Document Verification Status</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($request->documents as $document)
                                                <div class="border border-gray-200 rounded-md p-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $document->documentType->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $document->original_filename }}</div>
                                                        </div>
                                                        <span class="px-2 py-1 text-xs rounded-full {{ $document->status_badge_class }}">
                                                            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                        </span>
                                                    </div>
                                                    @if($document->admin_notes)
                                                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                                                            <strong>Admin Notes:</strong> {{ $document->admin_notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Admin Notes -->
                                @if($request->admin_notes)
                                    <div class="mb-6">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Administrative Notes</h4>
                                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-800">
                                            {{ $request->admin_notes }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-3">
                                    @if($request->status === 'pending' && $request->completion_percentage < 100)
                                        <a href="{{ route('student.enrollment.wizard') }}" 
                                           class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                            Continue Application
                                        </a>
                                    @elseif($request->status === 'pending')
                                        <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-md text-sm">
                                            Awaiting Review
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-md text-sm">
                                            Enrollment Complete
                                        </span>
                                    @else
                                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-md text-sm">
                                            Application Closed
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Applications State -->
                <div class="bg-white shadow rounded-lg">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No enrollment applications</h3>
                        <p class="mt-2 text-sm text-gray-500">You haven't submitted any enrollment applications yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('student.enrollment.wizard') }}" 
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                <svg class="mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                Start Your Application
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>