<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Announcements</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    @if($announcements->count() > 0)
                        <div class="space-y-6">
                            @foreach($announcements as $announcement)
                                <article class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                    <header class="mb-3">
                                        <h3 class="text-xl font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                        <div class="text-sm text-gray-500 mt-1">
                                            By {{ $announcement->author->name }} • {{ $announcement->created_at->format('M d, Y g:i A') }}
                                        </div>
                                    </header>
                                    <div class="text-gray-700 whitespace-pre-line">
                                        {{ $announcement->content }}
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500">No announcements available at this time.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>