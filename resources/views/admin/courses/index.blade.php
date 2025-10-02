<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Courses</h2>
            <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Create Course
            </button>
        </div>
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
                    @if($courses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($courses as $course)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $course->description }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $course->code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ optional($course->instructor)->name ?? 'Not assigned' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $course->capacity ?? 'Unlimited' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($course->is_active)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button onclick="openEditModal({{ json_encode($course) }})" class="text-indigo-600 hover:text-indigo-900">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">No courses yet.</div>
                            <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Create Your First Course
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Course Modal -->
    <div id="courseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border max-w-2xl w-full shadow-xl rounded-lg bg-white">
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Create Course</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <hr class="mt-4">
            </div>

            <!-- Error display -->
            <div id="modalErrors" class="hidden mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                <ul id="errorList" class="list-disc pl-5"></ul>
            </div>

            <form id="courseForm" method="POST">
                @csrf
                <div id="methodField"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Course Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <input type="text" name="department" id="department" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Computer Science">
                    </div>

                    <div>
                        <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                        <select name="instructor_id" id="instructor_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select an instructor</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                        <input type="number" name="capacity" id="capacity" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" placeholder="Leave empty for unlimited">
                    </div>

                    <div class="flex items-center">
                        <div class="mt-6">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active (visible to students for enrollment)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-2 mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Course description and details..."></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                        Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Create Course';
            document.getElementById('courseForm').action = '{{ route("admin.courses.store") }}';
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('submitBtn').textContent = 'Create Course';
            
            // Clear form and hide errors
            document.getElementById('courseForm').reset();
            document.getElementById('is_active').checked = true;
            hideErrors();
            
            document.getElementById('courseModal').classList.remove('hidden');
        }

        function openEditModal(course) {
            document.getElementById('modalTitle').textContent = 'Edit Course';
            document.getElementById('courseForm').action = `/admin/courses/${course.id}`;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('submitBtn').textContent = 'Update Course';
            
            // Fill form with course data
            document.getElementById('title').value = course.title || '';
            document.getElementById('code').value = course.code || '';
            document.getElementById('department').value = course.department || '';
            document.getElementById('description').value = course.description || '';
            document.getElementById('instructor_id').value = course.instructor_id || '';
            document.getElementById('capacity').value = course.capacity || '';
            document.getElementById('is_active').checked = course.is_active;
            
            hideErrors();
            document.getElementById('courseModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('courseModal').classList.add('hidden');
            hideErrors();
        }

        function hideErrors() {
            document.getElementById('modalErrors').classList.add('hidden');
            document.getElementById('errorList').innerHTML = '';
        }

        function showErrors(errors) {
            const errorList = document.getElementById('errorList');
            errorList.innerHTML = '';
            
            Object.values(errors).forEach(errorArray => {
                errorArray.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });
            });
            
            document.getElementById('modalErrors').classList.remove('hidden');
        }

        // Handle form submission with AJAX
        document.getElementById('courseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            hideErrors();

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (response.ok) {
                    // Success - reload the page to show updated data
                    window.location.reload();
                } else {
                    return response.json().then(data => {
                        throw data;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.errors) {
                    showErrors(error.errors);
                } else {
                    showErrors({ general: ['An error occurred. Please try again.'] });
                }
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('courseModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-app-layout>
