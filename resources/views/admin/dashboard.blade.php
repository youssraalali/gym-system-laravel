<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                {{ __("You're logged in!") }}
            </div>
            <div class="mt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                        <strong>{{ __('Recent Plan Requests:') }} </strong>
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-4">
                            <div class="flex items-center gap-4">

                                <select name="status"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                   focus:ring-blue-500 focus:border-blue-500 block p-2.5
                   dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                                    <option value="">All Statuses</option>

                                    <option value="pending"
                                        {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="approved"
                                        {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        Approved
                                    </option>

                                    <option value="rejected"
                                        {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        Rejected
                                    </option>
                                </select>

                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Filter
                                </button>

                            </div>
                        </form>
                        </div>

                        <table class="mt-4 w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border-b border-t border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-bold">{{ __('Member Name') }}</th>
                                    <th scope="col" class="px-6 py-3 font-bold">{{ __('Requested Plan') }}</th>
                                    <th scope="col" class="px-6 py-3 font-bold">{{ __('Requested At') }}</th>
                                    <th scope="col" class="px-6 py-3 font-bold">{{ __('Status') }}</th>
                                    <th scope="col" class="px-6 py-3 font-bold">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planRequests as $planRequest)
                                <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $planRequest->member->full_name }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $planRequest->membershipPlan->name }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $planRequest->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <form action="{{ route('plan-requests.update', $planRequest->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="pending" {{ $planRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $planRequest->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $planRequest->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                Update
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
