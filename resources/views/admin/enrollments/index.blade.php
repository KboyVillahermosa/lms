<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Enrollment Requests</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    @if($enrollmentRequests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($enrollmentRequests as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->course->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->course->description }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($request->status === 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($request->status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($request->document_status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved ({{ $request->documents->count() }})
                                                    </span>
                                                @elseif($request->document_status === 'rejected')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected ({{ $request->documents->count() }})
                                                    </span>
                                                @elseif($request->documents->count() > 0)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending ({{ $request->documents->count() }})
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        None (0)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $request->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <!-- View Details Button -->
                                                    <a href="{{ route('admin.enrollments.show', $request) }}" class="text-blue-600 hover:text-blue-900">
                                                        View Details
                                                    </a>
                                                    @if($request->status === 'pending')
                                                        <!-- Approve Button -->
                                                        <button onclick="approveRequest({{ $request->id }})" class="text-green-600 hover:text-green-900">
                                                            Approve
                                                        </button>
                                                        <!-- Reject Button -->
                                                        <button onclick="rejectRequest({{ $request->id }})" class="text-red-600 hover:text-red-900">
                                                            Reject
                                                        </button>
                                                    @else
                                                        <span class="text-gray-500">
                                                            Processed by {{ optional($request->admin)->name ?? 'Unknown' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500">No enrollment requests found.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Enrollment Request</h3>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" id="approve_notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any additional notes..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Enrollment Request</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                        <textarea name="admin_notes" id="reject_notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Explain why this request is being rejected..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function approveRequest(requestId) {
            document.getElementById('approveForm').action = `{{ route('admin.enrollments.index') }}/${requestId}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function rejectRequest(requestId) {
            document.getElementById('rejectForm').action = `{{ route('admin.enrollments.index') }}/${requestId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            if (event.target === approveModal) {
                approveModal.classList.add('hidden');
            }
            if (event.target === rejectModal) {
                rejectModal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>