
<x-app-layout>
	<x-slot name="header">Grading Queue</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white shadow sm:rounded-lg p-6">
				@if($submissions->isEmpty())
					<p>No ungraded submissions.</p>
				@else
					<ul class="space-y-4">
						@foreach($submissions as $s)
							<li class="p-4 border rounded">
								<div class="flex justify-between">
									<div>
										<div class="font-medium">{{ $s->student->name }} — {{ optional($s->assignment)->title }}</div>
										<div class="text-sm text-gray-600">Submitted: {{ optional($s->submitted_at)->toFormattedDateString() }}</div>
										@if($s->attachment)
											<div><a href="{{ asset('storage/' . $s->attachment) }}" target="_blank" class="text-indigo-600">Download</a></div>
										@endif
										<div class="mt-2">Answers: <div class="text-sm text-gray-700">{!! nl2br(e($s->answers)) !!}</div></div>
									</div>
									<div>
										<form action="{{ route('admin.assignments.submissions.grade', [$s->assignment, $s]) }}" method="POST" class="space-y-2">
											@csrf
											<div>
												<label class="block text-sm">Grade (max {{ optional($s->assignment)->total_points }})</label>
												<input type="number" name="grade" max="{{ optional($s->assignment)->total_points }}" class="border rounded px-2 py-1" />
											</div>
											<div>
												<label class="block text-sm">Feedback</label>
												<textarea name="feedback" class="border rounded w-56"></textarea>
											</div>
											<div>
												<button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Save Grade</button>
											</div>
										</form>
									</div>
								</div>
							</li>
						@endforeach
					</ul>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>
