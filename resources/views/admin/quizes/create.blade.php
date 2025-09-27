<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Quiz</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.quizes.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input name="title" required class="mt-1 block w-full border-gray-300 rounded-md" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="quiz-type" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="mcq">Multiple choice</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Assign to students (optional)</label>
                        <select name="assigned_users[]" multiple class="mt-1 block w-full border-gray-300 rounded-md">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">Questions <span id="question-count" class="text-sm text-gray-500">(0)</span></label>
                                <div class="space-x-2">
                                    <button type="button" id="add-question-top" class="px-2 py-1 bg-green-600 text-white rounded">Add Question</button>
                                </div>
                            </div>

                            <div id="questions" class="mt-4 space-y-4">
                                <!-- question templates will be added here -->
                            </div>

                            <!-- duplicate add button near submit for convenience -->
                            <div class="mt-2">
                                <button type="button" id="add-question-bottom" class="px-3 py-1 bg-green-600 text-white rounded">Add Question</button>
                            </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Create Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function(){
            let qIndex = 0;
            const addBtnTop = document.getElementById('add-question-top');
            const addBtnBottom = document.getElementById('add-question-bottom');
            const questionsDiv = document.getElementById('questions');
            const quizTypeSelect = document.getElementById('quiz-type');
            const questionCountEl = document.getElementById('question-count');

            function createQuestionNode(){
                const wrapper = document.createElement('div');
                wrapper.className = 'p-4 border rounded';
                wrapper.dataset.index = qIndex;

                const currentType = quizTypeSelect.value || 'mcq';
                const html = [];
                html.push('<label class="block font-medium">Question</label>');
                html.push(`<textarea name="questions[${qIndex}][question]" required class="mt-1 block w-full border-gray-300 rounded-md"></textarea>`);

                // hidden type input
                html.push(`<input type="hidden" name="questions[${qIndex}][type]" value="${currentType}">`);

                // MCQ block
                html.push('<div class="mcq-block"' + (currentType === 'mcq' ? '' : ' style="display:none"') + '>');
                html.push('<div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">');
                html.push(`<input name="questions[${qIndex}][option_a]" placeholder="Option A" class="border p-2 rounded mcq-option" ${currentType==='mcq'?'required':''} />`);
                html.push(`<input name="questions[${qIndex}][option_b]" placeholder="Option B" class="border p-2 rounded mcq-option" ${currentType==='mcq'?'required':''} />`);
                html.push(`<input name="questions[${qIndex}][option_c]" placeholder="Option C" class="border p-2 rounded mcq-option" ${currentType==='mcq'?'required':''} />`);
                html.push(`<input name="questions[${qIndex}][option_d]" placeholder="Option D" class="border p-2 rounded mcq-option" ${currentType==='mcq'?'required':''} />`);
                html.push('</div>');

                html.push('<div class="mt-2">');
                html.push('<label class="block text-sm font-medium">Correct option</label>');
                html.push('<div class="flex items-center space-x-2 mt-1">');
                ['a','b','c','d'].forEach(opt => {
                    html.push(`<label class="inline-flex items-center"><input type="radio" name="questions[${qIndex}][correct_option]" value="${opt}" class="mcq-correct" ${currentType==='mcq'?'required':''}><span class="ml-2">${opt.toUpperCase()}</span></label>`);
                });
                html.push('</div>');
                html.push('</div>');
                html.push('</div>');

                // Essay block (rubric)
                html.push('<div class="essay-block"' + (currentType === 'essay' ? '' : ' style="display:none"') + '>');
                html.push('<label class="block text-sm font-medium mt-2">Rubric / Notes for grading (optional)</label>');
                html.push(`<textarea name="questions[${qIndex}][rubric]" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>`);
                html.push('</div>');

                html.push('<div class="mt-2 text-right">');
                html.push('<button type="button" class="remove-question px-2 py-1 bg-red-600 text-white rounded">Remove</button>');
                html.push('</div>');

                wrapper.innerHTML = html.join('');

                wrapper.querySelector('.remove-question').addEventListener('click', function(){
                    wrapper.remove();
                    // update count after removal
                    if (typeof updateQuestionCount === 'function') {
                        updateQuestionCount();
                    }
                });

                qIndex++;
                return wrapper;
            }

            // wire both add buttons
            [addBtnTop, addBtnBottom].forEach(function(b){
                b.addEventListener('click', function(){
                    const node = createQuestionNode();
                    questionsDiv.appendChild(node);
                    updateQuestionCount();
                    // scroll into view
                    node.scrollIntoView({behavior: 'smooth', block: 'center'});
                });
            });

            // auto-add first question on load
            document.addEventListener('DOMContentLoaded', function(){
                if (questionsDiv.children.length === 0) {
                    const first = createQuestionNode();
                    questionsDiv.appendChild(first);
                    updateQuestionCount();
                }
            });

            // update count helper
            function updateQuestionCount(){
                const count = questionsDiv.querySelectorAll('[data-index]').length;
                if (questionCountEl) questionCountEl.textContent = `(${count})`; 
            }

            // update count when removing
            questionsDiv.addEventListener('click', function(e){
                if (e.target && e.target.classList.contains('remove-question')) {
                    // removal handled in individual listener; just update count after a tick
                    setTimeout(updateQuestionCount, 50);
                }
            });

            // keep question inputs in sync with quiz type (mcq/essay)
            quizTypeSelect.addEventListener('change', function(){
                const type = this.value;
                document.querySelectorAll('#questions [name$="[type]"]').forEach(function(inp){
                    inp.value = type;
                });
                document.querySelectorAll('#questions .mcq-block').forEach(function(el){
                    el.style.display = (type === 'mcq') ? '' : 'none';
                    // toggle required on inputs inside
                    el.querySelectorAll('.mcq-option').forEach(function(i){ i.required = (type === 'mcq'); });
                    el.querySelectorAll('.mcq-correct').forEach(function(i){ i.required = (type === 'mcq'); });
                });
                document.querySelectorAll('#questions .essay-block').forEach(function(el){
                    el.style.display = (type === 'essay') ? '' : 'none';
                });
            });
        })();
    </script>
</x-app-layout>
