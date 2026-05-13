<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Member Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border-b border-t border-gray-200 dark:border-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Full Name</th>
                                <th scope="col" class="px-6 py-3 font-medium">Email</th>
                                <th scope="col" class="px-6 py-3 font-medium">Phone</th>
                                <th scope="col" class="px-6 py-3 font-medium">Plan</th>
                                <th scope="col" class="px-6 py-3 font-medium">Start Date</th>
                                <th scope="col" class="px-6 py-3 font-medium">Status</th>
                                <th scope="col" class="px-6 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $member->full_name }}
                                </td>
                                <td class="px-6 py-4">{{ $member->email }}</td>
                                <td class="px-6 py-4">{{ $member->phone_number }}</td>
                                <td class="px-6 py-4">{{ $member->membershipPlan->name }}</td>
                                <td class="px-6 py-4">{{ $member->membership_start_date }}</td>
                                <td class="px-6 py-4">
                                    @if($member->status)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-4 sm:space-x-6 flex">
                                    <a href="{{ route('members.edit', $member) }}" class="font-medium text-yellow-600 dark:text-yellow-400 hover:underline">Edit</a>
                                    <a>
                                        <form action=" {{ route('members.destroy', $member) }} " method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:underline" onclick="return confirm('Are you sure you want to delete this member?')">Delete</button>
                                        </form>
                                    </a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
