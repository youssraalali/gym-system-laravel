<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($member->membership_plan_id == null)

                    <div class="mb-4 text-red-500">
                        {{ __('You do not have an active membership plan. Please subscribe to a plan to access the portal features.') }}
                    </div>

                    @if (isset($member->planRequests) && $member->planRequests->where('status', 'pending')->count() > 0)
                    <div class="mb-4 text-yellow-500">
                        {{ __('Your plan request is pending approval.') }}
                    </div>
                    @elseif (isset($member->planRequests) && $member->planRequests->where('status', 'rejected')->count() > 0)
                    <div class="mb-4 text-red-500">
                        {{ __('Your previous plan request was rejected. Please submit a new request.') }}
                    </div>
                    @endif
                    @if($member->planRequests->where('status', 'pending')->count() == 0)

                    <form method="POST" action="{{ route('member.request-plan', $member->id) }}" class="mb-4">
                        @csrf
                        <div class="mb-4">
                            <label for="plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Select a Membership Plan') }}</label>
                            <select id="plan_id" name="plan_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ $plan->price }} for {{ $plan->duration }} days</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Request Plan') }}
                        </button>



                    </form>
                    @endif
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border-b border-t border-gray-200 dark:border-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Plan Name</th>
                                <th scope="col" class="px-6 py-3 font-medium">Duration</th>
                                <th scope="col" class="px-6 py-3 font-medium">Price</th>
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
                            </tr>
                            @empty
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('No membership plans available.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @else
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ __('Welcome to your member portal! Here you can access your workout plans, track your progress, and manage your account settings.') }}
                    </p>
                    <div class="pt-4  mb-4 text-yellow-500">
                        <div>
                            <strong>{{ __('Your current membership plan:') }} </strong>
                        </div>
                        <div>
                            {{ __('Plan Name:') }} <strong class=" mb-4 text-gray-100">{{ $member->membershipPlan->name }}</strong>
                        </div>
                        <div>
                            {{ __('Duration:') }} <strong class=" mb-4 text-gray-100">{{ $member->membershipPlan->duration }} days</strong>
                        </div>
                        <div>
                            {{ __('Price:') }} <strong class=" mb-4 text-gray-100">${{ $member->membershipPlan->price }}</strong>
                        </div>
                        <div>
                            {{ __('Start Date:') }} <strong class=" mb-4 text-gray-100">{{ $member->membership_start_date }}</strong>
                        </div>
                        <div>
                            {{ __('Plan End Date:') }} <strong class=" mb-4 text-gray-100">{{ Carbon\Carbon::parse($member->membership_start_date)->addDays($member->membershipPlan->duration)->format('Y-m-d') }}</strong>
                        </div>
                    </div>


                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
