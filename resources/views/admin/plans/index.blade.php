<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Membership Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('error'))
        <div class="px-4 py-2 mb-2 text-red-500 text-sm">
            {{ session('error') }}
        </div>
        @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="relative overflow-x-auto w-full">
                    <div class="p-4 flex items-center justify-between space-x-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="text" class="block w-full max-w-96 ps-9 pe-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400" placeholder="Search plans">
                        </div>
                        <a href="{{ route('plans.create') }}" class="shrink-0 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg">
                            + Add Plan
                        </a>
                    </div>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border-b border-t border-gray-200 dark:border-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Plan Name</th>
                                <th scope="col" class="px-6 py-3 font-medium">Duration</th>
                                <th scope="col" class="px-6 py-3 font-medium">Price</th>
                                <th scope="col" class="px-6 py-3 font-medium w-auto">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $plan->name }}
                                </td>
                                <td class="px-6 py-4">{{ $plan->duration }} days</td>
                                <td class="px-6 py-4"> ${{ $plan->price }} </td>
                                <td class="px-6 py-4 w-px">
                                    <div class="flex items-center gap-3">

                                        <a href="{{ route('plans.show', $plan) }}"
                                            class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                            View
                                        </a>

                                        <a href="{{ route('plans.edit', $plan) }}"
                                            class="font-medium text-yellow-600 dark:text-yellow-400 hover:underline">
                                            Edit
                                        </a>

                                        <form action="{{ route('plans.destroy', $plan) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="font-medium text-red-600 dark:text-red-400 hover:underline"
                                                onclick="return confirm('Are you sure you want to delete this plan?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No plans found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
